<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
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

		// New Universal Template
		$this->resetView = 'translation-all/reset';
		$this->linkRequestView = 'translation-all/passwords/email';

		if (is_null($token)) {
			// return $this->getEmail();
			return $this->showLinkRequestForm();
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
	public function postEmail(Request $request, $lang = 'en') {
		// $this->validate($request, ['email' => 'required|email']);
		$validator = Validator::make($request->all(), [
			'email' => 'required|email'
		]);

		if ($validator->fails()) {
			// dd($request);

			$request->flash();
			return back()->withErrors($validator)->withInput($request->all());
		}

		if ($request->has('password')) {
			return $this->postReset($request);
		}

		$email = $request->input('email');
		$token = $this->getResetToken($request);
		$user = User::whereEmail($email)->first();
		// dd([$user]);
		if (!$user) {
			return $this->getSendResetLinkEmailFailureResponse($request, $response);
		}

		$response = $this->sendMailMessage($user, $lang, $token);

		switch ($response) {
			case Password::RESET_LINK_SENT:
				return $this->getSendResetLinkEmailSuccessResponse($response);

			case Password::INVALID_USER:
			default:
				return $this->getSendResetLinkEmailFailureResponse($request, $response);
		}
	}

	/**
	 * Send a reset link to the given user (for Api "password-retrieve" route)
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postEmailApi(Request $request, $lang = 'en') {

		$validator = Validator::make($request->all() , ['email' => 'required|email']);

		if ($validator->fails()) {
			return Response()->json(['error' => 'Email is required.'], 401);
		}

		$email = $request->input('email');
		$token = $this->getResetToken($request);
		$user = User::whereEmail($email)->first();
		// dd([$user]);
		if (!$user) {
			return Response()->json(['error' => 'User with email, "' . $email . '" could not be found.'], 401);
		}



		$response = $this->sendMailMessage($user, $lang, $token);

		switch ($response) {
			case Password::RESET_LINK_SENT:
				return Response()->json(['success' => true, 'data' => ['message' => 'An email has been sent to your e-mail, "' . $email . '" for password reset.']]);

			case Password::INVALID_USER:
			default:
				return Response()->json(['error' => 'Password reset has failed. Please try again.'], 401);
		}

	}

	/**
	 * Reset the given user's password.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function reset(Request $request) {

		$credentials = $request->only('email', 'password');

		$password = $credentials['password'];
		$user = User::whereEmail($credentials['email'])->first();

		$this->resetPassword($user, $password);
		// dd($this->redirectPath());
		return redirect($this->redirectPath())->with('status', 'Successful');
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

		$broker = $this->broker(); // PasswordBroker \Illuminate\Auth\Passwords\PasswordBroker
		// $response = Password::broker($broker)->sendResetLink(
		$response = $broker->sendResetLink($request->only('email') , $this->resetEmailBuilder($this->getRequestLang($request)));

		// return ($response);
		switch ($response) {
			case Password::RESET_LINK_SENT:
				return $this->getSendResetLinkEmailSuccessResponse($response);

			case Password::INVALID_USER:
			default:
				return $this->getSendResetLinkEmailFailureResponse($request, $response);
		}
	}

	/**
	 * Reset password and give confirmation
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	protected function postReset(Request $request) {
		$validator = Validator::make($request->all() , ['email' => 'required|email', 'password' => 'required|confirmed']);

		if ($validator->fails()) {
			$request->flash();
			return back()->withErrors($validator)->withInput($request->all());
		}

		return $this->reset($request);
	}

	/**
	 * Send E-mail Message for Password Reset
	 *
	 * @param  \App\User  $user
	 * @param  string  $lang
	 * @param  string  $token
	 * @return \Illuminate\Http\Response
	 */
	protected function sendMailMessage($user, $lang, $token) {
		$site_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
		$link = $site_url . '/password-reset/' . $lang . '/' . $token . '?email=' . urlencode($user->getEmailForPasswordReset());
		$email_message = 'Click here to reset your password: <a href="' . $link . '">' . $link . '</a>';
		Mail::send('translation-all.emails.password', ['email_message' => $email_message], function (Message $message) use ($email_message) {
			$resetView = view('translation-all/emails/password')->with(compact('email_message'));
			$body = $resetView->render();
			// dd([$body]);
			// $message = new Message();
			$message->getSwiftMessage()->setBody($body, 'text/html');
			$message->subject('Your Password Reset Link');
			$message->from(env('MAIL_FROM_NAME') ? env('MAIL_FROM_NAME') : env('APP_ADMIN_EMAIL', 'admin@territoryapi.com') , env('MAIL_TO_NAME', 'Territory Api Admin'));
			$message->bcc(env('APP_ADMIN_EMAIL', 'admin@territoryapi.com'));
			// dd([$message]);
			
		});

		return Password::RESET_LINK_SENT;

		// If Error:
		return Password::INVALID_USER;
	}

	protected function getResetToken(Request $request) {
		$length = 10;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$token = '';
		for ($i = 0;$i < $length;$i++) {
			$token .= $characters[rand(0, $charactersLength - 1) ];
		}
		return $token;
	}

	/**
	 * Get the response for after a failing password reset.
	 *
	 * @param  Request  $request
	 * @param  string  $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function getResetFailureResponse(Request $request, $response) {
		return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($response) ]);
	}

	/**
	 * Get the response for after the reset link could not be sent.
	 *
	 * @param  string  $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function getSendResetLinkEmailSuccessResponse($response) {
		return redirect($this->redirectPath())->with('status', trans($response));
	}
	/**
	 * Get the response for after the reset link could not be sent.
	 *
	 * @param  string  $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function getSendResetLinkEmailFailureResponse(Request $request, $response) {
		return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => trans($response) ]);
	}

	/**
	 * Get the Closure which is used to build the password reset email message.
	 *
	 * @return \Closure
	 */
	protected function resetEmailBuilder($lang = 'en') {
		return function (Message $message, $user = null, $token = null) use ($lang) {
			$request = Request();
			$creds = $request->only('email', 'token', '_token', 'csrftoken');
			// dd($creds);
			$email = $creds['email'];
			$token = $creds['_token']; // 'FakeToken000001'; //
			$user = User::whereEmail($creds['email'])->first();

			// Log::info([$request->path()]);
			// dd(['siteUrl' => $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"]);
			// dd([$request]);
			$site_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
			$link = $site_url . '/password-reset/en/' . $token . '?email=' . urlencode($user->getEmailForPasswordReset());
			$email_message = 'Click here to reset your password: <a href="' . $link . '">' . $link . '</a>';

			// dd(['resetEmailBuilder' => 1, 'lang' =>$lang, 'request' =>$request]);
			// dd(['APP_ADMIN_EMAIL' => env('APP_ADMIN_EMAIL', 'admin@territoryapi.com')]);
			$resetView = view('translation-all/emails/password')->with(compact('email_message'));
			$message->getSwiftMessage()->setBody($body = $resetView->render() , 'text/html');
			$message->subject($this->getEmailSubject());
			$message->from(env('MAIL_FROM_NAME') ? env('MAIL_FROM_NAME') : env('APP_ADMIN_EMAIL', 'admin@territoryapi.com') , env('MAIL_TO_NAME', 'Territory Api Admin'));
			$message->bcc(env('APP_ADMIN_EMAIL', 'admin@territoryapi.com'));
		};
	}

	public function getRequestLang($request = null) {
		$uri = $request->path();
		$uriSeg = explode('/', $uri);
		return end($uriSeg);
	}

}
