<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Branch Availability: row = product is branch me band — App\Services\Inventory\BranchAvailabilityService. */
class ProductBranchAvailability extends Model
{
    protected $table = 'product_branch_availability';

    protected $guarded = [];
}
