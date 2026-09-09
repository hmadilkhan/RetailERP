<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Scale Inventory API
    |--------------------------------------------------------------------------
    |
    | Defaults for the weighing scale inventory feed
    | (GET /api/scale/inventory/{company_id}). The database has no per item
    | weight limits, so these defaults are sent unless the caller overrides them
    | with the min_grams / max_grams query parameters.
    |
    */

    'image_base_url' => env('SCALE_IMAGE_BASE_URL', 'https://retail.sabsoft.com.pk/assets/images/products/'),

    'default_min_grams' => (int) env('SCALE_DEFAULT_MIN_GRAMS', 50),

    'default_max_grams' => (int) env('SCALE_DEFAULT_MAX_GRAMS', 5000),

    // uom_id values that are sold by weight, used by the only_weighed=1 filter.
    'weighed_uom_ids' => [5, 10], // KG, Gram

];
