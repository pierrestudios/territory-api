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
        'territory_id', 'order', 'name', 'phone', 'address'
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
		'order' => 'order',
		'address' => 'address',
		'phone' => 'phone'
	];
	
	public static $intKeys = [
		'territoryId',
		'addressId'
	];
	
	public static function getStreet($address = '') {
		if($address) {
			$address_ = explode(' ', $address);
			if ($address_[1]) return trim(str_replace($address_[0], '', $address));
		}
	}
     
}
