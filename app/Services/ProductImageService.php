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
     * which the image model cannot draw directly, so turn them into a plain English
     * visual description first. The text call costs a fraction of a cent.
     */
    protected function describe($product): string
    {
        $context = collect([
            'Product name: ' . $product->product_name,
            $product->brand_name ? 'Brand: ' . $product->brand_name : null,
            $product->department_name ? 'Department: ' . $product->department_name : null,
            $product->sub_depart_name ? 'Sub-department: ' . $product->sub_depart_name : null,
        ])->filter()->implode("\n");

        try {
            $response = $this->client->chat()->create([
                'model' => $this->textModel,
                'temperature' => 0.2,
                'max_tokens' => 80,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You describe retail products for a photographer. Given a product record from a '
                            . 'Pakistani retail catalogue (the name is often Urdu or transliterated), reply with one short '
                            . 'English noun phrase naming the physical object to photograph, including size and packaging '
                            . 'if stated. No brand names, no sentences, no quotes, max 15 words. '
                            . 'These are consumer goods sold over a shop counter, never machinery or equipment, unless the '
                            . 'department clearly says otherwise. Common terms: katta = large woven sack (about 50kg); '
                            . 'aata = wheat flour; chaki/chakki = stone-ground, so "aata chaki" is stone-ground wheat flour, '
                            . 'not a mill; daal = lentils (masoor = red, chana = split chickpea, maash = white urad, '
                            . 'malka masoor = whole red); gandum = wheat; chawal = rice; sela = parboiled; cheeni = sugar; '
                            . 'tel/tail = cooking oil; ghee = clarified butter; namak = salt; doodh = milk.',
                    ],
                    ['role' => 'user', 'content' => $context],
                ],
            ]);

            $text = trim($response->choices[0]->message->content ?? '');
        } catch (\Throwable $e) {
            $text = '';
        }

        return $text !== '' ? $text : (string) $product->product_name;
    }

    protected function prompt($product): string
    {
        return 'Professional e-commerce product photograph of: ' . $this->describe($product) . '. '
            . 'One single product centred on a plain light grey seamless background, soft even studio '
            . 'lighting, subtle contact shadow, sharp focus, realistic materials and proportions, square '
            . 'framing with generous margins. If the product is packed in a bag, sack or box, show a small '
            . 'neat pile of the actual contents in front of the pack so the goods are recognisable, and keep '
            . 'the pack a different tone from the background. Do not render any text, letters, numbers, '
            . 'logos, brand marks, labels, packaging copy, watermarks, hands or people.';
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
