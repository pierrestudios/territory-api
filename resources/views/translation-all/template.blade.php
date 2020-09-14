<!DOCTYPE html>
<html>
<head>
    <title>Territory App - {{$title ?? ' Manage Your Congregation Territory'}}</title>
    <link rel="preload" href="/assets/bundle.js" as="script">
	<base href="{{$lang}}/" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimal-ui">
	<meta name="msapplication-TileColor" content="#673ab8">
	<meta name="msapplication-TileImage" content="/assets/icons/mstile-150x150.png">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-title" content="Territory App - {{$title ?? ' Manage Your Congregation Territory'}}">
	<meta name="application-name" content="Territory App - {{$title ?? ' Manage Your Congregation Territory'}}">
	<meta name="format-detection" content="telephone=no">
	<meta name="theme-color" content="#673ab8">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/icons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/assets/icons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/assets/icons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link type="text/css" rel="stylesheet" href="/assets/react-datepicker.css" />
	<link type="text/css" rel="stylesheet" href="/assets/style.css" />
	<style>
		.title {
			font-size: 36px;
		}
	</style>
	@yield('page-styles')
 
</head>
<body>

@yield('page-content')

@yield('page-scripts')
	  
</body>
</html>