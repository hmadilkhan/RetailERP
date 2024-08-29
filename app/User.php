<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use Notifiable, Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'user_details';


    protected $fillable = [
        'fullname',
        'username',
        'password',
        'email',
        'contact',
        'country_id',
        'city_id',
        'address'
    ];

    protected $appends = ['role'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function company()
    {
        return $this->belongsTo(company::class, "company_id", "company_id");
    }

    public function branch()
    {
        return $this->belongsTo(branch::class, "branch_id", "branch_id");
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    public function getRoleAttribute()
    {
        return session("roleId");
    }

    public function canBeImpersonated()
    {
        // Example logic: Only allow non-admin users to be impersonated
        return $this->role !== 'admin';
    }
}
