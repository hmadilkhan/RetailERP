<?php

namespace App\Services\QuickBooks;

use App\Models\QuickBookSetting;
use QuickBooksOnline\API\DataService\DataService;

abstract class QuickBooksService
{
    protected $dataService;
    protected $authService;

    public function __construct(QuickBooksAuthService $authService)
    {
        $this->authService = $authService;
        $config = config('quickbooks');

        $this->dataService = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' =>  $config['client_id'],
            'ClientSecret' =>  $config['client_secret'],
            'accessTokenKey' =>  $config['access_token'],
            'refreshTokenKey' =>  $config['refresh_token'],
            'QBORealmID' =>  $config['realm_id'],
            'baseUrl' =>  $config['base_url'],
        ]);
    }

    public function getAccessToken()
    {
        $settings = QuickBookSetting::first();
        if (!$settings) {
            return null;
        }

        if (now()->greaterThan($settings->qb_token_expires_at)) {
            return $this->authService->refreshAccessToken();
        }

        return $settings->qb_access_token;
    }

    // Generic find by ID function
    public function findById($entity, $id)
    {
        return $this->dataService->FindById($entity, $id);
    }

    // Generic delete function
    public function deleteEntity($entity, $id)
    {
        $item = $this->findById($entity, $id);
        if (!$item) {
            return false;
        }

        return $this->dataService->Delete($item);
    }
}
