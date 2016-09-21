@extends('translation-creole/template')

@section('page-content')

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

@endsection
@section('page-scripts')

	<!-- Custom Theme JavaScript -->
    <script src="/theme-creole/lib/jquery.dataTables.min.js"></script>
    <script src="/theme-creole/lib/dataTables.bootstrap.min.js"></script>    
    <script src="/theme-creole/lib/dataTables.responsive.js"></script>     
    <script src="/theme-creole/lib/jquery.maskedinput.min.js"></script>       
    <script src="/theme-creole/lib/raphael-min.js"></script>
    <script src="/theme-creole/lib/morris.min.js"></script>
    <script src="/theme-creole/lib/switchery.min.js"></script>
    <script src="/theme-creole/lib/vis.min.js"></script>
    <script src="/theme-creole/lib/angular-js-1.3.14/angular.min.js"></script>
    <script src="/theme-creole/lib/angular-js-1.3.14/angular-route.min.js"></script>
    <script src="/theme-creole/lib/ui-bootstrap-tpls-0.14.3.min.js"></script>
    <script src="/theme-creole/lib/ngStorage.js"></script>
    <script src="/theme-creole/lib/loading-bar.js"></script>
    <script src="/theme-creole/lib/swal/sweetalert.min.js"></script>
    
    <script>
	    // Site Settings
	    var sitePath = 'creole', // Site URI 
	    	domainPath = window.location.protocol + '//' + window.location.hostname + '/',
	    	apiPath =  domainPath + 'v1',
	    	themePath = 'theme-creole/';
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing,geometry&key={{ getenv('GOOGLE_API_KEY') }}"></script>
    <script src="/theme-creole/scripts/ui.js"></script>
	<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
	<script src="/theme-creole/lib/d3-timeline.js"></script>
    <script src="/theme-creole/scripts/app.js"></script>
    <script src="/theme-creole/scripts/controllers.js"></script>
    <script src="/theme-creole/scripts/services.js"></script>

@endsection	