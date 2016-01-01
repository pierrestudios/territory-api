<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use JWTAuth;
use App\User;
use App\Territory;
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
	        $territory = Territory::where('id', $territoryId)->with('addresses.notes')->get();
	        $data = !empty($territory[0]) ? $this->transform($territory[0]->toArray(), 'territory') : null;
        } catch (Exception $e) {
        	$data = ['error' => 'Territory not found', 'message' => $e->getMessage()];
		}
		return ['data' => $data];
   	}  	
}

