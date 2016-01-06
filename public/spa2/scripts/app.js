(function () {
    'use strict';

    angular.module('app', [
        'ngStorage',
        'ngRoute',
        'angular-loading-bar',
        'ui.bootstrap', 
    ])
    .constant('urls', {
        BASE: 'http://territory.prositestudios.com/demo/',
        BASE_API: 'http://territory.prositestudios.com/v1'
    })
    .config(['$routeProvider', '$httpProvider', function ($routeProvider, $httpProvider) {
        $routeProvider.
            when('/', {
                templateUrl: 'spa2/partials/dashboard.html',
                controller: 'HomeController'
            }).
            when('/signin', {
                templateUrl: 'spa2/partials/signin.html',
                controller: 'HomeController'
            }).
            when('/signup', {
                templateUrl: 'spa2/partials/signup.html',
                controller: 'HomeController'
            }).
            when('/dashboard', {
                templateUrl: 'spa2/partials/dashboard.html',
                controller: 'ApiController'
            }).
            when('/users', {
                templateUrl: 'spa2/partials/users.html',
                controller: 'ApiController'
            }).
/*
            when('/users/:userId', {
                templateUrl: 'spa2/partials/user-details.html',
                controller: 'ApiController'
            }).
*/
            when('/publishers', {
                templateUrl: 'spa2/partials/publishers.html',
                controller: 'ApiController'
            }).
            when('/publishers/:publisherId', {
                templateUrl: 'spa2/partials/publisher-details.html',
                controller: 'ApiController'
            }).
            when('/territories', {
                templateUrl: 'spa2/partials/territories.html',
                controller: 'ApiController'
            }).
            when('/territories/:territoryId', {
                templateUrl: 'spa2/partials/territory-details.html',
                controller: 'ApiController'
            }).
            otherwise({
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
            if ($localStorage.token == null) {
                if ( $location.$$path !== "/signup" && $location.$$path !== "/signin" && $location.$$path !== "/" && $location.$$path !== "") {
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