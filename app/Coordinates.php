<?php

namespace App;

use Auth;
use App\Address;
use \Geocoder;
use Illuminate\Database\Eloquent\Model;

class Coordinates extends Model
{
    public static function getAddessCoordinates($address, $city) {
		$coordinates = self::getCoordinates($address['address'] . ' ' . $address['street']['street'], $city);
		$address['lat'] = $coordinates['lat'];
		$address['long'] = $coordinates['long'];
		return $address;
	}
	
	public static function getBuildingCoordinates($building, $city) {
		$coordinates = self::getCoordinates($building['street'], $city);
		$building['lat'] = $coordinates['lat'];
		$building['long'] = $coordinates['long'];
		return $building;
	}
	
	protected static function getCoordinates($address, $city) {
		// echo ' getCoordinates(): ' . $address . "\n\n";
		
		$coordinates = [
			'lat' => '',
			'long' => ''
		];
 
		// make Google API request
	   	$response = Geocoder::geocode('json', ["address" => $address . ', ' . $city]);

	   	if($response) {
		   	$response = json_decode($response);
		   	// dd($response);
		   	 
			if($response->status == "OK" && !empty($response->results[0]->geometry->location)) {
				$results = $response->results[0];
				if(count($response->results) > 1) {
					// Need to get the correct one
					
					$results = $response->results[0];
				}
				$coordinates['lat'] = $results->geometry->location->lat;
				$coordinates['long'] = $results->geometry->location->lng;
		   	}
	   	}

		// sleep(1);
			   	
		return $coordinates;
	}
	
	public static function updateAddress($address) {
		try {
			$addressObj = Address::findOrFail($address['id']);
			$data = $addressObj->update(['lat' => $address['lat'], 'long' => $address['long']]);
	    } catch (Exception $e) {
			$data = ['error' => 'Address not updated', 'message' => $e->getMessage()];
		}
		return $data;
		// dd(['$address' => $address, 'updated' => $data, 'addressObj' => $addressObj->toArray()]); exit;
	}
   	
   	public static function sortAddressByStreet($data) {
	   	if(!empty($data)) {
		   	$sortedAddress = [];
		   	foreach($data as $i => $address) {
			   	$sortedAddress[$address['street']['street']][] = $address;
		   	}
		   	return $sortedAddress;
	   	}
	   	return ['data' => $data];
   	}
}
