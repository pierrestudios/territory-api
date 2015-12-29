<?php

namespace App\Http\Controllers;

use Auth;
use Gate;
use JWTAuth;
use App\User;
use App\Publisher;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function signup(Request $request) {
		$credentials = $request->only('email', 'password');
	   	
	   	if ( ! $credentials['email'] || ! $credentials['password']) {
		   	return Response()->json(['error' => 'User could not be created.', 'message' => 'User email and password required.'], 401);
		}
	   	
	   	try {
		   	$user = User::create($credentials);
	   	} catch (Exception $e) {
	   		return Response()->json(['error' => 'User could not be created.', 'message' => $e->getMessage()], 401);
	   	} catch (JWTException $e) {
	   		return Response()->json(['error' => 'could_not_create_token', 'message' => $e->getMessage()], 500);
        }
	
		$token = JWTAuth::fromUser($user);
		return Response()->json(compact('token'));
	}
	
	public function signin(Request $request) {
		$credentials = $request->only('email', 'password');
	
		if ( ! $credentials['email'] || ! $credentials['password']) {
		   	return Response()->json(['error' => 'User could not login.', 'message' => 'User email and password required.'], 401);
		}
		
		if ( ! $token = JWTAuth::attempt($credentials)) {
			return Response()->json(false, 401);
		}
	
	   return Response()->json(compact('token'));
	}
	
	
	// Sample method for restricted methods
	public function restricted(Request $request) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		return ['data' => 'This has come from a dedicated API subdomain with restricted access.'];
   	}
   	
	// Sample method for restricted user methods
   	public function authUser(Request $request) {
	   	if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
	   	$user = Auth::user();
	   	
	   	return Response()->json([
	   		'data' => [
	   			'email' => $user->email,
	   			'registered_at' => $user->created_at->toDateTimeString()
	   		]
		]);
   	}
   	
   	public function publishers(Request $request) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('view-publishers')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		return ['data' => 'view-publishers access.'];
   	}
   	
   	public function territories(Request $request) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('update-territories')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		return ['data' => 'update-territories access.'];
   	}
   	
   	public function addresses(Request $request) {
		if ( ! $this->hasAccess($request) ) {
			return Response()->json(['error' => 'Access denied.'], 500);
		}
		
		if (Gate::denies('update-addresses')) {
            return Response()->json(['error' => 'Method not allowed'], 403);
        }
		return ['data' => 'update-addresses access.'];
   	}

   	/*
	* hasAccess() Check if JWT token is valid
	* @param $request \Illuminate\Http\Request
	*/
   	protected function hasAccess($request) {
	   	try {
			$user = JWTAuth::toUser($this->parseAuthHeader($request));
		} catch (Exception $e) {
        	$error = $e->getMessage();
		}
		
		if ( empty($user) || ! empty($error) ) return false;
		
		// Auth::user() = NULL
		// The Gate will automatically return false for all abilities when there is not an authenticated user
		if ( empty(Auth::user()) ) Auth::loginUsingId($user->id); // simulate user login
		// return Response()->json(['data' => Gate::denies('view-publishers'), '$user' => Auth::user()]);
		return true;
   	}
   	/*
	* parseAuthHeader() Great technique from jeroenbourgois -> https://github.com/tymondesigns/jwt-auth/issues/106
	* @param $request \Illuminate\Http\Request
	*/   	
   	protected function parseAuthHeader(Request $request, $headerName = 'authorization', $method = 'bearer') {
	    $header = $request->headers->get($headerName);
	
	    if(is_null($header)) {
	      $headers = array_change_key_case(getallheaders(), CASE_LOWER);
	
	      if(array_key_exists($headerName, $headers)) {
	        $header = $headers[$headerName];
	      }
	    }
	
	    if (! starts_with(strtolower($header), $method)) {
	        return false;
	    }
	
	    return trim(str_ireplace($method, '', $header));
	}
	
	/*
	* transformCollection() Convert collection to API response data
	* @param $collection Result object
	* @param $type Result object type
	*/
	protected function transformCollection($collection, $type) {
		$transformedCollection = [];
		foreach($collection as $i => $entity) {
			$transformedCollection[$i] = $this->transform($entity->toArray(), $type);
		}
		return $transformedCollection;
	}
	
	/*
	* transform() Convert entity to API response data
	* @param $entity Result object
	* @param $type Result object type
	*/
	protected function transform($entity, $type) {
		$transformedData = [];
		if ($type == 'publisher') {
			foreach(Publisher::$transformationData as $k => $v) {
				$transformedData[$k] = $entity[$v];	
			}
			return $transformedData;
		}
	}
	
}

