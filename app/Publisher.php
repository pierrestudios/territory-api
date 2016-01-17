<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Publisher extends Model  
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'type', 'user_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        // 'password', 'remember_token',
    ];
    
    public static $transformationData = [
		'publisherId' => 'id',
		'firstName' => 'first_name',
		'lastName' => 'last_name',
		'publisherType' => 'type',
		'territories' => 'territories'
	];
	
	/**
     * Get the territories for the publisher.
     */
    public function territories()
    {
        return $this->hasMany('App\Territory');
    }
    
    /**
     * Get the filters for the Publisher.
     */
    public static function getFilters($filters) {
	    // userId
	    if(array_key_exists('userId', $filters)) {
		    return ['user_id'=> $filters['userId']];
	    }
    }
     
     
}
