(function () {
    'use strict';

    angular.module('app')
        .factory('Auth', ['$http', '$localStorage', 'urls', function ($http, $localStorage, urls) {
            function urlBase64Decode(str) {
                var output = str.replace('-', '+').replace('_', '/');
                switch (output.length % 4) {
                    case 0:
                        break;
                    case 2:
                        output += '==';
                        break;
                    case 3:
                        output += '=';
                        break;
                    default:
                        throw 'Illegal base64url string!';
                }
                return window.atob(output);
            }

/*
            function getClaimsFromToken() {
                var token = $localStorage.token;
                var user = {};
                if (typeof token !== 'undefined') {
                    var encoded = token.split('.')[1];
                    user = JSON.parse(urlBase64Decode(encoded));
                }
                return user;
            }
*/

            // var tokenClaims = getClaimsFromToken();

            return {
                signup: function (data, success, error) {
                    $http.post(urls.BASE_API + '/signup', data).success(success).error(error)
                },
                signin: function (data, success, error) {
                    $http.post(urls.BASE_API + '/signin', data).success(success).error(error)
                },
/*
                getTokenClaims: function () {
                    return tokenClaims;
                }
*/
            };
        }
        ]);

    angular.module('app')
        .factory('API', ['$http', 'urls', '$localStorage', function ($http, urls, $localStorage) {

            return {
                logout: function (success) {
                    delete $localStorage.token;
                    success();
                },
                getApiAccess: function (success, error) {
                    $http.get(urls.BASE_API + '/auth-user').success(success).error(error)
                },
                getApiData: function (success, error) {
                    $http.get(urls.BASE_API + '/restricted').success(success).error(error)
                },
                getPublishers: function (success, error) {
                    $http.get(urls.BASE_API + '/publishers').success(success).error(error)
                },
                getPublisher: function (publisherId, success, error) {
                    $http.get(urls.BASE_API + '/publishers/' + publisherId).success(success).error(error)
                },
                getTerritories: function (success, error) {
                    $http.get(urls.BASE_API + '/territories').success(success).error(error)
                },
                getAddresses: function (success, error) {
                    $http.get(urls.BASE_API + '/addresses').success(success).error(error)
                }
            };
        }
        ]);
})();