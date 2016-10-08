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

            return {
                signup: function (data, success, error) {
                    $http.post(urls.BASE_API + '/signup', data).success(success).error(error)
                },
                signin: function (data, success, error) {
                    $http.post(urls.BASE_API + '/signin', data).success(success).error(error)
                }
            };
            
        }]);

	angular.module('app')
        .factory('Notify', ['$http', 'urls', '$localStorage', function ($http, urls, $localStorage) {
            return {
	            error: function(message, title, type) {
		            // alert(message);
		            swal({
			            title: title || "Notice",
			            text: message,
			            type: type || "warning",
			            // showCancelButton: true,
			            // confirmButtonColor: "#DD6B55",
			            // confirmButtonText: "Yes, delete it!",
			            // closeOnConfirm: false 
			        }, function(){
				        // swal("Deleted!", "Your imaginary file has been deleted.", "success"); 
			        });
	            }
	        }
	    }]);
	        	
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
                formatDateStrToObj: function(dateStr) {
	                var date_ = [], formatted = null;
	              	if (dateStr) {
		              	date_ = dateStr.split('-');
		              	formatted = new Date(date_[0], (parseInt(date_[1]) -1), date_[2]);
	              	}
	              	// console.log('formatted', formatted);
	              	
	              	return formatted;
                },
                passDueTerritory: function(date, passDueDate) {
	                var d = new Date(); d.setMonth(d.getMonth() - 3);
	                passDueDate = passDueDate || d.toDateString();
	                if (!/Invalid|NaN/.test(new Date(date))) {
				        return new Date(date) < new Date(passDueDate);
				    }
                },
                isOldTerritory: function(date, oldDate) {
	                var d = new Date(); d.setMonth(d.getMonth() - 6);
	                oldDate = oldDate || d.toDateString();
	                if (!/Invalid|NaN/.test(new Date(date))) {
				        return new Date(date) < new Date(oldDate);
				    }
                },
                getTerritoryStreets: function(data) {
	                if(data && data.length) {
		                var i=0, streets=[], territoryStreets=[];
		                for(i in data) {
			                if(!data[i].isApt && (streets.indexOf(data[i].streetName) === -1)) {
				                streets.push(data[i].streetName);
				                territoryStreets.push({"id": data[i].streetId, "name": data[i].streetName});
			                }
			                	
		                }
		                window.territoryStreets = territoryStreets;
		                return streets.sort(); // function(a, b){return parseInt(a) - parseInt(b)}
	                }
                },
                getTerritoryBuildings: function(data) {
	                if(data && data.length) {
		                var i=0, buildings = [], territoryBuildings = [];
		                for(i in data) {
			                if(data[i].isApt && (buildings.indexOf(data[i].building) === -1)) {
				                buildings.push(data[i].building);
				                territoryBuildings.push({"id": data[i].streetId, "name": data[i].building});
			                }
		                }
		                window.territoryBuildings = territoryBuildings;
		                return buildings.sort();
	                }
                },
                getStreetId: function(isApt, street, building) {
	                var entityType = (isApt ? building : street),
	                	modelType = (isApt ? window.territoryBuildings : window.territoryStreets), 
	                	m=0;
	                for(m in modelType) {
		                if(entityType == modelType[m].name)
		                	return modelType[m].id;
	                }
                },
                getApiAccess: function (success, error) {
                    $http.get(urls.BASE_API + '/auth-user').success(success).error(error)
                },
                getApiData: function (success, error) {
                    $http.get(urls.BASE_API + '/restricted').success(success).error(error)
                },
                getRecentActivities: function (success, error) {
                    $http.get(urls.BASE_API + '/activities').success(success).error(error)
                },
                getUsers: function (success, error) {
                    $http.get(urls.BASE_API + '/users').success(success).error(error)
                },
                saveUser: function (userId, data, success, error) {
                    $http.post(urls.BASE_API + '/users/' + userId + '/save', data).success(success).error(error)
                },
                attachUser: function (data, success, error) {
                    $http.post(urls.BASE_API + '/publishers/attach-user', data).success(success).error(error)
                },
                deleteUser: function (userId, success, error) {
                    $http.post(urls.BASE_API + '/users/' + userId + '/delete').success(success).error(error)
                },
                getPublishers: function (filter, success, error) {
                    if(filter) 
	                	$http.post(urls.BASE_API + '/publishers/filter', filter).success(success).error(error)
	                else
	                	$http.get(urls.BASE_API + '/publishers').success(success).error(error)
                },
                getPublisher: function (publisherId, success, error) {
                    $http.get(urls.BASE_API + '/publishers/' + publisherId).success(success).error(error)
                },
                addPublisher: function (data, success, error) {
                    $http.post(urls.BASE_API + '/publishers/add', data).success(success).error(error)
                },
                updatePublisher: function (publisherId, data, success, error) {
                    $http.post(urls.BASE_API + '/publishers/' + publisherId + '/save', data).success(success).error(error)
                },
                deletePublisher: function (publisherId, success, error) {
                    $http.post(urls.BASE_API + '/publishers/' + publisherId + '/delete').success(success).error(error)
                },
                getTerritories: function (filter, success, error) {
	                if(filter) 
	                	$http.post(urls.BASE_API + '/territories/filter', filter).success(success).error(error)
	                else
                    	$http.get(urls.BASE_API + '/territories').success(success).error(error)
                },
                getNewTerritories: function (success, error) {
                    $http.get(urls.BASE_API + '/available-territories').success(success).error(error)
                },
                getTerritory: function (territoryId, isAdmin, success, error) {
	                if(isAdmin) 
	                	$http.get(urls.BASE_API + '/territories-all/' + territoryId).success(success).error(error);
	                else	
	                    $http.get(urls.BASE_API + '/territories/' + territoryId).success(success).error(error);
                },
                getTerritoryActivities: function (territoryId, success, error) {
                    $http.get(urls.BASE_API + '/territories/' + territoryId + '/activities').success(success).error(error)
                },
                getAllActivities: function (success, error) {
                    $http.get(urls.BASE_API + '/all-activities').success(success).error(error)
                },
                getTerritoryMap: function (territoryId, success, error) {
	                $http.get(urls.BASE_API + '/territories/' + territoryId + '/map').success(success).error(error);
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
                removeAddress: function (data, success, error) {
                    $http.post(urls.BASE_API + '/addresses/remove/' + data.addressId, data).success(success).error(error)
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
    
    angular.module('app')
        .factory('Language', [ function () {
            return {
	            translate: function(key, defaultText) {
		            return langPacks[lang][key] || defaultText;
	            }
	        }
	    }]);
})();