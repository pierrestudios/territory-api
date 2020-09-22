@extends('translation-all/template')

@section('page-content')
  <div id="app" class="main-container">
   <header class="_3m_sybl1wI_TQE0EQ6jxlE">
    <nav class="_2ZoWOMi2AXi2nbzld4CvhK">
     <a href="#"><h1 color="#fff" class="_3DSzQkLhLLp4RZex21xlU" style="color: rgb(255, 255, 255);">Territory App</h1></a>
    </nav>
   </header>
   <div class="_1v9UFOi0DG4e50MmJixuHQ _1hsPEFyseXT-usWJRPlKq3">
    <h2 class="_1sLltdW7V-nkdPZRNHfkpe"><span>Reset Your Password</span></h2>
    <div class="_2m5JQFZ8VcFT0hWlvdWorB">
      @if (session('status'))
        <div class="alert alert-success">
          {{ session('status') }}
        </div>
      @else
      <form class="form-horizontal" role="form" method="POST" action="{{ '/password-reset/'. $lang }}">
        {!! csrf_field() !!}
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
          <input name="email" placeholder="E-Mail Address" type="email" autocomplete="username" class="_20oJ_EZkxWbFQcvPMG6WyJ _3J2u7Y4lQxirfSxpaKyoUP" value="{{ $email ?? old('email') }}" />
          @if ($errors->has('email'))
            <span class="help-block">
              <strong style="color:red">>{{ $errors->first('email') }}</strong>
            </span>
          @endif
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
          <input name="password" placeholder="Password" type="password" class="_20oJ_EZkxWbFQcvPMG6WyJ _3J2u7Y4lQxirfSxpaKyoUP" />
          @if ($errors->has('password'))
            <span class="help-block">
              <strong style="color:red">{{ $errors->first('password') }}</strong>
            </span>
          @endif
        </div>
        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
          <input name="password_confirmation" placeholder="Confirm Password" type="password" class="_20oJ_EZkxWbFQcvPMG6WyJ _3J2u7Y4lQxirfSxpaKyoUP" />
          @if ($errors->has('password_confirmation'))
            <span class="help-block">
              <strong style="color:red">>{{ $errors->first('password_confirmation') }}</strong>
            </span>
          @endif
        </div>
        <button class="_2rx-79Bt3Th5s6FmzLO6Iq _3vOsf5ulw9iB4_PfOXruru">Submit New Password</button>
        <div class="_1NAWrKVeyyNkABKgmB_cg2"></div>
      </form>
      @endif
    </div>
   </div>
  </div>
@endsection
