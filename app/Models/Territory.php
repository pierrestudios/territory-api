<?php

namespace App\Models;

use App\Models\Publisher;
use App\Models\Coordinates;
use Illuminate\Database\Eloquent\Model;

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
        'publisher' => 'publisher',
        'records' => 'records'
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
        return $this->hasMany('App\Models\Address');
    }

    /**
     * Get the records for the territory.
     */
    public function records()
    {
        return $this->hasMany('App\Models\Record');
    }

    /**
     * Get the publisher assigned the territory.
     */
    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher');
    }

    /**
     * Get the filters for the territory.
     */
    public static function applyFilters($filters)
    {
        // userId
        if (array_key_exists('userId', $filters)) {
            $publisher = Publisher::where('user_id', $filters['userId'])->first();
            if (!empty($publisher['id'])) {
                return ['publisher_id' => $publisher['id']];
            } else {
                return ['id' => null];
            }
        }
    }


    public static function prepareMapData($territory)
    {
        $data = [];
        $buildingCoordinates = [];
        foreach ($territory['addresses'] as $i => $address) {
            if (empty((float)$address['lat']) || empty((float)$address['long'])) {
                if ($address['street']['is_apt_building']) {
                    if (empty($buildingCoordinates[$address['street']['id']])) {
                        $addressData = $address;
                        $addressData['street']['address_id'] = $addressData['id'];
                        $buildingCoordinates[$address['street']['id']] = Coordinates::getBuildingCoordinates($address['street'], $territory['city_state']);
                    }

                    $address['lat'] = $buildingCoordinates[$address['street']['id']]['lat'];
                    $address['long'] = $buildingCoordinates[$address['street']['id']]['long'];
                } else {
                    $address = Coordinates::getAddessCoordinates($address, $territory['city_state']);
                }
            }

            $data[] = (object)[
                'address' => ($address['street']['is_apt_building'] ? ($address['street']['street']) : ($address['address'] . ' ' . $address['street']['street'])),
                'name' => ($address['street']['is_apt_building'] ? 'Apartment' : ($address['name'] ? $address['name'] : "Home")),
                'lat' => $address['lat'],
                'long' => $address['long'],
                'id' => $address['id']
            ];
        }
        
        return $data;
    }
}
