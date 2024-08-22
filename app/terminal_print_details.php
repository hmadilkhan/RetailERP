<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class terminal_print_details extends Model
{
    //
    protected $fillable = ['terminal_id','header','footer','LAN','bluetooth','printer_name','image','cloud'];
    public $timestamps = false;
}