(function () {
    'use strict';

    angular.module('app', [
        'ngStorage',
        'ngRoute',
        'angular-loading-bar'
    ])
        .constant('urls', {
            BASE: 'http://territory.prositestudios.com/front/',
            BASE_API: 'http://territory.prositestudios.com/v1'
        })
        .config(['$routeProvider', '$httpProvider', function ($routeProvider, $httpProvider) {
            $routeProvider.
                when('/', {
                    templateUrl: 'spa/partials/home.html',
                    controller: 'HomeController'
                }).
                when('/signin', {
                    templateUrl: 'spa/partials/signin.html',
                    controller: 'HomeController'
                }).
                when('/signup', {
                    templateUrl: 'spa/partials/signup.html',
                    controller: 'HomeController'
                }).
                when('/restricted', {
                    templateUrl: 'spa/partials/restricted.html',
                    controller: 'RestrictedController'
                }).
                otherwise({
                    redirectTo: '/'
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
                        if (response.status === 401  || response.status === 403) { 
                            if (response.data[0] == 'Token has expired' || $location.$$url == '/signin') {
	                            delete $localStorage.token;
	                            $location.path('signin');
                            }
                            	
                        }
                        return $q.reject(response);
                    }
                };
            }]);
        }
        ]).run(function($rootScope, $location, $localStorage) {
            $rootScope.$on( "$routeChangeStart", function(event, next) {
                if ($localStorage.token == null) {
                    if ( next.templateUrl === "spa/partials/restricted.html") {
                        $location.path("signin");
                    }
                }
            });
        });
})();