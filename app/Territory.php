<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Territory extends Model  
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'publisher_id', 'assigned_date', 'number', 'location', 'boundaries'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public static $transformationData = [
		'territoryId' => 'id',
		'publisherId' => 'publisher_id',
		'date' => 'assigned_date', 
		'number' => 'number',
		'location' => 'location',
		'boundaries' => 'boundaries',
		'addresses' => 'addresses',
		'publisher' => 'publisher'
	];
	
	public static $intKeys = [
		'territoryId',
		'publisherId',
		'number'
	];
	
	/**
     * Get the addresses for the territory.
     */
    public function addresses()
    {
        return $this->hasMany('App\Address');
    }
    
    /**
     * Get the publisher assigned the territory.
     */
    public function publisher()
    {
        return $this->belongsTo('App\Publisher');
    }
     
}
