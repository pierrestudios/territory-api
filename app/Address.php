<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'territory_id', 'street_id', 'inactive', 'name', 'phone', 'address', 'apt', 'lat', 'long'
    ];

    /**
     * The attributes to display is JSON response.
     *
     * @var array
     */
    public static $transformationData = [
        'addressId' => 'id',
        'territoryId' => 'territory_id',
        'inActive' => 'inactive',
        'name' => 'name',
        'address' => 'address',
        'apt' => 'apt',
        'lat' => 'lat',
        'long' => 'long',
        'phone' => 'phone',
        'street' => 'street',
        'streetId' => 'street_id',
        'notes' => 'notes'
    ];

    public static $intKeys = [
        'territoryId',
        'addressId',
        'inActive'
    ];

    /**
     * Get the territory for the address.
     */
    public function territory()
    {
        return $this->belongsTo('App\Territory');
    }

    /**
     * Get the street for the address.
     */
    public function street()
    {
        return $this->belongsTo('App\Street');
    }

    /**
     * Get the notes for the address.
     */
    public function notes()
    {
        return $this->hasMany('App\Note', 'entity_id', 'id');
    }

    public static function getStreet($address = '')
    {
        if ($address) {
            $address_ = explode(' ', $address);
            if (!empty($address_[1])) return trim(str_replace($address_[0], '', $address));
        }
    }
}
