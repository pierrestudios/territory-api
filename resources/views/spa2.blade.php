<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Territory Services API - SPA</title>
    
    
    <title>SB Admin 2 - Bootstrap Admin Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="/spa2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/spa2/css/sb-admin-2.css" rel="stylesheet">
    <link href="/spa2/css/switchery.min.css" rel="stylesheet" />

    <!-- Morris Charts CSS -->
    <link href="/spa2/css/morris.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="/spa2/css/dataTables.responsive.bootstrap.css" rel="stylesheet">
        
    <!-- Custom Fonts -->
    <link href="/spa2/css/font-awesome.min.css" rel="stylesheet">
     
    <link href="/spa2/css/app.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body ng-app="app">
    
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0" data-ng-controller="ApiController">
            <div class="navbar-header">
                <button data-ng-show="token" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#/">Territory App</a>
            </div>
            <!-- /.navbar-header -->
 
			<ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
<!--
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#/">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
-->
                    <ul class="dropdown-menu dropdown-user">
                        <li data-ng-show="token"><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                        <li data-ng-show="token"><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
                        <li class="divider"></li>
						<li data-ng-show="token"><a ng-href="#/restricted">Restricted area</a></li>
	                    <li data-ng-hide="token"><a ng-href="#/signin">Signin</a></li>
	                    <li data-ng-hide="token"><a ng-href="#/signup">Signup</a></li>
	                    <li data-ng-show="token"><a ng-href="#/" ng-click="logout()">Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div data-ng-show="token" class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="#/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
						<li ng-show="isAdmin">
                            <a href="#/users"><i class="fa fa-user fa-fw"></i> Users</a>
                        </li>
						<li ng-show="isManager">
                            <a href="#/publishers"><i class="fa fa-group fa-fw"></i> Publishers</a>
                        </li>
						<li>
                            <a href="#/territories"><i class="fa fa-map fa-fw"></i> Territories</a>
                        </li> 
                        <li class="hidden"><a href="#/settings"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
	                    <li><a href="#/logout" ng-click="logout()"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
            
        </nav>
        
		<div id="page-wrapper" class="page-content" ng-class="tokenActive" ng-view=""></div>
		
    </div>
    	
    <div class="footer" style="display: none">
        <div class="container">
            <p class="muted credit">Developed by <a href="http://www.pierrestudios.com" title="Pierre Studios">Pierre Studios</a></p>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/spa2/lib/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/spa2/lib/bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/spa2/lib/jquery.dataTables.min.js"></script>
    <script src="/spa2/lib/dataTables.bootstrap.min.js"></script>    
    <script src="/spa2/lib/dataTables.responsive.js"></script>     
    <script src="/spa2/lib/jquery.maskedinput.min.js"></script>       
    <script src="/spa2/lib/raphael-min.js"></script>
    <script src="/spa2/lib/morris.min.js"></script>
    <script src="/spa2/lib/switchery.min.js"></script>
    <script src="/spa2/scripts/ui.js"></script>
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.14/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.14/angular-route.min.js"></script>
    <script src="/spa2/lib/ui-bootstrap-tpls-0.14.3.min.js"></script>
    <script src="/spa2/lib/ngStorage.js"></script>
    <script src="/spa2/lib/loading-bar.js"></script>
    <script src="/spa2/scripts/app.js"></script>
    <script src="/spa2/scripts/controllers.js"></script>
    <script src="/spa2/scripts/services.js"></script>
</body>
</html>