<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Territory App - {{$title or ' Manage Your Congregation Territory'}}</title>
    
    <!-- Bootstrap Core CSS -->
    <link href="/theme-all/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/theme-all/css/sb-admin-2.css" rel="stylesheet">
    <link href="/theme-all/css/switchery.min.css" rel="stylesheet" />
    <link href="/theme-all/css/sweetalert.css" rel="stylesheet" />
        
	<!-- DataTables Responsive CSS -->
    <link href="/theme-all/css/dataTables.responsive.bootstrap.css" rel="stylesheet">
    
    <!-- Custom Fonts -->
    <link href="/theme-all/css/font-awesome.min.css" rel="stylesheet">
     
    <link href="/theme-all/css/app.css" rel="stylesheet">
	
	@yield('page-styles')

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body ng-app="app">
    
    <div id="wrapper">

 

@yield('page-content')

         
        		
    </div>
    	
    <div class="footer" style="display: none">
        <div class="container">
            <p class="muted credit">Developed by <a href="http://www.pierrestudios.com" title="Pierre Studios">Pierre Studios</a></p>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/theme-all/lib/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/theme-all/lib/bootstrap.min.js"></script>

    @yield('page-scripts')
	
  
</body>
</html>