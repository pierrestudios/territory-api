<?php

namespace App;

use App\Publisher;
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
        'publisher_id', 'assigned_date', 'number', 'location', 'city_state', 'boundaries'
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
		'cityState' => 'city_state',
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
    
    /**
     * Get the filters for the territory.
     */
    public static function getFilters($filters) {
	    // userId
	    if(array_key_exists('userId', $filters)) {
		    $publisher = Publisher::where('user_id', $filters['userId'])->first();
		    if(!empty($publisher['id']))
		    	return ['publisher_id'=> $publisher['id']];
		    else return ['id'=> null];
	    }
    }
     
}
