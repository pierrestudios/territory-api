<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
// use Illuminate\Contracts\Auth\PasswordBroker;
use Log;
use Mail;
use Validator;

class PasswordController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    // use ResetsPasswords;
	
	use ResetsPasswords {
      // getReset as postResetTrait;
    }

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest');
    }
	
	
	/**** Overwrite methods found in: /vendor/laravel/framework/src/Illuminate/Foundation/Auth/ResetsPasswords.php ***/
	

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @param  string|null  $lang
     * @return \Illuminate\Http\Response
     */
    public function getReset(Request $request, $lang = 'all', $token = null) {	    
		$this->lang = $lang;
		
		// dd($lang);
		
		// Match Language Views:
		/*
			$this->resetView = 'translation-'.$lang.'/reset';
			$this->linkRequestView = 'translation-'.$lang.'/passwords/email';	
		*/
		
		// New Universal Template
		$this->resetView = 'translation-all/reset';
		$this->linkRequestView = 'translation-all/passwords/email';
		
		if (is_null($token)) {
            return $this->getEmail();
        }

        $email = $request->input('email');

        if (property_exists($this, 'resetView')) {
            return view($this->resetView)->with(compact('token', 'email', 'lang'));
        }

        if (view()->exists('auth.passwords.reset')) {
            return view('auth.passwords.reset')->with(compact('token', 'email'));
        }

        return view('auth.reset')->with(compact('token', 'email'));
    }
    
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm() {
        if (property_exists($this, 'linkRequestView')) {
            return view($this->linkRequestView)->with('lang', $this->lang);
        }

        if (view()->exists('auth.passwords.email')) {
            return view('auth.passwords.email');
        }

        return view('auth.password');
    }
    
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request) {
	    // $this->validate($request, ['email' => 'required|email']);
	    $validator = Validator::make($request->all(), [
		    'email' => 'required|email'
		]);
	     
	    if ($validator->fails()) {
		    // dd($request);
		    
		    $request->flash();
            return back()->withErrors($validator)->withInput($request->all());
        }
        
        // var_dump($this->lang);
        
        // dd(['postEmail' => 1, 'request' => $request->all()]);

        /*
	#parameters: array:5 [▼
      "_token" => "Ow9RnyoH5Irt5M1J8yLNTcMjiOXwaZXFABYrYcvq"
      "token" => "Ow9RnyoH5Irt5M1J8yLNTcMjiOXwaZXFABYrYcvq"
      "email" => "test322@sitetest.com"
      "password" => "123456"
      "password_confirmation" => "123456"
    ]  
	    */    
	    
	    if(!empty($request->input('password'))) {
		    return $this->postReset($request); 
	    }
         
        $sendReset =  $this->sendResetLinkEmail($request);
        // dd($sendReset);
        
        return $sendReset;
    }
    
    /**
     * Get the response for after a failing password reset.
     *
     * @param  Request  $request
     * @param  string  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResetFailureResponse(Request $request, $response)
    {
	    // dd(['getResetFailureResponse()' => 1, 'request' => $request->all(), '$response' => $response]);
	    
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
    
    /**
     * Get the response for after the reset link could not be sent.
     *
     * @param  string  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getSendResetLinkEmailFailureResponse(Request $request, $response)
    {
	    // dd(['getSendResetLinkEmailFailureResponse()' => ['email' => trans($response)], 'request' => $request->all(), '$response' => $response]);
	    
        return redirect()->back()
        	->withInput($request->only('email'))
        	->withErrors(['email' => trans($response)]);
    }
    
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $this->validate(
            $request,
            $this->getResetValidationRules(),
            $this->getResetValidationMessages(),
            $this->getResetValidationCustomAttributes()
        );

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

		// dd($credentials);
		
		$password = $credentials['password'];
		$user = User::whereEmail($credentials['email'])->first();
		
		$this->resetPassword($user, $password);
		// dd($this->redirectPath());
		return redirect($this->redirectPath())->with('status', 'Successful');
		/*** Bypass all ***/
		
		
		
		
		
		
		
		
		
		/*
		
		// $tri = \Illuminate\Auth\Passwords\TokenRepositoryInterface::create($user);
        $broker = $this->getBroker(); // new \Illuminate\Auth\Passwords\PasswordBroker($tri); // 
        // dd([$user, $password, $broker, $credentials]);
        
		// \Illuminate\Auth\Passwords\TokenRepositoryInterface
		// DatabaseTokenRepository: \Illuminate\Auth\Passwords\DatabaseTokenRepository
		// exists() -> token: 165e7f8f8c63a2d12c4985dd4790dbca3b33b74e052419fccc59edbb0321a882
        $response = Password::broker()->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

		// dd(bcrypt('1@polisse'));
		// dd(['$response' => $response, 'Password::PASSWORD_RESET' => Password::PASSWORD_RESET, '$this->redirectPath()' => $this->redirectPath(), 'trans($response)' => trans($response)]);

        switch ($response) {
            case Password::PASSWORD_RESET:
                // return $this->getResetSuccessResponse($response);
                return redirect($this->redirectPath())->with('status', trans($response));

            default:
                return $this->getResetFailureResponse($request, $response);
        }
        */
    }
    
    // redirect after success
    public function redirectPath() {
	    return '/password-reset/en';
    }


 
	/**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request) {
        $this->validate($request, ['email' => 'required|email']);

        $broker = $this->getBroker(); // PasswordBroker \Illuminate\Auth\Passwords\PasswordBroker
		
        $response = Password::broker($broker)->sendResetLink(
            $request->only('email'), $this->resetEmailBuilder($this->getRequestLang($request))
        );
        
        // dd($response);
        
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return $this->getSendResetLinkEmailSuccessResponse($response);

            case Password::INVALID_USER:
            default:
                return $this->getSendResetLinkEmailFailureResponse($request, $response);
        }
    }
    
    /**
     * Get the Closure which is used to build the password reset email message.
     *
     * @return \Closure
     */
    protected function resetEmailBuilder($lang='en') {
        return function (Message $message, $user = null, $token = null) use ($lang) {
	        $request = Request();
	        $creds = $request->only(
            	'email', 'token', '_token', 'csrftoken'
			);
			// dd($creds);
			$email = $creds['email'];
			$token = $creds['_token']; // 'FakeToken000001'; // 
			$user = User::whereEmail($creds['email'])->first();
			
			// Log::info([$request->path()]);
			// dd(['siteUrl' => $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"]);
			
			// dd([$request]);
			$site_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
			$link = $site_url . '/password-reset/en/' . $token . '?email=' . urlencode($user->getEmailForPasswordReset());
			$email_message = 'Click here to reset your password: <a href="'. $link .'">' . $link . '</a>';
			 
			// dd(['resetEmailBuilder' => 1, 'lang' =>$lang, 'request' =>$request]);

			// dd(['APP_ADMIN_EMAIL' => env('APP_ADMIN_EMAIL', 'admin@territoryapi.com')]);
			
	    	$resetView = view('translation-all/emails/password')->with(compact('email_message'));
		    $message->getSwiftMessage()->setBody($body=$resetView->render(), 'text/html');
            $message->subject($this->getEmailSubject());
            $message->from(env('APP_ADMIN_EMAIL', 'admin@territoryapi.com'), env('MAIL_TO_NAME', 'Territory Api Admin'));
            $message->bcc(env('APP_ADMIN_EMAIL', 'admin@territoryapi.com'));
        };
    }
    
    public function getRequestLang($request=null) {
	    $uri = $request->path();
	    $uriSeg = explode('/', $uri);
	    return end($uriSeg);
    }

}
