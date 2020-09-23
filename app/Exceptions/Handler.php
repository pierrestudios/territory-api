<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use JWTAuth;
use Log;
use Mail;
use Throwable;
use App\Models\User;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        TokenInvalidException::class,
        TokenExpiredException::class,
        JWTException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        // Note: "render" method overrides $this->renderable closures
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function report(Throwable $exception)
    {
        if (! in_array(get_class($exception), $this->dontReport)) {
            $this->sendEmail($exception);
        }

        return parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenExpiredException) {
            return $this->refreshToken($request, $exception);
        }

        if ($exception instanceof TokenInvalidException) {
            return response(
                [
                    'error' => 'Token is invalid',
                ], 401
            );
        }

        return response(
            [
                'error' => $exception->getMessage(),
            ], 500
        );
    }

    /**
     * Sends an email to the developer about the exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function sendEmail(Throwable $exception)
    {
        try {
            $content = $this->buildErrorMessage($exception);
            $subject = 'Territory API server error: ' . $exception->getMessage();
    
            Mail::send(
                'translation-all/emails/notice', compact('content', 'subject'), function ($message) use ($subject) {
                    $message->to(config('app.mailToEmail'), config('app.mailToName'));
                    $message->from(config('app.mailFromEmail'));
                    $message->subject($subject);
                }
            );
        } catch (Exception $ex) {
            Log::debug('sendEmail failed: ' . $ex->getMessage());
        }
    }

    /**
     * Sends an email to the developer about the exception.
     *
     * @param  \Throwable  $exception
     * @return string
     */
    protected function buildErrorMessage(Throwable $exception)
    {
        return 'Error: ' . $exception->getMessage() . " \n" .
            // Get API Endpoint
            '<h4>API Endpoint</h4>' .
            '<pre>' .
                $_SERVER['REQUEST_URI']
            . '</pre>' .

            // Get User Info 
            '<h4>User Info</h4>' .
            '<pre>' .
                json_encode(Auth::user() ?? '', JSON_PRETTY_PRINT)
            . '</pre>' .

            // Get User Agent 
            '<h4>User Agent</h4>' .
            '<pre>' .
                $_SERVER['HTTP_USER_AGENT']
            . '</pre>' .

            // Get User HTTP Request
            '<h4>User HTTP Request Data (POST)</h4>' .
            '<pre>' .
                json_encode($_POST ?? '', JSON_PRETTY_PRINT)
            . '</pre>' .

            // Get User HTTP Headers
            '<h4>User HTTP Headers</h4>' .
            '<pre>' .
                json_encode(getallheaders(), JSON_PRETTY_PRINT)
            . '</pre>' .

            // Print stack in JSON PRETTY PRINT
            '<h4>Exception Stack</h4>' .
            '<pre>' .
            json_encode(empty($exception->getTrace()) ? '' : array_map(function ($trace) {
                return [
                    'file' => $trace['file'] ?? '',
                    'line' => $trace['line'] ?? '',
                    'function' => $trace['function'] ?? '',
                    'class' => $trace['class'] ?? '',
                ];
            }, $exception->getTrace()), JSON_PRETTY_PRINT) ?? ''
            . '</pre>';
    }

    /**
     * Refresh the JWT
     *
     * @param \Illuminate\Http\Request $request 
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    protected function refreshToken(Request $request, Throwable $exception) {
        if (strpos($exception->getMessage(), "can no longer be refreshed") !== false) {
            return response(['error' => 'Token has expired and can no longer be refreshed.'], 401);
        }

        $refreshedTokenResponse = $this->tryToRefreshToken($request);
        if (!empty($refreshedTokenResponse)) {
            Log::debug('Refresh Token Response', ['refreshedToken' => $refreshedTokenResponse]);

            return $refreshedTokenResponse;
        }
    }

    /*
     * Try to refresh the JWT
     * 
     * @param $request \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
    */
    protected function tryToRefreshToken(Request $request)
    {
        try {     
            auth()->setToken(JWTAuth::getToken());
            $newToken = auth()->refresh();
            $user = JWTAuth::toUser($newToken);
 
            if (empty($user)) {
                return response(['error' => 'Token is invalid', 'data' => 'empty user'], 401);
            }

            return response(
                [
                    'data' => [
                        'email' => $user->email,
                        'userId' => $user->id,
                        'userType' => User::getTypeString($user->level),
                        'refreshedToken' => $newToken
                    ]
                ], 200
            );
        } catch (JWTException $e) {
            return response(['error' => 'Token is invalid', 'data' => 'empty user'], 401);
        }
    }
}
