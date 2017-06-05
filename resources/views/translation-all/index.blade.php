@extends('translation-all/template')

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

                    <ul class="dropdown-menu dropdown-user">
                        <li data-ng-show="token"><a href="#"><i class="fa fa-user fa-fw"></i> {{$Language->translate('Profile', 'Profile')}}</a></li>
                        <li data-ng-show="token"><a href="#"><i class="fa fa-gear fa-fw"></i> {{$Language->translate('Settings', 'Settings')}}</a></li>
                        <li class="divider"></li>
	                    <li data-ng-show="token"><a ng-href="#/" ng-click="logout()">{{$Language->translate('Logout', 'Logout')}}</a></li>
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
                            <a href="#/dashboard"><i class="fa fa-dashboard fa-fw"></i> {{$Language->translate('Dashboard', 'Dashboard')}}</a>
                        </li>
						<li ng-show="isAdmin">
                            <a href="#/users"><i class="fa fa-user fa-fw"></i> {{$Language->translate('Users', 'Users')}}</a>
                        </li>
						<li ng-show="isManager">
                            <a href="#/publishers"><i class="fa fa-group fa-fw"></i> {{$Language->translate('Publishers', 'Publishers')}}</a>
                        </li>
						<li>
                            <a href="#/territories"><i class="fa fa-map fa-fw"></i> {{$Language->translate('Territories', 'Territories')}}</a>
                        </li> 
                        <li class="hidden"><a href="#/settings"><i class="fa fa-gear fa-fw"></i> {{$Language->translate('Settings', 'Settings')}}</a></li>
	                    <li><a href="#/logout" ng-click="logout()"><i class="fa fa-sign-out fa-fw"></i> {{$Language->translate('Logout', 'Logout')}}</a></li>
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
    <script src="/theme-all/lib/jquery.dataTables.min.js"></script>
    <script src="/theme-all/lib/dataTables.bootstrap.min.js"></script>    
    <script src="/theme-all/lib/dataTables.responsive.js"></script>     
    <script src="/theme-all/lib/jquery.maskedinput.min.js"></script>       
    <script src="/theme-all/lib/raphael-min.js"></script>
    <script src="/theme-all/lib/morris.min.js"></script>
    <script src="/theme-all/lib/switchery.min.js"></script>
    <script src="/theme-all/lib/vis.min.js"></script>
    <script src="/theme-all/lib/angular-js-1.3.14/angular.min.js"></script>
    <script src="/theme-all/lib/angular-js-1.3.14/angular-route.min.js"></script>
    <script src="/theme-all/lib/ui-bootstrap-tpls-0.14.3.min.js"></script>
    <script src="/theme-all/lib/ngStorage.js"></script>
    <script src="/theme-all/lib/loading-bar.js"></script>
    <script src="/theme-all/lib/swal/sweetalert.min.js"></script>
    
    <script>
	    // Site Settings
	    var sitePath = '{{$lang}}', // Site URI
	    	lang = '{{$lang}}',
	    	langPacks = {!! $langPacks !!},
	    	domainPath = '{{env("APP_URL")}}',
	    	apiPath = '{{env("APP_URL")}}/v1',
	    	themePath = 'theme-all/';
	    	
	    	if (!window.localStorage) {
		    	alert('Please enable Cookies and Data storage');
		    	// window.location = '/';
	    	}
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing,geometry&key={{ getenv('GOOGLE_API_KEY') }}"></script>
	<script src="//d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script src="/theme-all/scripts/ui.js"></script>
	<script src="/theme-all/lib/d3-timeline.js"></script>
    <script src="/theme-all/scripts/app.js"></script>
    <script src="/theme-all/scripts/controllers.js"></script>
    <script src="/theme-all/scripts/services.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.3/Chart.bundle.min.js"></script>

@endsection	