<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Session;

class Section extends Model
{
    protected $guarded = [];

    public static function getSection(){
        return DB::table('sections')->where('company_id',session('company_id'))->get();
    }
}
