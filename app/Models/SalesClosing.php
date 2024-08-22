<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceProviderLedger;

class SalesClosing extends Model
{
	protected $table = "sales_closing";
	protected $primaryKey = 'closing_id';
    protected $guarded = [];
    public $timestamps = false;
}