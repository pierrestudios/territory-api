<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'publisher_id', 'territory_id', 'activity_date', 'activity_type' 
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public static $transformationData = [
		'recordId' => 'id',
		'userId' => 'user_id',
		'territoryId' => 'territory_id',
		'publisherId' => 'publisher_id',
		'activityType' => 'activity_type',
		'date' => 'activity_date', 
		'user' => 'user',
		'publisher' => 'publisher'
	];
	
	public static $intKeys = [
		'userId',
		'territoryId',
		'publisherId',
		'number'
	];
	
	/**
     * Record territory check-in
     */
    public static function checkIn($territoryId, $publisherId, $date) {
        self::create([
	        'user_id' => Auth::user()->id,
	        'publisher_id' => $publisherId,
	        'territory_id' => $territoryId,
	        'activity_date' => $date,
	        'activity_type' => 'checkin'
        ]);
    }
		
	/**
     * Record territory check-out
     */
    public static function checkOut($territoryId, $publisherId, $date) {
        self::create([
	        'user_id' => Auth::user()->id,
	        'publisher_id' => $publisherId,
	        'territory_id' => $territoryId,
	        'activity_date' => $date,
	        'activity_type' => 'checkout'
        ]);
    }
	    
    /**
     * Get the user for the Record.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    /**
     * Get the territory for the Record.
     */
    public function territory()
    {
        return $this->belongsTo('App\Territory');
    }
    
    /**
     * Get the publisher for the Record.
     */
    public function publisher()
    {
        return $this->belongsTo('App\Publisher');
    }
}
