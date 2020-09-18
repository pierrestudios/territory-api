<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Auth;
use Log;
use Mail;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
        //
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Emails.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        $this->sendEmail($exception);

        return parent::report($exception);
    }

    /**
     * Sends an email to the developer about the exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    protected function sendEmail(Throwable $exception)
    {

        Log::error( 'sendEmail: "' . $exception->getMessage() . '"');

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
            Log::error('sendEmail failed: ' . $ex->getMessage());
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
}
