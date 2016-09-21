Click here to reset your password (Creole): <a href="{{ $link = url('password-reset/creole', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a>
