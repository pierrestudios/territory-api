<?php

namespace App\Http\Controllers;


use App\User;
use Auth\AuthController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function signup(Request $request) {
		$credentials = $request->only('email', 'password');

	   	// return Response()->json(compact('credentials'));
	   	
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
	
		if ( ! $token = JWTAuth::attempt($credentials)) {
			return Response()->json(false, 401);
		}
	
	   return Response()->json(compact('token'));
	}
	
	public function restricted() {
		return ['data' => 'This has come from a dedicated API subdomain with restricted access.'];
		try {
	        JWTAuth::parseToken()->toUser();
		} catch (Exception $e) {
        	return Response()->json(['error' => $e->getMessage()], 500);
		}
		return ['data' => 'This has come from a dedicated API subdomain with restricted access.'];
   	}
   	
   	public function authUser(Request $request) {
	   	// return ['data' => $request->headers];
	   	// JWTAuth::parseToken()->toUser(); // 
	   	// $user = JWTAuth::parseToken()->authenticate();
	   	$token = $this->parseAuthHeader($request);
	   	$user = $token ? JWTAuth::toUser($token) : null;
	   	if ( empty($user) ) return Response()->json(['error' => 'get_token', 'message' => 'Must login'], 500);
	   	
	   	return Response()->json([
	   		'data' => [
	   			'email' => $user->email,
	   			'registered_at' => $user->created_at->toDateTimeString()
	   		]
		]);
   	}
   
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
}

