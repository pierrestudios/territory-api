<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Territory Services API - SPA</title>
    
    <!-- Bootstrap Core CSS -->
    <link href="/theme-creole/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/theme-creole/css/sb-admin-2.css" rel="stylesheet">
    <link href="/theme-creole/css/switchery.min.css" rel="stylesheet" />

    <!-- Morris Charts CSS -->
    <link href="/theme-creole/css/morris.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="/theme-creole/css/dataTables.responsive.bootstrap.css" rel="stylesheet">
        
    <!-- Custom Fonts -->
    <link href="/theme-creole/css/font-awesome.min.css" rel="stylesheet">
     
    <link href="/theme-creole/css/app.css" rel="stylesheet">

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
                        <li data-ng-show="token"><a href="#"><i class="fa fa-user fa-fw"></i> Profile</a></li>
                        <li data-ng-show="token"><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
                        <li class="divider"></li>
						<li data-ng-show="token"><a ng-href="#/restricted">Restricted area</a></li>
	                    <li data-ng-hide="token"><a ng-href="#/signin">Signin</a></li>
	                    <li data-ng-hide="token"><a ng-href="#/signup">Signup</a></li>
	                    <li data-ng-show="token"><a ng-href="#/" ng-click="logout()">Soti</a></li>
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
                            <a href="#/dashboard"><i class="fa fa-dashboard fa-fw"></i> Antré an</a>
                        </li>
						<li ng-show="isAdmin">
                            <a href="#/users"><i class="fa fa-user fa-fw"></i> Itilizatè</a>
                        </li>
						<li ng-show="isManager">
                            <a href="#/publishers"><i class="fa fa-group fa-fw"></i> Pwoklamatè</a>
                        </li>
						<li>
                            <a href="#/territories"><i class="fa fa-map fa-fw"></i> Teritwa</a>
                        </li> 
                        <li class="hidden"><a href="#/settings"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
	                    <li><a href="#/logout" ng-click="logout()"><i class="fa fa-sign-out fa-fw"></i> Soti</a></li>
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
    <script src="/theme-creole/lib/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/theme-creole/lib/bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/theme-creole/lib/jquery.dataTables.min.js"></script>
    <script src="/theme-creole/lib/dataTables.bootstrap.min.js"></script>    
    <script src="/theme-creole/lib/dataTables.responsive.js"></script>     
    <script src="/theme-creole/lib/jquery.maskedinput.min.js"></script>       
    <script src="/theme-creole/lib/raphael-min.js"></script>
    <script src="/theme-creole/lib/morris.min.js"></script>
    <script src="/theme-creole/lib/switchery.min.js"></script>
    <script src="/theme-creole/lib/vis.min.js"></script>
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.14/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.14/angular-route.min.js"></script>
    <script src="/theme-creole/lib/ui-bootstrap-tpls-0.14.3.min.js"></script>
    <script src="/theme-creole/lib/ngStorage.js"></script>
    <script src="/theme-creole/lib/loading-bar.js"></script>
    
    <script>
	    // Site Settings
	    var sitePath = 'creole',
	    	domainPath = 'http://territory.prositestudios.com/',
	    	apiPath = 'http://territory.prositestudios.com/v1',
	    	themePath = 'theme-creole/';
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script src="/theme-creole/scripts/ui.js"></script>
    
    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
	<script src="/theme-creole/lib/d3-timeline.js"></script>
  
    <script src="/theme-creole/scripts/app.js"></script>
    <script src="/theme-creole/scripts/controllers.js"></script>
    <script src="/theme-creole/scripts/services.js"></script>
</body>
</html>