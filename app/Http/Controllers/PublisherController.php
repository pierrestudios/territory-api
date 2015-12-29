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

class PublisherController extends ApiController
{    
   	public function index(Request $request) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('view-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		return ['data' => $this->transformCollection(Publisher::latest()->get(), 'publisher')];
   	}   	
}

