<?php

namespace App\Services\QuickBooks;

use QuickBooksOnline\API\DataService\DataService;

abstract class QuickBooksService
{
    protected $dataService;

    public function __construct()
    {
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
