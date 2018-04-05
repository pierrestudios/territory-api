<?php

namespace App\Exceptions;

use Exception;
use JWTAuth;
use Auth;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
	    // catch JWT Invalid Token Exceptions
	    if ($e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
		    return response(['Token is invalid'], 401);
		} else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
		    return response(['Token is invalid'], 401);
		} else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
		    return response(['Token is invalid'], 401);
		} else if ($e instanceof \Illuminate\Database\QueryException) {
		    return response(['error' => 'An error occured', 'data' => 'QueryException'], 401);
		} else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
		    // return response(['Token has expired'], 401);
		    $errorMessage = $e->getMessage();
		    // Log::info('TokenExpiredException ' . $errorMessage);
		    if(strpos($errorMessage, "can no longer be refreshed") !== false) {
			    return response(['error' => 'Token has expired and can no longer be refreshed.'], 401);
		    }
		    
            $header = $request->headers->get('authorization');
		    if(is_null($header)) {
		      $headers = array_change_key_case(getallheaders(), CASE_LOWER);
		      if(array_key_exists('authorization', $headers)) {
		        $header = $headers['authorization'];
		      }
		    } 
		    if(!empty($header)) {
			    $token = trim(str_ireplace('bearer', '', $header));
	            $newToken = JWTAuth::refresh($token);  
	            $user = JWTAuth::toUser($newToken);

				if(empty($user))
					return response(['Token is invalid'], 401);

				return [
					'data' => [
		   				'email' => $user->email,
		   				'userId' => $user->id,
		   				'userType' => User::getTypeString($user->level),
		   				'refreshedToken' => $newToken
		   			]
		   		];
	   		}
              
		}
  
		// 202 Accepted
		// The request has been accepted for processing, but the processing has not been completed. The request might or might not be eventually acted upon, and may be disallowed when processing occurs.
        return response([
			'error' => $e->getMessage(), 
			// 'ExceptionType' => get_class($e),
			// 'user' => Auth::user(),
			// 'token' => $request->bearerToken()
			], 500); // parent::render($request, $e);
    }
}
