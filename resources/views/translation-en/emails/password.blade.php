<html>
<head></head>
<body style="margin: 0 auto">
	<div style="background: #337ab7; color: #fff; padding: 20px">
		<h3>Your Password Reset Link</h3>
	</div>
	<div style="color: #333; padding: 20px">
		<p>
			Click here to reset your password: <a href="{{ $link = url('password-reset/en', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
		</p>
	</div>
</body>
</html>
