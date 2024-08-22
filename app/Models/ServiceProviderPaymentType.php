<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceProviderLedger;

class ServiceProviderPaymentType extends Model
{
    protected $table = "service_provider_payment_type";
    protected $guarded = [];
    public $timestamps = false;
}