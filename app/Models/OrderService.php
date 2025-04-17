<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;

    protected $table = "sales_receipts_services";
    protected $guarded = [];


    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class,"service_type_id","id");
    }
}
