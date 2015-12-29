(function () {
    'use strict';

    angular.module('app', [
        'ngStorage',
        'ngRoute',
        'angular-loading-bar'
    ])
    .constant('urls', {
        BASE: 'http://territory.prositestudios.com/front2/',
        BASE_API: 'http://territory.prositestudios.com/v1'
    })
    .config(['$routeProvider', '$httpProvider', function ($routeProvider, $httpProvider) {
        $routeProvider.
            when('/', {
                templateUrl: 'spa2/partials/dashboard.html',
                controller: 'RestrictedController'
            }).
            when('/signin', {
                templateUrl: 'spa2/partials/signin.html',
                controller: 'HomeController'
            }).
            when('/signup', {
                templateUrl: 'spa2/partials/signup.html',
                controller: 'HomeController'
            }).
            when('/publishers', {
                templateUrl: 'spa2/partials/publishers.html',
                controller: 'RestrictedController'
            }).
            when('/publishers/:publisherId', {
                templateUrl: 'spa2/partials/publisher-details.html',
                controller: 'RestrictedController'
            }).
            when('/territories', {
                templateUrl: 'spa2/partials/territories.html',
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
                    if (response.data[0] == 'Token has expired' || $location.$$url == '/signin') {
                        delete $localStorage.token;
                        $location.path('signin');
                    }
                    return $q.reject(response);
                }
            };
        }]);
    }]).run(function($rootScope, $location, $localStorage) {
        $rootScope.$on( "$routeChangeStart", function(event, next) {
            if ($localStorage.token == null) {
                if ( next.templateUrl !== "spa2/partials/signin.html") {
                    $location.path("signin");
                }
            }
        });
    });
})();