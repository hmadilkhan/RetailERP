<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class InventoryDepartmentSection extends Model
{
    protected $guarded = [];
    protected $table = "inventory_department_sections";

    public function inventoryDepartmentSection()
    {
        return $this->hasMany(InventoryDepartmentSection::class);
    }
}
