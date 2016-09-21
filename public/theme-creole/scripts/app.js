(function () {
    'use strict';
    
    angular.module('app', [
        'ngStorage',
        'ngRoute',
        'angular-loading-bar',
        'ui.bootstrap', 
    ])
    .constant('urls', {
        BASE:  domainPath + sitePath + '/',
        BASE_API: apiPath
    })
    .constant('settings', {
        site: sitePath,
        domain: domainPath,
        api: apiPath,
        theme: themePath
    })
    .config(['$routeProvider', '$httpProvider', function ($routeProvider, $httpProvider) {
        $routeProvider
            .when('/', {
                templateUrl: themePath + 'partials/dashboard.html',
                controller: 'HomeController'
            })
            .when('/signin', {
                templateUrl: themePath + 'partials/signin.html',
                controller: 'HomeController'
            })
            .when('/signup', {
                templateUrl: themePath + 'partials/signup.html',
                controller: 'HomeController'
            })
            .when('/password-retrieve', {
                templateUrl: themePath + 'partials/password-retrieve.html',
                controller: 'HomeController'
            })
            
            // API Services Endpoints
            .when('/dashboard', {
                templateUrl: themePath + 'partials/dashboard.html',
                controller: 'ApiController'
            })
            .when('/users', {
                templateUrl: themePath + 'partials/users.html',
                controller: 'ApiController'
            })
            .when('/publishers', {
                templateUrl: themePath + 'partials/publishers.html',
                controller: 'ApiController'
            })
            .when('/publishers/:publisherId', {
                templateUrl: themePath + 'partials/publisher-details.html',
                controller: 'ApiController'
            })
            .when('/territories', {
                templateUrl: themePath + 'partials/territories.html',
                controller: 'ApiController'
            })
            .when('/territories/map/:territoryId', {
                templateUrl: themePath + 'partials/territory-map.html',
                controller: 'ApiController',
                map: true
            })
            .when('/territories/:territoryId', {
                templateUrl: themePath + 'partials/territory-details.html',
                controller: 'ApiController'
            })
            .when('/activities', {
                templateUrl: themePath + 'partials/activities.html',
                controller: 'ApiController'
            })
            
            .otherwise({
                redirectTo: '/dashboard'
            });

        $httpProvider.interceptors.push(['$q', '$location', '$localStorage', function ($q, $location, $localStorage) {
            return {
                'request': function (config) {
                    config.headers = config.headers || {};
                    if ($localStorage.token) {
                        config.headers.Authorization = 'Bearer ' + $localStorage.token;
                    }
                    return config;
                },
                'responseError': function (response) {
                    console.log('response', response);
                    if (response.data[0] == 'Token has expired' || $location.$$url == '/signin') {
                        delete $localStorage.token;
                        $location.path('signin');
                    }
                    return $q.reject(response);
                }
            };
        }]);
    }]).run(function($rootScope, $location, $localStorage, $templateCache) {
        $rootScope.$on( "$routeChangeStart", function(event, next) {
	        // console.log('$rootScope', $rootScope);
	        // console.log('$location', $location);
            if (!$localStorage.token || $localStorage.token == null) {
                if ( $location.$$path !== "/signup" 
                	&& $location.$$path !== "/signin" 
                	&& $location.$$path !== "/password-retrieve" 
                	&& $location.$$path !== "/" 
                	&& $location.$$path !== "") {
                    $location.path("signin");
                    window.location.reload();
                }
            }
            if (typeof(current) !== 'undefined'){
	            $templateCache.remove(current.templateUrl);
	        }
        });
    });
})();