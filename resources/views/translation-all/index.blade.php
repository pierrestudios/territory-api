@extends('translation-all/template')

@section('page-content')
<div id="browser-message"></div>
@endsection
@section('page-scripts')

@if ($lang)
<script>
	if (typeof Symbol !== 'function')
		alert('Your browser is not supported.' + "\n" + 'Download Chrome, https://www.google.com/chrome/browser/desktop/index.html');

	else {
		// Site Settings
		var siteSettings = {
			sitePath: '{{$lang}}', // Site URI
			lang: '{{$lang}}',
			langPacks: {!!$langPacks!!},  // Keep on 1 line (for Blade)
			domainPath: '{{env("APP_URL")}}',
			apiPath: '{{env("APP_URL")}}/v1',
			GOOGLE_API_KEY: '{{ getenv("GOOGLE_API_KEY") }}'
		}
		if (!window.localStorage) {
			alert('Please enable Cookies and Data storage');
		}
	}
</script>
<script type="text/javascript" src="/assets/bundle.js"></script>
@else
<script>
	var langList = {!!$langList!!};
	var langOptions  = langList.map(function(la) {
		return '<a href="/' + la.id + '">' + la.label + '</a>';
	}).join(" - ");
	var textContent = (typeof Symbol === 'function') ? '<h3 class="title">Territory App</h3><p>Select your language: ' + langOptions + '</p>' : '<p>Your browser is not supported. <a href="https://www.google.com/chrome/browser/desktop/index.html">Download Chrome</a></p>';
	document.getElementById('browser-message').innerHTML = '<div style="text-align: center; margin: 1em">' + textContent + '</div>';
</script>
@endif

@endsection