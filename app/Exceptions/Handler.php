<?php

namespace App\Exceptions;

use Exception;
use JWTAuth;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
	    if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
		    return response(['Token is invalid'], 401);
		} if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
		    // return response(['Token has expired'], 401);
		    
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
  
        return response(['error' => $e->getMessage()], 500); // parent::render($request, $e);
    }
}
