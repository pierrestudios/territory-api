(function () {
    'use strict';

    angular.module('app')
        .controller('HomeController', ['$rootScope', '$scope', 'settings', '$location', '$localStorage', 'Auth', 'Language',
            function ($rootScope, $scope, settings, $location, $localStorage, Auth, Language) {
	            
	            $scope.Language = Language; 
	            
                function successAuth(res) {
                    $localStorage.token = res.token;
                    setTimeout(function() {
	                    window.location = "/" + settings.site + "#/dashboard";
	                    window.location.reload();
                    }, 1000);
                }

                $scope.signin = function () {
                    var formData = {
                        email: $scope.email,
                        password: $scope.password
                    };

                    Auth.signin(formData, successAuth, function (res) {
                        $rootScope.error = res.message || Language.translate('Invalid Infomation.', 'Invalid Infomation.');
                    })
                };

                $scope.signup = function () {
	                // Validate
	                if ($scope.password != $scope.passwordComfirm ) {
		                $rootScope.error = Language.translate('Password does not match.', 'Password does not match.');
		                return;
	                }
	                
                    var formData = {
                        email: $scope.email,
                        password: $scope.password
                    };
						
                    Auth.signup(formData, successAuth, function (res) {
                        $rootScope.error = res.message || res.error || Language.translate('Failed to sign up.', 'Failed to sign up.');
                    })
                };
 
                $scope.token = $localStorage.token;
                $scope.tokenActive = $scope.token ? 'token-active' : 'token-not-active';
                
                if ($scope.token && $location.$$path == '/') {
	                console.log('$scope.token', $scope.token);
	 				window.location = "/" + settings.site + "#/dashboard";
	                window.location.reload();
 				}   
 				               
            }])

		.controller('ModalController', ['$scope', '$uibModalInstance', 'API', 'entity', 'Notify', 'Language',
        	function ($scope, $uibModalInstance, API, entity, Notify, Language) {
	            $scope.Language = Language; 
				$scope.entity = entity;    
				
				// STORE userType
		        $scope.isAdmin = window.isAdmin;
	            $scope.isManager = window.isManager;              
	            $scope.isEditor = window.isEditor;
	            $scope.userId = window.userId;  
		            
				$scope.closeModal = function () {
				    $uibModalInstance.dismiss('cancel');
				};
				
				$scope.saveUser = function () {
					API.saveUser($scope.entity.userId, {"email": $scope.entity.email, "userType": $scope.entity.type}, function (res) {
						if(res && res.error) {
							Notify.error(res.error, Language.translate('User Not Saved', 'User Not Saved'), "error");
							return;
						}
						window.location.reload();
					});
			    };
			    $scope.deleteUser = function () {
					API.deleteUser($scope.entity.userId, function (res) {
						if(res && res.error) {
							Notify.error(res.error, Language.translate('User Not Deleted', 'User Not Deleted'), "error");
							return;
						}
						window.location.reload();
					});
			    };
			    $scope.deletePublisher = function () {
					API.deletePublisher($scope.entity.publisherId, function (res) {
						if(res && res.error) {
							Notify.error(res.error);
							return;
						}
						window.location.reload();
					});
			    };
			    $scope.removeAddress = function () {
				    // validate note
				    if(!$scope.entity.note) {
					    Notify.error(Language.translate('Enter your reason for removing address', 'Enter your reason for removing address'));
					    return false;
				    }
				    
					API.removeAddress({"addressId": $scope.entity.addressId, "delete" : $scope.entity.confirmDelete, "note": $scope.entity.note }, 
					function (res) {
						if(res && res.error) {
							Notify.error(res.error, Language.translate('Address Not Removed', 'Address Not Removed'), "error");
							return;
						}
						window.location.reload();
					});
			    };
			    $scope.unassignTerritory = function () {
					API.updateTerritory($scope.entity.territoryId, {"publisherId": null, "date": API.formatDateObj(new Date())}, function (res) {
						if(res && res.error) {
							Notify.error(res.error, Language.translate('Territory Not Unassigned', 'Territory Not Unassigned'), "error");
							return;
						}
						window.location.reload();
					});
			    };
			    
	        }])	
	        
        .controller('ApiController', ['$rootScope', '$scope', '$route', 'settings', 'API', '$location', '$localStorage', '$routeParams', '$uibModal', 'Notify', 'Language', '$sce',
        	function ($rootScope, $scope, $route, settings, API, $location, $localStorage, $routeParams, $uibModal, Notify, Language, $sce) {
 				 
	            $scope.Language = Language; 
	            $scope.trust = $sce.trustAsHtml;
				// console.log(settings); 
				 
				$scope.logout = function () {
                    API.logout(function () {
	                    $location.path("signin");
	                    window.location.reload();
                    });
                };
                
                // Chart Data
	            $scope.MorrisData = function(Morris) {
	                $('#morris-area-chart').text('');
	                Morris.Area({
				        element: 'morris-area-chart',
				        data: [{
				            period: '2016-01',
				            records: 168,
				            territories: 4,
				            publishers: 4
				        }, {
				            period: '2015-12',
				            records: 225,
				            territories: 21,
				            publishers: 4
				        }, {
				            period: '2015-11',
				            records: 183,
				            territories: 23,
				            publishers: 7
				        }, {
				            period: '2015-10',
				            records: 171,
				            territories: 23,
				            publishers: 9
				        }, {
				            period: '2015-09',
				            records: 232,
				            territories: 36,
				            publishers: 6
				        }, {
				            period: '2015-08',
				            records: 119,
				            territories: 25,
				            publishers: 6
				        }],
				        xkey: 'period',
				        ykeys: ['territories', 'records', 'publishers'],
				        labels: [Language.translate('Territories', 'Territories'), Language.translate('Reports', 'Reports'), Language.translate('Publishers', 'Publishers')],
				        xLabels: 'month',
				        pointSize: 2,
				        hideHover: 'auto',
				        resize: true
				    });
					
	            };
                
                // Verify token exists then continue
 				if (! $localStorage.token) {
	 				$scope.token = null;
	 				$scope.tokenActive = 'token-not-active';
	 				$location.path("signin");
                    // window.location.reload();
 				} else
 				
				API.getApiAccess(function (res) {
					if (res.data.refreshedToken) 
						$localStorage.token = res.data.refreshedToken;
					
					$scope.token = $localStorage.token;
					$scope.tokenActive = $scope.token ? 'token-active' : 'token-not-active';
		            // console.log('$scope', $scope);
		                            
		            // STORE userType
		            window.isAdmin = (res.data.userType == 'Admin');
		            window.isManager = (window.isAdmin || res.data.userType == 'Manager'); 
		            window.isEditor = (window.isAdmin || window.isManager || res.data.userType == 'Editor'); 
		            window.userId = res.data.userId;
		            
		            $scope.isAdmin = window.isAdmin;
		            $scope.isManager = window.isManager;              
		            $scope.isEditor = window.isEditor;
		            $scope.userId = window.userId;              
		                            
		            // DASHBOARD
		            if ( $location.$$path == '/dashboard') {
		                setTimeout(function() {
		                    $scope.MorrisData(Morris);
		                }, 100);
		                
		                if ($scope.isManager) 
			            API.getRecentActivities(function (res) {
			                $scope.records = res.data.records;
			                $scope.territories = res.data.territories;
			                $scope.publishers = res.data.publishers;
			            });
			            
		            }
		            
		            // ALL USERS
					if ( $location.$$path == '/users') {
						
						API.getUsers(function (res) {
							if(!$('#dataTables-users').is('.dataTable') && res.data && res.data.length) {
								$('#dataTables-users').DataTable({
						            "data": res.data,
						            "columns": [
								        { "data": "publisher" },
								        { "data": "email", "orderable": false },
								        { "data": "userType", "orderable": false },
								        { "data": "publisher", "orderable": false },
								        { "data": "userId", "orderable": false }
								    ],
								    "columnDefs": [{
							            "targets": 0,
							            "data": null,
							            "render": function(data, type, fullObj, meta ) {
									        return data ? '<a href="#/publishers/'+ data.publisherId +'"><strong>' + data.firstName + ' ' + data.lastName + '</strong> <i class="fa fa-arrow-circle-right"></i></a>' : '<a href="" class="btn btn-info attach-publisher" data-user-id="'+ fullObj.userId +'">'+ Language.translate('Attach a Publisher', 'Attach a Publisher') +'</a>';
									    }
							        },{
							            "targets": 3,
							            "data": "publisher",
							            "render": function(publisher) {
								            var data = publisher.territories || null;
								            if(data && data.length) {
									            var assignedBadges = '';
									            for(var t=0; t<data.length; t++) {
										            assignedBadges += '<a href="#/territories/'+ data[t].territoryId +'" class="btn badge '+ (API.passDueTerritory(data[t].date) ? 'badge-danger' : '') +'">' + data[t].number + '</a> ';
										            // console.log('pass due ' + data[t].date, API.passDueTerritory(data[t].date));
									            } 
										        return assignedBadges;
								            }
								            // http://datatables.net/manual/tech-notes/4
								            // If using columns.render or columns.data ensure that they are returning a value
								            return '';
									    }
							        },{
							            "targets": 4,
							            "data": "userId",
							            "render": function(data, type, fullObj) {
									        return '<a class="btn btn-info btn-sm edit-user" href="" data-user-id="' + data + '" data-user-email="'+ fullObj.email +'" data-user-type="'+ fullObj.userType +'">' + Language.translate('Edit', 'Edit') + '</a> ' + (window.isAdmin ? ' <a class="btn btn-sm btn-danger delete-user" data-user-id="' + data + '" data-user-email="'+ fullObj.email +'">' + Language.translate('Delete', 'Delete') + '</a>' : '');
									    }
									}],
						            searching: false,
						            paging: false,
						            responsive: true
						        });
						        
						        $('.attach-publisher').on('click', function(e){
							        e.preventDefault();
							        e.stopPropagation();
							        $('#btnAttachUser').attr('data-user-id', $(this).data('user-id'));
							        $('#attachUser #user-email').text($(this).data('user-email'));
							        
						        	$('#targetAttachUser').trigger('click');
						        });
						        
						        $('.edit-user').on('click', function(e){
							        e.preventDefault();
							        e.stopPropagation();
							        $('#btnAttachUser').attr('data-user-id', $(this).data('user-id'));
							        $('#attachUser #user-email').text($(this).data('user-email'));
							        
						        	$scope.editUser({"userId": $(this).data('user-id'), "email": $(this).data('user-email'), "type": $(this).data('user-type')});
						        });
						        
						        $('.delete-user').on('click', function(e){
							        e.preventDefault();
							        e.stopPropagation();
							        
							        $scope.deleteUser({"userId": $(this).data('user-id'), "email": $(this).data('user-email')});
							    });
							    
							    $('#dataTables-users tr td:first-child').on('click', function(e) {
							        console.log('#dataTables-users');
							        
							        setTimeout(function() {
								        $('.attach-publisher').on('click', function(e){
									        e.preventDefault();
									        e.stopPropagation();
									        $('#btnAttachUser').attr('data-user-id', $(this).data('user-id'));
									        $('#attachUser #user-email').text($(this).data('user-email'));
									        
								        	$('#targetAttachUser').trigger('click');
								        });
								        
								        $('.edit-user').on('click', function(e){
									        e.preventDefault();
									        e.stopPropagation();
									        $('#btnAttachUser').attr('data-user-id', $(this).data('user-id'));
									        $('#attachUser #user-email').text($(this).data('user-email'));
									        
								        	$scope.editUser({"userId": $(this).data('user-id'), "email": $(this).data('user-email'), "type": $(this).data('user-type')});
								        });
								        
								        $('.delete-user').on('click', function(e){
									        e.preventDefault();
									        e.stopPropagation();
									        
									        $scope.deleteUser({"userId": $(this).data('user-id'), "email": $(this).data('user-email')});
									    });
								        
								    }, 500 );
								    
								});    
						        
							}
							
							if(res.data && res.data.length)
								API.getPublishers({"userId": null}, function (res) {
							        $scope.publishers = res.data;
							    }); 
							   
							var modalInstance = null;
							    
							$scope.editUser = function (data) {
								$scope.user = data;
							    window.modalInstance = $uibModal.open({
							      	animation: true,
							      	templateUrl: settings.theme + 'partials/edit-user.html',
							      	controller: 'ModalController',
							      	backdrop: 'static',
							      	resolve: {
							        	entity: function() {
								        	return $scope.user
								        }
							      	}
							    });
							};
							
							$scope.deleteUser = function (data) {
								$scope.user = data;
							    window.modalInstance = $uibModal.open({
							      	animation: true,
							      	templateUrl: settings.theme + 'partials/delete-user.html',
							      	controller: 'ModalController',
							      	backdrop: 'static',
							      	resolve: {
							        	entity: function() {
								        	return $scope.user
								        }
							      	}
							    });
							};
							 					  	    
						});
						
						$scope.attachUser = function () {
							API.attachUser({"userId": $('#btnAttachUser').attr('data-user-id'), "publisherId": $scope.publisher}, function (res) {
								if(res && res.error) {
								Notify.error(res.error, Language.translate('User Not Attached', 'User Not Attached'), "error");
									return;
								}
								window.location.reload();
							});
					    }; 
		            }
		                
					// PUBLISHER
					if ($routeParams.publisherId) {
						var elem = document.querySelector('.js-switch');
						if (! $(elem).data('switchery'))
							var init = new Switchery(elem);
							
						API.getPublisher($routeParams.publisherId, function(res) {
							$scope.publisher = res.data;
						});
						
						API.getNewTerritories(function (res) {
							$scope.territories = res.data;
						});
						
						$scope.updatePublisher = function () {
							console.log($scope.publisher.firstName, $scope.publisher.lastName);
							API.updatePublisher($scope.publisher.publisherId, {"firstName": $scope.publisher.firstName, "lastName": $scope.publisher.lastName}, function (res) {
								if(res && res.error) {
									Notify.error(res.error, Language.translate('Publisher Not Updated', 'Publisher Not Updated'), "error");
									return;
								}
								window.location.reload();
							});
					    };
					    $scope.assignTerritory = function () {
							API.updateTerritory($scope.newTerritory.terrSelected, {"publisherId": $scope.publisher.publisherId, "date": API.formatDateObj($scope.newTerritory.date)}, function (res) {
								if(res && res.error) {
									Notify.error(res.error, Language.translate('Territory Not Assigned', 'Territory Not Assigned'), "error");
									return;
								}
								window.location.reload();
								$scope.newTerritory.terrSelected = '';
								$scope.newTerritory.date = '';
							});
					    }; 
					    $scope.unassignTerritory = function (territory) {
						    $scope.territory = territory;
						    window.modalInstance = $uibModal.open({
						      	animation: true,
						      	templateUrl: settings.theme + 'partials/unassign-territory.html',
						      	controller: 'ModalController',
						      	backdrop: 'static',
						      	resolve: {
						        	entity: function() {
							        	return $scope.territory
							        }
						      	}
						    });
					    };
					    
						$scope.isPassDue = function(date) {
							return API.passDueTerritory(date);
						};
						
						$scope.isPassDueClass = function(date) {
							return API.passDueTerritory(date) ? ' label-danger' : ' not';
						}; 
					}
					
					// ALL PUBLISHERS
					if ( $location.$$path == '/publishers') {
						
						API.getPublishers(null, function (res) {
							if(!$('#dataTables-publishers').is('.dataTable') && res.data && res.data.length) {
								$('#dataTables-publishers').DataTable({
						            "data": res.data,
						            "columns": [
								        { "data": "name" },
								        { "data": "publisherType", "orderable": false },
								        { "data": "territories", "orderable": false },
								        { "data": "publisherId", "orderable": false }
								    ],
								    "columnDefs": [{
							            "targets": 0,
							            "data": null,
							            "render": function(data, type, fullObj, meta ) {
									        return fullObj.firstName + ' ' + fullObj.lastName;
									    }
							        },{
							            "targets": 2,
							            "data": "territories",
							            "render": function(data) {
								            var assigned = [];
								            if(data && data.length) {
									            var assignedBadges = '';
									            for(var t=0; t<data.length; t++) {
										            assigned.push(data[t].number);
										            assignedBadges += ' <a href="#/territories/'+ data[t].territoryId +'" class="btn badge '+ (API.passDueTerritory(data[t].date) ? 'badge-danger' : '') +'" >' + data[t].number + '</a> ';
										            // console.log('pass due ' + data[t].date, API.passDueTerritory(data[t].date));
									            }
									            
										        // return assigned.join(', ');
										        return assignedBadges;
								            }
								            // http://datatables.net/manual/tech-notes/4
								            // If using columns.render or columns.data ensure that they are returning a value
								            return '';
									    }
							        },{
							            "targets": 3,
							            "data": "publisherId",
							            "render": function(data, type, fullObj, meta ) {
									        return '<a class="btn btn-info btn-sm" href=#/publishers/' + data + '>' + Language.translate('Edit', 'Edit') + '</a> ' + (window.isAdmin ? '<a class="btn btn-sm btn-danger delete-publisher" data-publisher-id="' + data + '" data-publisher-name="' + fullObj.firstName + ' '+ fullObj.lastName + '">' + Language.translate('Delete', 'Delete') + '</a>' : '');
									    }
									}],
						            searching: false,
						            paging: false,
						            // scrollY: 400,
						            responsive: true
						        });
						        
						        $('.delete-publisher').on('click', function(e){
							        e.preventDefault();
							        e.stopPropagation();
							        
							        $scope.deletePublisher({"publisherId": $(this).data('publisher-id'), "name": $(this).data('publisher-name')});
							    });
							    
							    $('#dataTables-publishers tr td:first-child').on('click', function(e) {
							        console.log('#dataTables-publishers');
							        
							        setTimeout(function() {
								        $('.delete-publisher').on('click', function(e){
									        e.preventDefault();
									        e.stopPropagation();
									        
									        $scope.deletePublisher({"publisherId": $(this).data('publisher-id'), "name": $(this).data('publisher-name')});
									    });
								    }, 500);
								});        
							}
						});
						
						$scope.addPublisher = function () {
							API.addPublisher({"firstName": $scope.publisher.firstName, "lastName": $scope.publisher.lastName}, function (res) {
								if(res && res.error) {
									Notify.error(res.error, Language.translate('Publisher Not Added', 'Publisher Not Added'), "error");
									return;
								}
								window.location.reload();
							});
					    };
					    
					    $scope.deletePublisher = function (data) {
							$scope.publisher = data;
						    window.modalInstance = $uibModal.open({
						      	animation: true,
						      	templateUrl: settings.theme + 'partials/delete-publisher.html',
						      	controller: 'ModalController',
						      	backdrop: 'static',
						      	resolve: {
						        	entity: function() {
							        	return $scope.publisher
							        }
						      	}
						    });
						};
				        
		            }
		            
		            // TERRITORY
		            if ($routeParams.territoryId) {
			            
			            // console.log('$route', $route);
			            
			            // Load Map
			            if ($route.current.$$route.map) {
											             
							API.getTerritoryMap($routeParams.territoryId, function(res) {
								if(res.data) {
									$scope.territory = res.data.territory;
									$scope.mapdata = window.mapdata = res.data.map;
									
									
									// Google Maps Api App
									$scope.initializeMap = function () {
										$('#territory-map-display').css('height', ($(window).height() - 140))
										
									    // isMapInitialized=true; 
									    
									    
									    //******* Now draw boudaries **********
											
										map = new google.maps.Map(document.getElementById("territory-map-display"), mapOptions);	
										
									    var boundary = [];
									    var bounds = new google.maps.LatLngBounds();
								  	
									  	var colors = {
										  	orange: '#fb8c00',
										  	orangeLite: '#FFE8CE'
									  	}
									  	
									  	// Load the saved boundaries data
									  	var savedPoly = res.data.territory.boundaries; 
									  	
									  	if(savedPoly == '[]') savedPoly = '';
									  	// console.log('savedPoly', savedPoly);
									  	
									  	// Construct the polygon.
										var terrCoordinates = new google.maps.Polygon({
										    // paths: JSON.parse(savedPoly),
										    strokeColor: colors.orange,
										    strokeWeight: 5,
										    fillColor: colors.orangeLite,
										    fillOpacity: 0.5,
										    editable: true,
											zIndex: 1
										});
										
										if(savedPoly) {
											boundary = JSON.parse(savedPoly);
											terrCoordinates.setPaths(boundary);
											
											// now fit the map to the newly inclusive bounds
											terrCoordinates.getPath().forEach(function(Latlng, number) {
											  	bounds.extend(Latlng);
										  	});
										  	
											map.fitBounds(bounds);
										}	
										
										terrCoordinates.setMap(map);
										
									    /***** Add Markers ********/
									
									    if(window.mapdata && window.mapdata[0]) {
											
											var centerLatlng = new google.maps.LatLng(window.mapdata[0].lat, window.mapdata[0].long),
												bounds = new google.maps.LatLngBounds();
											
										    var mapOptions = {
										        zoom: 18,
										        center: centerLatlng
										    }
									
										    var markers = window.mapdata;
										    
										    var m = 0;
										    
										    for(m in markers) {
										        markers[m].myLatlng = new google.maps.LatLng(window.mapdata[m].lat, window.mapdata[m].long);
										        var markerColor = google.maps.geometry.poly.containsLocation(markers[m].myLatlng, terrCoordinates) ? 'blue' : 'red';
										        markers[m].marker = $scope.createMarker(map, markers[m], markerColor);
										        
												bounds.extend(markers[m].myLatlng);
											
												infowindow = new google.maps.InfoWindow();
										
												markers[m].marker.addListener('click', function(e) {
													infowindow.setContent(this.title);
													infowindow.open(map, this);
												});
										    }
										    
											map.fitBounds(bounds);
										    
									    }
									     
									}
									
									$scope.createMarker = function(map, data, markerColor) {
										var marker = new google.maps.Marker({
									        position: new google.maps.LatLng(data.lat, data.long),
									        map: map,
									        title: data.name + ' - ' + data.address,
									        id: data.id,
									        animation: google.maps.Animation.DROP,
									        icon: {
										        path: google.maps.SymbolPath.CIRCLE,
										        fillColor: markerColor,
										        fillOpacity: .62,
										        strokeColor: 'white',
										        strokeWeight: 2.5,
										        scale: 10
										    }
										});
										
										return marker;
									}
									
									$scope.initTrackUser = function(geoLoc) {
									    if (geoLoc) {
									        geoLoc.getCurrentPosition(function(position) {
											    $scope.setUserLocation(position); 
											    $scope.watchCurrentPosition(geoLoc);
											}, $scope.logError, {
									            enableHighAccuracy : true,
									            timeout : 60000,
									            maximumAge : 0
									        });
									    } else {
									        alert(Language.translate('Your device does not support the Geolocation API', 'Your device does not support the Geolocation API'));
									    }
									}
									 
									$scope.setUserLocation = function(pos) {
									    userLocation = new google.maps.Marker({
									       map : map,
									       position : new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude),
									       title : "You are here",
									       optimized: false,
									       icon : settings.theme + 'images/marker-user.gif',
									    }); 
									    
									    console.log('userLocation', userLocation);
									    
										// scroll to userLocation
										map.panTo(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));
									}
									
									$scope.watchCurrentPosition = function(geoLoc) {
									    positionTimer = geoLoc.watchPosition(function(position) {
									        $scope.setMarkerPosition(userLocation, position);
									        map.panTo(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
									    });
									}
									
									$scope.setMarkerPosition = function(marker, position) {
									     marker.setPosition(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
									}
									
									$scope.logError = function(error){
										console.log('error', error)
									}
 									 
								    var map, geocoder, infowindow, tracking, positionTimer, geoLoc, userLocation;
								    
								    $scope.initializeMap();
								     
								    // Add user marker
								    $('#track-user').on('click', function(e) {
									    e.preventDefault();
									    
									    geoLoc = navigator.geolocation;
								
									    if(!tracking) {
										    $scope.initTrackUser(geoLoc);
										    tracking = true;
										    $(this).addClass('green').find('span').text("Tracking: ON");
									    }  else {
										    geoLoc.clearWatch(positionTimer);
										    userLocation.setMap(null);
										    tracking = false;
										    $(this).removeClass('green').find('span').text("Track");
									    } 
									    
								    });
									 
								}
								
							});
													            
			            } else {
				            
				            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

							elems.forEach(function(html) {
								if (! $(html).data('switchery'))
								var switchery = new Switchery(html);
							});
							 
								
							API.getTerritory($routeParams.territoryId, window.isAdmin, function(res) {
								if(res.data) {
									$scope.territory = window.territory = res.data;
									if($scope.territory.publisher && $scope.territory.publisherId)
										$scope.territory.publisher.urlLink = '#/publishers/' + $scope.territory.publisherId;
									$scope.territory.pdfLink = '/pdf/' + $scope.territory.number;	
									$scope.territory.mapLink = '#/territories/map/' + $scope.territory.territoryId;	
									$scope.territory.buildings = API.getTerritoryBuildings(res.data.addresses);
									$scope.territory.streets = API.getTerritoryStreets(res.data.addresses);
									
									// console.log('streets', window.territoryStreets);
									// console.log('buildings', window.territoryBuildings);
									// console.log('$scope.territory.buildings', $scope.territory.buildings);
									// console.log('$scope.territory.streets', $scope.territory.streets);
								}
									 
								if(!$('#dataTables-addresses').is('.dataTable') && res.data.addresses && res.data.addresses.length) {	
									var table = $('#dataTables-addresses').DataTable({
							            "data": res.data.addresses,
							            "columns": [
									        { "data": "name" },
									        { "data": "address" },
									        { "data": "streetName"},
									        { "data": "phone", "orderable": false },
									        { "data": "notes", "orderable": false},
									        { "data": "addressId", "orderable": false}
									    ],
									    "columnDefs": [{ 
											"orderData": 2, 
											"targets": 1,
											"data": "address",
								            "render": function(data, type, fullObj) {								            
									            if (fullObj.isApt) {
										            return fullObj.building + ', ' + (!fullObj.address.match(/ap/i) ? 'Apt ' : '') + fullObj.address;
									            }
										        return fullObj.address + (fullObj.apt ? ' (' + fullObj.apt + ') ' : '') + ' ' + fullObj.streetName;
										    }
										},
									    {
								            "targets": 4,
								            "data": "notes",
								            "render": function(data, type, fullObj) {
									            var isOwner = false;
									            var notes = (window.isEditor ? '<li class="list-group-item"><button class="btn btn-success btn-sm badge badge-success add-note" data-address-id="' + fullObj.addressId + '">' + Language.translate('Add Notes', 'Add Notes') + '</button> &nbsp; </li>' : '');
									            for(var n in data) {
										            isOwner = (data[n].userId == window.userId);
										            if(n < 5) notes += '<li class="list-group-item">'+ data[n].note + (data[n].date != '0000-00-00' ? ' <small class="label label-default">'+ data[n].date  +'</small>' : '') + ( isOwner ? ' <button class="btn btn-info btn-sm edit-note" data-note-id="' + data[n].noteId + '" data-note-note="' + data[n].note + '" data-note-date="' + data[n].date + '">'+ Language.translate('Update Notes', 'Update Notes') +'</button></li>' : ''); 
									            }
										        return '<ul class="list-group">' + notes + '</ul>'; // <div class="btn-group flex"><a class="btn btn-info btn-sm" href=#/addresses/' + data + '>' + '<i class="fa fa-edit"></i>' + '</a> <a class="btn btn-danger btn-sm" href=#/addresses/' + data + '>' + '<i class="fa fa-trash"></i>' + '</a></div>';
										    }
										},{
								            "targets": 5,
								            "data": "addressId",
								            "render": function(data, type, fullObj, meta ) {
									            var isInActive = '';
									            if (fullObj.inActive && window.isAdmin) {
										            isInActive = '<button class="btn btn-sm btn-default edit-address" data-address-id="' + data + '" data-address-address="'+ fullObj.address +'" title="'+ Language.translate('Make Active', 'Make Active') +'">' + '<i class="fa fa-eye"></i>' + '</button>';
									            }
										        return (window.isEditor ? ' <button class="btn btn-info btn-sm edit-address" title="'+ Language.translate('Edit Address', 'Edit Address') +'" data-address-id="' + data.addressId + '"><i class="fa fa-edit"></i></button> ' + (isInActive ? isInActive : '<button class="btn btn-sm btn-danger delete-address" data-address-id="' + data + '" data-address-address="'+ fullObj.address +'" title="'+ Language.translate('Remove Address', 'Remove Address') +'">' + '<i class="fa fa-times"></i>' + '</button>') : '');
										    }
										}],
										"order": [[ 1, 'asc' ]],
							            searching: false,
							            paging: false,
							            responsive: true
							        });
							        
							        $('.edit-address').on('click', function(e){
								        e.preventDefault();
								        var address = res.data.addresses[table.row(this.parentNode.parentNode).index()];
								        // console.log(address);
								        
								        $('#btnUpdateAddress').attr('data-address-id', address.addressId);
								        $('input[ng-model="editTerritoryAddress.name"]').val(address.name);
								        $('input[ng-model="editTerritoryAddress.phone"]').val(address.phone);
								        $('input[ng-model="editTerritoryAddress.address"]').val(address.address);
								        $('input[ng-model="editTerritoryAddress.apt"]').val(address.apt);
								        $('input[ng-model="editTerritoryAddress.lat"]').val(address.lat);
								        $('input[ng-model="editTerritoryAddress.long"]').val(address.long);
								        $('select[ng-model="editTerritoryAddress.inactive"]').val(address.inActive);
								        $('input[ng-model="editTerritoryAddress.isApt"]').val(address.isApt);
								        
								        // is-apt
								        if(address.isApt) {
									        $('input[ng-model="editTerritoryAddress.address"]').val(address.address.replace('APT ', ''));
									        // console.log($('input[ng-model="editTerritoryAddress.address"]').val());
									        $('select[ng-model="editTerritoryAddress.building"]').val(address.building);
									        $('.is-apt').show();
									        $('.is-street').hide();
								        } else {
									        $('select[ng-model="editTerritoryAddress.street"]').val(address.streetName);
									        $('.is-apt').hide();
									        $('.is-street').show();
									        
									        // duplex
	/*
									        if(address.apt) {
										        $('.is-duplex').show();
									        } else {
										        $('.is-duplex').hide();
									        }
	*/
								        }
								        	
								        	
							        	$('#targetEditAddress').trigger('click');
							        });
							        
							        $('.delete-address').on('click', function(e){ 
								        e.preventDefault();
								        var address = res.data.addresses[table.row(this.parentNode.parentNode).index()];
								        // console.log(address);
								        $scope.removeAddress(address);
								    });
								    
								    $('#dataTables-addresses').on('.add-note', 'click', function(e){
								        e.preventDefault();
								        $('#btnSaveNote').attr('data-address-id', $(this).data('address-id'));
								        $('#btnSaveNote').attr('data-note-id', '');
								        $('input[ng-model="editNote.note"]').val('');
								        $('input[ng-model="editNote.date"]').val('');
								        
							        	$('#targetEditNote').trigger('click');
							        });
									        
							        $('#dataTables-addresses.dtr-inline.collapsed tr>td:first-child').on('click', function(e) {
								        // console.log('#dataTables-addresses');
								        
								        setTimeout(function() {
									        // $('#dataTables-addresses tr td ul button')
									        // console.log( $('#dataTables-addresses .add-note'));
									        
									        $('#dataTables-addresses .add-note').on('click', function(e){
										        e.preventDefault();
										        e.stopPropagation();
										        $('#btnSaveNote').attr('data-address-id', $(this).data('address-id'));
										        $('#btnSaveNote').attr('data-note-id', '');
										        $('input[ng-model="editNote.note"]').val('');
										        $('input[ng-model="editNote.date"]').val('');
										        
									        	$('#targetEditNote').trigger('click');
									        });
									        
									        $('#dataTables-addresses .edit-note').on('click', function(e){
										        e.preventDefault();
										        e.stopPropagation();
										        $('#btnSaveNote').attr('data-note-id', $(this).data('note-id'));
										        $('input[ng-model="editNote.note"]').val($(this).data('note-note'));
										        $('input[ng-model="editNote.date"]').val($(this).data('note-date'));
										        
									        	$('#targetEditNote').trigger('click');
									        });
									        
									    }, 500);
								        
							        });
							        						        
	/*
							        table.on('responsive-display', function ( e, datatable, row, showHide, update ) {
									    console.log( 'Details for row '+row.index()+' '+(showHide ? 'shown' : 'hidden') );
									});
	*/
							        
							        $('.add-note').on('click', function(e){
								        e.preventDefault();
								        e.stopPropagation();
								        $('#btnSaveNote').attr('data-address-id', $(this).data('address-id'));
								        $('#btnSaveNote').attr('data-note-id', '');
								        $('input[ng-model="editNote.note"]').val('');
								        $('input[ng-model="editNote.date"]').val('');
								        
							        	$('#targetEditNote').trigger('click');
							        });
							         
							        $('.edit-note').on('click', function(e){
								        e.preventDefault();
								        $('#btnSaveNote').attr('data-note-id', $(this).data('note-id'));
								        $('input[ng-model="editNote.note"]').val($(this).data('note-note'));
								        $('input[ng-model="editNote.date"]').val($(this).data('note-date'));
								        
							        	$('#targetEditNote').trigger('click');
							        });
							        
							        $('.delete-note').on('click', function(e){ 
								        e.preventDefault();
								        var note = res.data.addresses[table.row(this.parentNode.parentNode).index()];
								        console.log(address);
								        $scope.removeAddress(address.addressId);
								    });
							         
								    
								} else if(!$('#dataTables-addresses').is('.dataTable')) {
									$('#dataTables-addresses').addClass('dataTable').hide().after('No addresses found.');
								}
								
							    if($.mask) {
							        $.mask.definitions['~'] = "[+-]"; 
							        $(".maskPhone").mask("(999) 999-9999");
							    }
							});  
							$scope.updateTerritory = function () {
								API.updateTerritory($scope.territory.territoryId, {"location": $scope.territory.location, "cityState": $scope.territory.cityState}, function (res) {
									if(res && res.error) {
									Notify.error(res.error, Language.translate('Territory Not Updated', 'Territory Not Updated'), "error");
										return;
									}
									window.location.reload();
								});
						    };
						    $scope.addAddress = function () {
							    var notes = $scope.newAddress.notes ? [{
									"note": $scope.newAddress.notes,
									"date": API.formatDateObj($scope.newAddress.date)
								}] : null;
								
								var street = ($scope.newAddress.isApt !== 1 && $scope.newAddress.street == 'new-street' || $scope.newAddress.isApt == 1 && $scope.newAddress.building == 'new-building') ? [{
									"street": ($scope.newAddress.isApt == 1 ? $scope.newAddress.newBuilding : $scope.newAddress.newStreet),
									"isAptBuilding": $scope.newAddress.isApt == 1
								}] : null;
								// console.log('$scope.newAddress', $scope.newAddress);
								API.addAddress($scope.territory.territoryId, 
								{
									"name": $scope.newAddress.name,
									"address": $scope.newAddress.address,
									"apt": $scope.newAddress.apt,
									"lat": $scope.newAddress.lat,
									"long": $scope.newAddress.long,
									"phone": $scope.newAddress.phone,
									"streetId": ($scope.newAddress.street != 'new-street') ? API.getStreetId($scope.newAddress.isApt, $scope.newAddress.street, $scope.newAddress.building) : '',
									"notes": notes,
									"street": street
								}, 
								function (res) {
									if(res && res.error) {
									Notify.error(res.error, Language.translate('Address Not Added', 'Address Not Added'), "error");
										return;
									}
									window.location.reload();
								});
						    };
						    $scope.updateAddress = function () {
								API.updateAddress($scope.territory.territoryId, $('#btnUpdateAddress').attr('data-address-id'),
								{
									"name": $('input[ng-model="editTerritoryAddress.name"]').val(),
									"address": $('input[ng-model="editTerritoryAddress.address"]').val(),
									"apt": $('input[ng-model="editTerritoryAddress.apt"]').val(),
									"streetId": API.getStreetId($('input[ng-model="editTerritoryAddress.isApt"]').val(), $('select[ng-model="editTerritoryAddress.street"]').val(), $('select[ng-model="editTerritoryAddress.building"]').val()),
									"phone": $('input[ng-model="editTerritoryAddress.phone"]').val(),
									"inActive": $('select[ng-model="editTerritoryAddress.inactive"]').val()
								}, 
								function (res) {
									if(res && res.error) {
									Notify.error(res.error, Language.translate('Address Not Updated', 'Address Not Updated'), "error");
										return;
									}
									window.location.reload();
								});
						    };
						    $scope.removeAddress = function (data) {
								$scope.address = data;
							    window.modalInstance = $uibModal.open({
							      	animation: true,
							      	templateUrl: settings.theme + 'partials/delete-address.html',
							      	controller: 'ModalController',
							      	backdrop: 'static',
							      	resolve: {
							        	entity: function() {
								        	return $scope.address
								        }
							      	}
							    });
						    };
						    $scope.saveNote = function () {
							    if(! $('#btnSaveNote').attr('data-note-id') && $('#btnSaveNote').attr('data-address-id')) {
									API.addNote($scope.territory.territoryId, $('#btnSaveNote').attr('data-address-id'),
									{
										"note": $('input[ng-model="editNote.note"]').val(),
										"date": $('input[ng-model="editNote.date"]').val(),
									}, 
									function (res) {
										if(res && res.error) {
										Notify.error(res.error, Language.translate('Note Not Saved', 'Note Not Saved'), "error");
											return;
										}
										window.location.reload();
									});
								} else {	
									API.updateNote($scope.territory.territoryId, $('#btnSaveNote').attr('data-note-id'),
									{
										"note": $('input[ng-model="editNote.note"]').val(),
										"date": $('input[ng-model="editNote.date"]').val(),
									}, 
									function (res) {
										if(res && res.error) {
										Notify.error(res.error, Language.translate('Note Not Saved', 'Note Not Saved'), "error");
											return;
										}
										window.location.reload();
									});
								}
						    };
						    $scope.deleteNote = function (noteId) {
								API.deleteNote(noteId, 
								function (res) {
									if(res && res.error) {
										Notify.error(res.error, Language.translate('Note Not Deleted', 'Note Not Deleted'), "error");
										return;
									}
									window.location.reload();
								});
						    };
			            }
			            
						
					}
		            
		            // ALL TERRITORIES
		            if ( $location.$$path == '/territories') {
				       
				       var filter = '';
				       if (!$scope.isManager) filter = {'userId': $scope.userId};
				        API.getTerritories(filter, function (res) {
					        if(res && res.error) {
								Notify.error(res.error);
								return;
							}
							if(!$('#dataTables-territories').is('.dataTable') && res.data && res.data.length) {
								$('#dataTables-territories').DataTable({
						            "data": res.data,
						            "columns": [
								        { "data": "number" },
								        { "data": "location", "orderable": false },
								        { "data": "date", "orderable": false },
								        { "data": "territoryId", "orderable": false }
								    ],
								    "columnDefs": [{
							            "targets": 0,
							            "data": "number",
							            "render": function(data) {
									        return '<span class="btn badge">' + data + '</span>';
									    }
									},{
							            "targets": 2,
							            "data": "date",
							            "render": function(data, type, fullObj) {
								            var due = API.passDueTerritory(data), old = API.isOldTerritory(data);
								            if(fullObj.publisherId)
									        	return '<span class="badge badge-success '+ (due ? 'badge-danger' : '') +'">' + (data != '0000-00-00' ? data : '') + '</span>' +  (window.isManager ? ' <span class="badge badge-info">'+ (fullObj.publisher ? fullObj.publisher.firstName + ' ' + fullObj.publisher.lastName : Language.translate('Yes', 'Yes')) +'</span> ' : (due ? ' <span class="badge badge-danger">'+ Language.translate('Overdue', 'Overdue') +'</span>' : '')); // <span class="badge badge-danger">Anreta</span>
									        else 
									        	return '<span class="badge '+ (old ? 'badge-primary' : '') +'">' + (data != '0000-00-00' ? data : '') + '</span> '; // <span class="badge">Non</span>
									    }
									},{
							            "targets": 3,
							            "data": "territoryId",
							            "render": function(data, type, fullObj, meta ) {
									        return '<a class="btn btn-info btn-sm" href=#/territories/' + data + '>' + Language.translate('Details', 'Details') + '</a>';
									    }
									}],
						            searching: false,
						            paging: false,
						            // scrollY: 400,
						            responsive: true
						        });
							} else {
								$('#dataTables-territories').hide();
							}
						});
						
						$scope.addTerritory = function () {
							API.addTerritory({"location": $scope.territory.location, "number": $scope.territory.number}, function (res) {
								window.location.reload();
							}, function(err) {
								if(err) {
									Notify.error(err);
								}
							});
					    };
		            }
		            
		            
		            // ALL ACTIVITIES
		            if ( $location.$$path == '/activities') {
					
						if (!window.chartDone) {
							
							window.chartDone = true;
							
							API.getAllActivities(function(res) {
								if(res && res.error) {
									Notify.error(res.error);
									return;
								}
								if(res.data) {
/*
									 
							        var timeline;
							        
							        // Build Chart data
							        var total = res.data.length, i = 0, r = 0, group = {}, groups = new vis.DataSet(), items = new vis.DataSet();
   
							        for(i=0; i < total; i++) {
								        // console.log('terr', res.data[i].number);
								        
								        group = {id: i, content: '<div class="territory-num">'+ res.data[i].number +'</div>' };
								        groups.add(group);
								        
								        if(res.data[i].records.length) {
											 
									        // activityType
									        var checkin = null, checkout = null, endDate = null;
									        for(r in res.data[i].records) {
										        // console.log('res.data[i].records', r);
										        
										        if(res.data[i].records[r].activityType == "checkout") {
											        
											        endDate = ( (res.data[i].records[parseInt(r + 1)] &&  res.data[i].records[parseInt(r + 1)].activityType == "checkin" && res.data[i].records[r].publisherId == res.data[i].records[parseInt(r + 1)].publisherId) ? API.formatDateStrToObj(res.data[i].records[parseInt(r + 1)].date) : null);
											        
											        items.add({
												      id: i + r,
												      group: i,
												      content: '<div class="publisher"> <span class="territory-num">'+ res.data[i].number +'</span> <span class="name">'+ res.data[i].records[r].publisher.firstName +' '+ res.data[i].records[r].publisher.lastName + '</span> <span class="territory-date checkout">'+ res.data[i].records[r].date +'</span> ' + (endDate ? ' <span class="territory-span"></span><span class="territory-date checkin">'+ res.data[i].records[parseInt(r + 1)].date +'</span> ' : '') + '</div>',
												      start: API.formatDateStrToObj(res.data[i].records[r].date),
												      end: endDate,
												      type: (endDate ? 'range' : 'box')
												    });
										        
											        // console.log(res.data[i].records[r].activityType, res.data[i].records[r].date);
											    
											        // if(res.data[i].records[parseInt(r + 1)]) 
											        	// console.log(res.data[i].records[parseInt(r + 1)].activityType, res.data[i].records[parseInt(r + 1)].date);
										        }
										        
									        }
								        }  
								        
							        }
							        
							        // create visualization
									var container = document.getElementById('territory-activities'), options = {
									    groupOrder: 'id'  // groupOrder can be a property name or a sorting function
									};
									
									// console.log('groups', groups);
									// console.log('items', items); 
									
									timeline = new vis.Timeline(container);
									timeline.setOptions(options);
									timeline.setGroups(groups);
									timeline.setItems(items);
*/

									// D3 Timeline
									
									var labelTestData1 = [
								        {label: "person a", times: [
									        {"starting_time": 1355752800000, "ending_time": 1355759900000}, 
									        {"starting_time": 1355767900000, "ending_time": 1355774400000}
									    ]},
								        {label: "person b", times: [{"starting_time": 1355759910000, "ending_time": 1355761900000}, {"starting_time": 1355771900000, "ending_time": 1355781900000}]},
								        {label: "person c", times: [{"starting_time": 1355761910000, "ending_time": 1355763910000}]},
								    ];
								 
								 
									var labelTestData2 = [
								        {label: "person a", times: [
									        {"starting_time": API.formatDateStrToObj('2016-1-12'), "ending_time": API.formatDateStrToObj('2016-1-15')}, 
									        {"starting_time": API.formatDateStrToObj('2016-1-11'), "ending_time": API.formatDateStrToObj('2016-1-22')}
									    ]},
								        {label: "person b", times: [{"starting_time": API.formatDateStrToObj('2016-1-02'), "ending_time": API.formatDateStrToObj('2016-1-12')}, {"starting_time": API.formatDateStrToObj('2016-1-15'), "ending_time": API.formatDateStrToObj('2016-1-22')}]},
								        {label: "person d", times: [{"starting_time": API.formatDateStrToObj('2015-02-02'), "ending_time": API.formatDateStrToObj('2015-11-02')}]},
								        {label: "person e", times: [{"starting_time": API.formatDateStrToObj('2015-09-02'), "ending_time": API.formatDateStrToObj('2016-1-02')}]},
								        {label: "person f", times: [{"starting_time": API.formatDateStrToObj('2015-11-02'), "ending_time": API.formatDateStrToObj('2016-2-02')}]},
								        {label: "person g", times: [{"starting_time": API.formatDateStrToObj('2015-10-02'), "ending_time": API.formatDateStrToObj('2016-1-02')}]},
								        {label: "person h", times: [{"starting_time": API.formatDateStrToObj('2015-11-02'), "ending_time": API.formatDateStrToObj('2016-1-02')}]},
								    ];
								 
								 
								
								    var width = 3200;
								       
								    var timelineHover = function() {
								        var chart = d3.timeline()
								          .width(width)
								          .stack()
								          .margin({left:70, right:30, top:0, bottom:0})
								          .hover(function (d, i, datum) {
								          // d is the current rendering object
								          // i is the index during d3 rendering
								          // datum is the id object
								            var div = $('#hoverRes');
								            var colors = chart.colors();
								            div.find('.coloredDiv').css('background-color', colors(i))
								            div.find('#name').text(datum.label);
								          })
								          .click(function (d, i, datum) {
								            console.log(datum.label);
								          })
								          
								          // .navigate(navigateBackwards, navigateForwards)
								          // navigateBackwards(beginning, chartData)
								          
								          .scroll(function (x, scale) {
								            $("#scrolled_date").text(scale.invert(x) + " to " + scale.invert(x+width));
								          });
								
								        var svg = d3.select("#timeline3")
								        			.append("svg").attr("width", width)
								          				.datum(labelTestData2).call(chart);
								    }
								 
								 
								    timelineHover(); 

								}
								
							});
							
						}
							
			            
			        }    
		            
	
				});
				
        }]);
})();