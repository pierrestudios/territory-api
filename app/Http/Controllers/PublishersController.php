<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use JWTAuth;
use App\User;
use App\Publisher;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class PublishersController extends ApiController
{    
   	public function index(Request $request) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('view-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		return ['data' => $this->transformCollection(Publisher::latest()->with('territories')->get(), 'publisher')];
   	} 
   	
   	public function view(Request $request, $publisherId = null) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('view-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		
        try {
	        $publisher = Publisher::where('id', $publisherId)->with('territories')->get();
	        $data = !empty($publisher[0]) ? $this->transform($publisher[0]->toArray(), 'publisher') : null;
        } catch (Exception $e) {
        	$data = ['error' => 'Publisher not found', 'message' => $e->getMessage()];
		}
		return ['data' => $data];
   	} 
   	
   	public function save(Request $request, $publisherId = null) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('update-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		
		if(!empty($publisherId)) {
    		$publisher = Publisher::findOrFail($publisherId);
	        try {
		        $publisher->update(["first_name" => $request->input('firstName'), "last_name" => $request->input('lastName')]);
		        $data = !empty($publisher) ? $this->transform($publisher->toArray(), 'publisher') : null;
	        } catch (Exception $e) {
	        	$data = ['error' => 'Publisher not found', 'message' => $e->getMessage()];
			}
		}
		return ['data' => $data];
   	}  	
}

