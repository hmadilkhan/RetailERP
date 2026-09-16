<?php

namespace App\Services;

use App\Traits\MediaTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenAI;

class ProductImageService
{
    use MediaTrait;

    const STORAGE_PATH = "images/products/";

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
     * Products of the current company that still have no image.
     */
    public function missingImageIds(int $limit = 5000): array
    {
        return DB::table('inventory_general')
            ->where('company_id', session('company_id'))
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereNull('image')->orWhere('image', '');
            })
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Generate and store an image for one product. Returns the saved file name.
     */
    public function generate(int $productId): string
    {
        $product = $this->product($productId);
        if (!$product) {
            throw new \RuntimeException('Product not found.');
        }

        $binary = $this->render($this->prompt($product));

        return $this->store($product, $binary);
    }

    protected function product(int $id)
    {
        return DB::table('inventory_general as invent')
            ->leftJoin('inventory_department as dept', 'dept.department_id', '=', 'invent.department_id')
            ->leftJoin('inventory_sub_department as sdept', 'sdept.sub_department_id', '=', 'invent.sub_department_id')
            ->leftJoin('brands', 'brands.id', '=', 'invent.brand_id')
            ->where('invent.id', $id)
            ->where('invent.company_id', session('company_id'))
            ->select(
                'invent.id',
                'invent.product_name',
                'invent.image',
                'invent.short_description',
                'dept.department_name',
                'sdept.sub_depart_name',
                'brands.name as brand_name'
            )
            ->first();
    }

    /**
     * Product names here are often Urdu or local shorthand ("بول چکی 50کلو", "Cutting Khatt"),
     * which the image model cannot draw directly, so turn them into a plain English subject
     * first. Whether the item is sold packaged comes back with it, because that decides how
     * the shot is framed. The text call costs a fraction of a cent.
     */
    protected function describe($product): array
    {
        $fallback = ['subject' => (string) $product->product_name, 'packaged' => false, 'served' => false];

        $context = collect([
            'Product name: ' . $product->product_name,
            $product->brand_name ? 'Brand: ' . $product->brand_name : null,
            $product->department_name ? 'Department: ' . $product->department_name : null,
            $product->sub_depart_name ? 'Sub-department: ' . $product->sub_depart_name : null,
        ])->filter()->implode("
");

        try {
            $response = $this->client->chat()->create([
                'model' => $this->textModel,
                'temperature' => 0.2,
                'max_tokens' => 120,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You brief a photographer on retail products from a Pakistani shop catalogue '
                            . '(names are often Urdu or transliterated). Reply with JSON only: '
                            . '{"subject": "<short English noun phrase, max 15 words>", "packaged": <true|false>, '
                            . '"served": <true|false>}. '
                            . "\n"
                            . 'The "subject" briefs a photographer who cannot read Urdu, so it must be plain English '
                            . 'describing what the thing physically looks like. Never hand the product name back '
                            . 'verbatim and never leave an Urdu or transliterated word in it: translate every such '
                            . 'word, using the terms listed below or your own knowledge. '
                            . "\n"
                            . 'The product name is the truth about what the item is. The department is only a shelf '
                            . 'section and is often broader than the item, so use it to disambiguate, never to override '
                            . 'the name: "Pineapple" in an "ICE CREAM SHAKES" department is still a fresh pineapple, not '
                            . 'a milkshake. Never invent preparation, flavouring or packaging the name does not state. '
                            . "\n"
                            . 'What the name DOES state, however, must be shown. When the name itself carries a '
                            . 'prepared or ready-to-serve form (chai, tea, coffee, qahwa, lassi, shake, juice, '
                            . 'sharbat, soup, biryani, karahi, sandwich, burger, roll, cake), that prepared item is '
                            . 'the product, and the other words in the name are its flavouring or ingredient, not '
                            . 'the product: "Gur Chai" is a served cup of tea brewed with jaggery, never a block of '
                            . 'jaggery; "Badam Milk" is almond-flavoured milk, not almonds; "Pineapple Shake" is a '
                            . 'milkshake, not a pineapple. Show such items served and ready to drink or eat, in the '
                            . 'cup, glass, plate or bowl they are served in. '
                            . "\n"
                            . 'Set "packaged" true only when the item is genuinely sold sealed in a sack, bag, box, '
                            . 'bottle, tin or carton (flour, sugar, rice, lentils, oil, biscuits). Set it false for '
                            . 'anything sold loose or bare: fresh fruit and vegetables, meat, bakery items, prepared '
                            . 'food and drinks, garments, footwear, electronics, hardware, crockery. '
                            . "\n"
                            . 'Set "served" true only when the item is sold ready to drink or eat and so has to be '
                            . 'shown in a cup, glass, plate or bowl: chai and other hot drinks, shakes, juices, lassi, '
                            . 'soups, cooked dishes and rice. Set it false for anything sold as goods to take away, '
                            . 'including bread and bakery items, raw meat, and loose tea leaves. Never set both '
                            . '"served" and "packaged" true. '
                            . "\n"
                            . 'No brand names in the subject. Include size or weight when the name states it. '
                            . "\n"
                            . 'Common terms: katta = large woven sack (about 50kg); aata = wheat flour; chaki/chakki = '
                            . 'stone-ground, so "aata chaki" is stone-ground wheat flour, not a mill; daal = lentils '
                            . '(masoor = red, chana = split chickpea, maash = white urad, malka masoor = whole red); '
                            . 'gandum = wheat; chawal = rice; sela = parboiled; cheeni = sugar; tel/tail = cooking oil; '
                            . 'ghee = clarified butter; namak = salt; doodh = milk; gur = jaggery (raw cane sugar, '
                            . 'sold as a solid block); chai = tea as a brewed drink; qahwa = green tea; doodh patti = '
                            . 'strong milk tea; lassi = yoghurt drink; sharbat = sweet cordial drink.',
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

        $served = (bool) ($parsed['served'] ?? false);

        return [
            'subject'  => (string) $parsed['subject'],
            // a served drink is never also a sealed pack, and the framings contradict each other
            'packaged' => $served ? false : (bool) ($parsed['packaged'] ?? false),
            'served'   => $served,
        ];
    }

    protected function prompt($product): string
    {
        $described = $this->describe($product);

        // Pale goods vanish on a white ground, and a sealed sack of anything looks like every
        // other sack, so packaged items get a light grey ground and a sample of the contents.
        // A served drink or dish needs its cup or plate, and "bare product" would strip that
        // away and leave loose tea leaves. Everything else is shot bare, or the model wraps
        // fruit and shirts in packaging.
        if ($described['served']) {
            $framing = 'Show the item already prepared and served, ready to drink or eat, in the plain '
                . 'cup, mug, glass, plate or bowl it is normally served in. No packaging, no wrapper, and '
                . 'no raw ingredients beside it. ';
        } elseif ($described['packaged']) {
            $framing = 'Show the closed pack with a small neat pile of the actual contents placed to one side in '
                . 'front of it, fully visible and never hidden behind the pack. Use a plain natural kraft '
                . 'or woven sack in a tone that contrasts with both the background and the contents. ';
        } else {
            $framing = 'Show the bare product itself with no packaging, no wrapper, no box and no bag of any kind. ';
        }

        return 'Professional e-commerce product photograph of: ' . $described['subject'] . '. '
            . $framing
            . 'One single product centred on a plain light grey seamless background, soft even studio '
            . 'lighting, subtle contact shadow, sharp focus, realistic materials and proportions, square '
            . 'framing with generous margins. Do not render any text, letters, numbers, logos, brand marks, '
            . 'labels, packaging copy, watermarks, hands or people.';
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
     * identical to a manually uploaded product image.
     */
    protected function store($product, string $binary): string
    {
        $temp = tempnam(sys_get_temp_dir(), 'genimg');
        file_put_contents($temp, $binary);

        try {
            $file = new UploadedFile(
                $temp,
                $product->id . '-' . Str::random(6) . '.png',
                'image/png',
                null,
                true
            );

            $saved = $this->uploads($file, self::STORAGE_PATH, $product->image ?? '', [
                'width'  => 400,
                'height' => 400,
            ]);
        } finally {
            @unlink($temp);
        }

        DB::table('inventory_general')->where('id', $product->id)->update([
            'image' => $saved['fileName'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $saved['fileName'];
    }
}
