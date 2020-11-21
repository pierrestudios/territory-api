<?php

namespace App\Models;

use Auth;
use App\Models\Address;
use \Geocoder;
use Illuminate\Database\Eloquent\Model;

class Coordinates extends Model
{
    public static function getAddessCoordinates($address, $city)
    {
        $coordinates = self::getCoordinates($address['address'] . ' ' . $address['street']['street'], $city);

        // Note: Update the address if the coordinates is updated
        if ($address['lat'] != $coordinates['lat'] || $address['long'] != $coordinates['long']) {
            $address['lat'] = $coordinates['lat'];
            $address['long'] = $coordinates['long'];
            self::updateAddress($address);
        }

        return $address;
    }

    public static function getBuildingCoordinates($building, $city)
    {
        $coordinates = self::getCoordinates($building['street'], $city);
        $building['lat'] = $coordinates['lat'];
        $building['long'] = $coordinates['long'];

        return $building;
    }

    protected static function getCoordinates($address, $city)
    {
        $coordinates = [
            'lat' => 0.0,
            'long' => 0.0
        ];

        dd(["address" => $address . ', ' . $city ?? config('app.APP_CITY_STATE')]);

        $response = Geocoder::geocode('json', ["address" => $address . ', ' . $city ?? config('app.APP_CITY_STATE')]);

        if ($response) {
            $response = json_decode($response);

            if ($response->status == "OK" && !empty($response->results[0]->geometry->location)) {
                $results = $response->results[0];
                if (count($response->results) > 1) {
                    // Note: in case of multiple addresses, assume [0] is the correct one
                    $results = $response->results[0];
                }
                $coordinates['lat'] = $results->geometry->location->lat;
                $coordinates['long'] = $results->geometry->location->lng;
            }
        }

        return $coordinates;
    }

    public static function updateAddress($address)
    {
        try {
            $addressObj = Address::findOrFail($address['id']);
            $data = $addressObj->update(['lat' => $address['lat'], 'long' => $address['long']]);
        } catch (Exception $e) {
            $data = ['error' => 'Address not updated', 'message' => $e->getMessage()];
        }

        return $data;
    }

    public static function sortAddressByStreet($data)
    {
        if (!empty($data)) {
            $sortedAddress = [];
            foreach ($data as $i => $address) {
                $sortedAddress[$address['street']['street']][] = $address;
            }
            return $sortedAddress;
        }

        return ['data' => $data];
    }
}
