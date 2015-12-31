<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Address extends Model  
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'territory_id', 'name', 'phone', 'address'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public static $transformationData = [
		'addressId' => 'id',
		'territoryId' => 'territory_id',
		'name' => 'name',
		'address' => 'address',
		'phone' => 'phone'
	];
	
	public static $intKeys = [
		'territoryId',
		'addressId'
	];
     
}
