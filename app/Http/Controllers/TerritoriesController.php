<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use JWTAuth;
use App\User;
use App\Territory;
use App\Address;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class TerritoriesController extends ApiController
{    
   	public function index(Request $request) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		return ['data' => $this->transformCollection(Territory::latest()->get(), 'territory')];
   	} 
   	
   	public function view(Request $request, $territoryId = null) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		try {
	        $territory = Territory::where('id', $territoryId)->with(['publisher', 'addresses.notes' => function ($query) {
			    $query->orderBy('date', 'desc');
			}])->get();
	        $data = !empty($territory[0]) ? $this->transform($territory[0]->toArray(), 'territory') : null;
        } catch (Exception $e) {
        	$data = ['error' => 'Territory not found', 'message' => $e->getMessage()];
		}
		return ['data' => $data];
   	}  	
   	
   	public function save(Request $request, $territoryId = null) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('update-territories')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		
		if(!empty($territoryId)) {
    		$territory = Territory::findOrFail($territoryId);
	        try {
		        // dd($this->unTransform($request->all(), 'territory'));
		        $data = $territory->update($this->unTransform($request->all(), 'territory'));
	        } catch (Exception $e) {
	        	$data = ['error' => 'Territory not updated', 'message' => $e->getMessage()];
			}
		}
		return ['data' => $data];
   	} 
   	
   	public function saveAddress(Request $request, $territoryId = null, $addressId = null) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('update-addresses')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
        
        if(empty($territoryId)) {
            return ['error' => 'Territory not found', 'message' => 'Territory not found'];
        }
		
		if(!empty($addressId)) {
    		$address = Address::findOrFail($addressId);
	        try {
		        // dd($this->unTransform($request->all(), 'address'));
		        $data = $address->update($this->unTransform($request->all(), 'address'));
	        } catch (Exception $e) {
	        	$data = ['error' => 'Address not updated', 'message' => $e->getMessage()];
			}
		} else {
			if (Gate::denies('create-addresses')) {
	            return Response()->json(['error' => 'Method not allowed'], 403);
	        }
	        // dd($this->unTransform($request->all(), 'address'));
	        try {
	        	$transformedData = $this->unTransform($request->all(), 'address');
	        	$territory = Territory::findOrFail($territoryId);
				$address = !empty($territory) ? $territory->addresses()->create($transformedData) : null;
				if($address && !empty($transformedData['notes'])) 
					$note = $address->notes()->create($transformedData['notes'][0]);
				$data = $address;
	        } catch (Exception $e) {
	        	$data = ['error' => 'Address not updated', 'message' => $e->getMessage()];
			}
		}
		return ['data' => $data];
   	}
   	
   	public function removeAddress(Request $request, $addressId = null) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('delete-addresses')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
        
        if(empty($addressId)) {
            return ['error' => 'Address not found', 'message' => 'Address not found'];
        }
		
		$address = Address::findOrFail($addressId);
        try {
	        // dd($this->unTransform($request->all(), 'address'));
	        $data = $address->delete();
        } catch (Exception $e) {
        	$data = ['error' => 'Address not updated', 'message' => $e->getMessage()];
		}
		
   	}
}

