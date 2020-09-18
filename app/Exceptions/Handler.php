<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        if ($this->shouldReport($exception)) {
            $this->sendEmail($exception); // sends an email
        }

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

        Log::error( 'sendEmail()' . $exception->getMessage());

        try {
            $content = $this->buildErrorMessage($exception);
            $subject = 'An exception occurred';
    
            Mail::send(
                'translation-all/emails/notice', compact('content', 'subject'), function ($message) use ($subject) {
                    $message->to(config('app.mailToEmail'), config('app.mailToName'));
                    $message->from(config('app.mailFromEmail'));
                    $message->subject($subject);
                }
            );
        } catch (Exception $ex) {
            Log::error( $ex->getMessage());
        }
    }

    /**
     * Build Exception message for email 
     *
     * @param  \Throwable  $exception
     * @return string
     */
    protected function buildErrorMessage(Throwable $exception)
    {
        return $exception->getMessage() . " \n" .
            json_encode([
                'file' => $exception->getTrace()[0]['file'],
                'line' => $exception->getTrace()[0]['line'],
                'function' => $exception->getTrace()[0]['function'],
                'class' => $exception->getTrace()[0]['class'],
            ]);
    }
}
