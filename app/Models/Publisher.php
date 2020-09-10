<?php

namespace App\Models;

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
 
    public function territories()
    {
        return $this->hasMany('App\Territory');
    }

    /**
     * Apply filters for Publisher search.
     * 
     * @param array $filters 
     * 
     * @return array
     */
    public static function applyFilters($filters)
    {
        if (array_key_exists('userId', $filters)) {
            return ['user_id' => $filters['userId']];
        }
    }
}
