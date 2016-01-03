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
		'phone' => 'phone',
		'notes' => 'notes'
	];
	
	public static $intKeys = [
		'territoryId',
		'addressId'
	];
	
	/**
     * Get the notes for the address.
     */
    public function notes()
    {
        return $this->hasMany('App\Note', 'entity_id', 'id');
    }
    
	public static function getStreet($address = '') {
		if($address) {
			$address_ = explode(' ', $address);
			if (!empty($address_[1])) return trim(str_replace($address_[0], '', $address));
		}
	}
     
}
