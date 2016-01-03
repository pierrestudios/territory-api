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
                formatDateObj: function(date) {
	                var formatted = '';
	              	if (date && Object.prototype.toString.call(date) === '[object Date]') {
		              	formatted = date.getFullYear() + '-' + ("0" + (date.getMonth() + 1)).slice(-2) + '-' + date.getDate();
	              	}
	              	return formatted;
                },
                passDueTerritory: function(date, passDueDate) {
	                var d = new Date(); d.setMonth(d.getMonth() - 3);
	                passDueDate = passDueDate || d.toDateString();
	                if (!/Invalid|NaN/.test(new Date(date))) {
				        return new Date(date) < new Date(passDueDate);
				    }
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
                addPublisher: function (data, success, error) {
                    $http.post(urls.BASE_API + '/publishers/add', data).success(success).error(error)
                },
                updatePublisher: function (publisherId, data, success, error) {
                    $http.post(urls.BASE_API + '/publishers/' + publisherId, data).success(success).error(error)
                },
                getTerritories: function (success, error) {
                    $http.get(urls.BASE_API + '/territories').success(success).error(error)
                },
                getTerritory: function (territoryId, success, error) {
                    $http.get(urls.BASE_API + '/territories/' + territoryId).success(success).error(error)
                },
                addTerritory: function (data, success, error) {
                    $http.post(urls.BASE_API + '/territories/add', data).success(success).error(error)
                },
                updateTerritory: function (territoryId, data, success, error) {
                    $http.post(urls.BASE_API + '/territories/' + territoryId, data).success(success).error(error)
                },
                addAddress: function (territoryId, data, success, error) {
                    $http.post(urls.BASE_API + '/territories/' + territoryId + '/addresses/add', data).success(success).error(error)
                },
                updateAddress: function (territoryId, addressId, data, success, error) {
                    $http.post(urls.BASE_API + '/territories/' + territoryId + '/addresses/edit/' + addressId, data).success(success).error(error)
                },
                removeAddress: function (addressId, success, error) {
                    $http.post(urls.BASE_API + '/addresses/remove/' + addressId).success(success).error(error)
                },
                getAddresses: function (success, error) {
                    $http.get(urls.BASE_API + '/addresses').success(success).error(error)
                },
                addNote: function (territoryId, addressId, data, success, error) {
                    $http.post(urls.BASE_API + '/territories/' + territoryId + '/addresses/'+ addressId +'/notes/add', data).success(success).error(error)
                },
                updateNote: function (territoryId, noteId, data, success, error) {
                    $http.post(urls.BASE_API + '/territories/' + territoryId + '/notes/edit/' + noteId, data).success(success).error(error)
                }
            };
        }
        ]);
})();