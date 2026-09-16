<?php

namespace App\Services;

use App\Traits\MediaTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenAI;

/**
 * Same idea as ProductImageService, but a department is a shelf section rather than a
 * single item, so the shot is a small group of representative goods instead of one
 * product. Images land in images/department/ next to the manually uploaded ones.
 */
class DepartmentImageService
{
    use MediaTrait;

    const STORAGE_PATH = "images/department/";

    protected $client;
    protected string $imageModel;
    protected string $size;
    protected string $quality;
    protected string $textModel;

    public function __construct()
    {
        $key = config('services.openai.key');
        if (empty($key)) {
            throw new \RuntimeException('OpenAI API key is not configured.');
        }

        $this->client     = OpenAI::client($key);
        $this->imageModel = config('services.openai.image_model', 'gpt-image-1');
        $this->size       = config('services.openai.image_size', '1024x1024');
        $this->quality    = config('services.openai.image_quality', 'low');
        $this->textModel  = config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * Active departments of the current company that still have no image.
     */
    public function missingImageIds(int $limit = 2000): array
    {
        return DB::table('inventory_department')
            ->where('company_id', session('company_id'))
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('image')->orWhere('image', '');
            })
            ->orderBy('department_id')
            ->limit($limit)
            ->pluck('department_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Generate and store an image for one department. Returns the saved file name.
     */
    public function generate(int $departmentId): string
    {
        $department = $this->department($departmentId);
        if (!$department) {
            throw new \RuntimeException('Department not found.');
        }

        $binary = $this->render($this->prompt($department));

        return $this->store($department, $binary);
    }

    protected function department(int $id)
    {
        return DB::table('inventory_department')
            ->where('department_id', $id)
            ->where('company_id', session('company_id'))
            ->select(
                'department_id',
                'department_name',
                'website_department_name',
                'code',
                'image',
                'description'
            )
            ->first();
    }

    /**
     * Its own sub-departments say far more about what a department actually holds than the
     * name does ("GROCERY" alone could be anything), so they go into the brief. A couple of
     * real product names are pulled in for the same reason, capped so the context stays cheap.
     */
    protected function children($department): array
    {
        $subDepartments = DB::table('inventory_sub_department')
            ->where('department_id', $department->department_id)
            ->orderBy('sub_department_id')
            ->limit(8)
            ->pluck('sub_depart_name')
            ->filter()
            ->all();

        $products = DB::table('inventory_general')
            ->where('company_id', session('company_id'))
            ->where('department_id', $department->department_id)
            ->where('status', 1)
            ->orderBy('id')
            ->limit(8)
            ->pluck('product_name')
            ->filter()
            ->all();

        return [$subDepartments, $products];
    }

    /**
     * Department names are often Urdu or local shorthand and the image model cannot draw them
     * directly, so turn the name plus its sub-departments and stock into a plain English
     * category subject and a short list of goods to show. The text call costs a fraction of a cent.
     */
    protected function describe($department): array
    {
        $fallback = [
            'subject' => (string) ($department->website_department_name ?: $department->department_name),
            'items'   => [],
        ];

        [$subDepartments, $products] = $this->children($department);

        $context = collect([
            'Department name: ' . $department->department_name,
            $department->website_department_name ? 'Website name: ' . $department->website_department_name : null,
            $department->description ? 'Description: ' . Str::limit(strip_tags($department->description), 200) : null,
            $subDepartments ? 'Sub-departments: ' . implode(', ', $subDepartments) : null,
            $products ? 'Example products: ' . implode(', ', $products) : null,
        ])->filter()->implode("\n");

        try {
            $response = $this->client->chat()->create([
                'model' => $this->textModel,
                'temperature' => 0.2,
                'max_tokens' => 160,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You brief a photographer on catalogue category tiles for a Pakistani retail shop '
                            . '(department names are often Urdu or transliterated). Reply with JSON only: '
                            . '{"subject": "<short English noun phrase naming the category, max 10 words>", '
                            . '"items": ["<2 to 4 specific everyday goods to show>"]}. '
                            . "\n"
                            . 'The department name is the truth about the category. Sub-departments, the description '
                            . 'and the example products tell you what it actually holds, so use them to pick the items, '
                            . 'never to rename the category. If they disagree with the name, trust the name. '
                            . "\n"
                            . 'Items must be concrete, drawable goods that genuinely belong to the category: for '
                            . '"Bakery" use bread loaf, croissant, cake slice, not "bakery products". Prefer items '
                            . 'that appear in the example products when there are any. Keep them visually different '
                            . 'from each other so the tile reads at a glance. '
                            . "\n"
                            . 'No brand names anywhere. For a service or non-stock section with nothing to show '
                            . '(for example "General", "Miscellaneous", "Services"), return an empty items array. '
                            . "\n"
                            . 'Common terms: kiryana/kirana = grocery staples; aata = wheat flour; chaki/chakki = '
                            . 'stone-ground; daal = lentils; chawal = rice; cheeni = sugar; tel/tail = cooking oil; '
                            . 'ghee = clarified butter; namak = salt; doodh = milk; sabzi = vegetables; phal = fruit; '
                            . 'gosht = meat; murghi = chicken; gur = jaggery; chai = tea as a brewed drink; '
                            . 'bekri/bakery = bakery; mashroobat = beverages; '
                            . 'crockery = tableware; kapray = garments; joota = footwear.',
                    ],
                    ['role' => 'user', 'content' => $context],
                ],
            ]);

            $parsed = json_decode(trim($response->choices[0]->message->content ?? ''), true);
        } catch (\Throwable $e) {
            $parsed = null;
        }

        if (!is_array($parsed) || empty($parsed['subject'])) {
            return $fallback;
        }

        return [
            'subject' => (string) $parsed['subject'],
            'items'   => collect($parsed['items'] ?? [])
                ->filter(fn ($item) => is_string($item) && trim($item) !== '')
                ->map(fn ($item) => trim($item))
                ->take(4)
                ->values()
                ->all(),
        ];
    }

    protected function prompt($department): string
    {
        $described = $this->describe($department);

        // With nothing concrete to show the model invents signage and shelf labels, so an
        // empty item list falls back to a single symbolic object rather than a shop scene.
        $framing = $described['items']
            ? 'Show a small tidy arrangement of ' . implode(', ', $described['items'])
                . ', all fully visible and none hidden behind another, grouped close together in the centre. '
            : 'Show one simple symbolic object that represents this category, centred on its own. ';

        return 'Professional e-commerce category tile photograph representing the shop section: '
            . $described['subject'] . '. '
            . $framing
            . 'Plain light grey seamless background, soft even studio lighting, subtle contact shadows, sharp '
            . 'focus, realistic materials and proportions, square framing with generous margins. No shelves, '
            . 'no aisles, no shop interior, no signage. Do not render any text, letters, numbers, logos, brand '
            . 'marks, labels, packaging copy, watermarks, hands or people.';
    }

    protected function render(string $prompt): string
    {
        $response = $this->client->images()->create([
            'model' => $this->imageModel,
            'prompt' => $prompt,
            'n' => 1,
            'size' => $this->size,
            'quality' => $this->quality,
        ]);

        $payload = $response->data[0] ?? null;
        $encoded = $payload->b64_json ?? '';

        if ($encoded === '') {
            throw new \RuntimeException('Image model returned no image data.');
        }

        $binary = base64_decode($encoded, true);
        if ($binary === false || $binary === '') {
            throw new \RuntimeException('Image model returned unreadable image data.');
        }

        return $binary;
    }

    /**
     * Saved through MediaTrait::uploads() so naming, resizing and disk layout stay
     * identical to a manually uploaded department image.
     */
    protected function store($department, string $binary): string
    {
        $temp = tempnam(sys_get_temp_dir(), 'genimg');
        file_put_contents($temp, $binary);

        try {
            $file = new UploadedFile(
                $temp,
                $department->department_id . '-' . Str::random(6) . '.png',
                'image/png',
                null,
                true
            );

            $saved = $this->uploads($file, self::STORAGE_PATH, $department->image ?? '', [
                'width'  => 400,
                'height' => 400,
            ]);
        } finally {
            @unlink($temp);
        }

        DB::table('inventory_department')
            ->where('department_id', $department->department_id)
            ->update([
                'image' => $saved['fileName'],
                'date'  => date('Y-m-d'),
                'time'  => date('H:i:s'),
            ]);

        return $saved['fileName'];
    }
}
