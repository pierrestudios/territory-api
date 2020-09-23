<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
	 * If no token is present, display the link request form.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * 
	 * @param  string  $lang
	 * @param  string|null  $token
	 * @return \Illuminate\Http\Response
	 */
	public function getResetView(Request $request, $lang = 'all', $token = null) {
		$this->lang = $lang;
		$this->resetView = 'translation-all/reset';
		$this->linkRequestView = 'translation-all/passwords/email';

		if (is_null($token)) {
			return $this->showLinkRequestForm($request);
		}

		$email = $request->input('email');

		return view($this->resetView)->with(compact('token', 'email', 'lang'));
	}

	/**
	 * Display the form to request a password reset link.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function showLinkRequestForm(Request $request) {
		$isApi = strpos($request->path(), 'v1');
		return view($this->linkRequestView)->with(['lang' => $this->lang, 'isApi' => $isApi])->withErrors(null);
	}

	/**
	 * Send a reset link to the given user.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $lang
	 * @return \Illuminate\Http\Response
	 */
	public function postEmail(Request $request, $lang = 'en') {
		$validator = Validator::make($request->all(), [
			'email' => 'required|email'
		]);

		if ($validator->fails()) {
			$request->flash();
			return back()->withErrors($validator)->withInput($request->all());
		}

		if ($request->has('password')) {
			return $this->postReset($request);
		}

		$email = $request->input('email');
		$resetToken = $this->generateResetToken();
		$user = User::whereEmail($email)->first();

		if (!$user) {
			return $this->getSendResetLinkEmailFailureResponse($request, 'Email not found in our system');
		}

		$response = $this->sendMailMessage($user, $lang, $resetToken);

		switch ($response) {
			case Password::RESET_LINK_SENT:
				return $this->getSendResetLinkEmailSuccessResponse($response, $lang);

			case Password::INVALID_USER:
			default:
				return $this->getSendResetLinkEmailFailureResponse($request, $response);
		}
	}

	/**
	 * Send a reset link to the given user (for Api "password-retrieve" route)
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $lang
	 * @return \Illuminate\Http\Response
	 */
	public function postEmailApi(Request $request, $lang = 'en') {

		$validator = Validator::make($request->all() , ['email' => 'required|email']);

		if ($validator->fails()) {
			return Response()->json(['error' => 'Email is required.'], 401);
		}

		$email = $request->input('email');
		$resetToken = $this->generateResetToken();
		$user = User::whereEmail($email)->first();

		if (!$user) {
			return Response()->json(['error' => 'User with email, "' . $email . '" could not be found.'], 401);
		}

		$response = $this->sendMailMessage($user, $lang, $resetToken);

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
		return redirect($this->redirectPath())->with('status', 'Successful');
	}

	// redirect after success
	public function redirectPath($lang = 'en') {
		return '/' . $lang;
	}

	/**
	 * Send a reset link to the given user.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function sendResetLinkEmail(Request $request) {
		$this->validate($request, ['email' => 'required|email']);
		$broker = $this->broker();
		$response = $broker->sendResetLink($request->only('email'), $this->resetEmailBuilder($this->getRequestLang($request)));

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
	 * @param  \App\Models\User  $user
	 * @param  string  $lang
	 * @param  string  $resetToken
	 * @return \Illuminate\Http\Response
	 */
	protected function sendMailMessage($user, $lang, $resetToken) {
		$link = config('app.url') . '/password-reset/' . $lang . '/' . $resetToken . '?email=' . urlencode($user->getEmailForPasswordReset());
		$email_message = 'Click here to reset your password: <a href="' . $link . '">' . $link . '</a>';
		Mail::send('translation-all.emails.password', ['email_message' => $email_message], function (Message $message) use ($email_message, $user) {
			$resetView = view('translation-all/emails/password')->with(compact('email_message'));
			$message->getSwiftMessage()->setBody($resetView->render(), 'text/html');
			$message->subject('Your Password Reset Link');
      $message->from(config('app.mailFromEmail'), config('app.mailFromName'));
			$message->bcc(config('app.mailToEmail'), config('app.mailToName'));
			$message->to($user->email);
		});

		return Password::RESET_LINK_SENT;
	}

	protected function generateResetToken() {
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
	protected function getSendResetLinkEmailSuccessResponse($response, $lang = 'en') {
		return redirect($this->redirectPath($lang))->with('status', trans($response));
	}
	/**
	 * Get the response for after the reset link could not be sent.
	 *
	 * @param  Request  $request
	 * @param  string  $response
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function getSendResetLinkEmailFailureResponse(Request $request, $response = 'An error occurred') {
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
			$email = $creds['email'];
			$token = $creds['_token']; 
			$user = User::whereEmail($creds['email'])->first();
			$link = config('app.url') . '/password-reset/en/' . $token . '?email=' . urlencode($user->getEmailForPasswordReset());
			$email_message = 'Click here to reset your password: <a href="' . $link . '">' . $link . '</a>';
			$resetView = view('translation-all/emails/password')->with(compact('email_message'));
			$message->getSwiftMessage()->setBody($body = $resetView->render() , 'text/html');
			$message->subject($this->getEmailSubject());
			$message->from(env('MAIL_FROM_NAME') ? env('MAIL_FROM_NAME') : env('APP_ADMIN_EMAIL', 'admin@territoryapi.com') , env('MAIL_TO_NAME', 'Territory Api Admin'));
			$message->bcc(env('APP_ADMIN_EMAIL', 'admin@territoryapi.com'));
		};
	}

	protected function getRequestLang($request = null) {
		$uri = $request->path();
		$uriSeg = explode('/', $uri);
		return end($uriSeg);
	}

}
