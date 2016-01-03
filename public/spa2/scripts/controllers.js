(function () {
    'use strict';

    angular.module('app')
        .controller('HomeController', ['$rootScope', '$scope', '$location', '$localStorage', 'Auth',
            function ($rootScope, $scope, $location, $localStorage, Auth) {
                function successAuth(res) {
                    $localStorage.token = res.token;
                    setTimeout(function() {
	                    window.location = "/front2#/dashboard";
	                    window.location.reload();
                    }, 1000);
                }

                $scope.signin = function () {
                    var formData = {
                        email: $scope.email,
                        password: $scope.password
                    };

                    Auth.signin(formData, successAuth, function (res) {
                        $rootScope.error = res.message || 'Invalid credentials.';
                    })
                };

                $scope.signup = function () {
                    var formData = {
                        email: $scope.email,
                        password: $scope.password
                    };

                    Auth.signup(formData, successAuth, function (res) {
                        $rootScope.error = res.message || res.error || 'Failed to sign up.';
                    })
                };

/*
                $scope.logout = function () {
	                console.log('signin 1');
	                    $location.path('signin');
                        // window.location = "/front2#/login";
                    });
                };
*/
                $scope.token = $localStorage.token;
                $scope.tokenActive = $scope.token ? 'token-active' : 'token-not-active';
                // $scope.tokenClaims = Auth.getTokenClaims();
                
                
            }])

        .controller('ApiController', ['$rootScope', '$scope', 'API', '$location', '$localStorage', '$routeParams', 
        	function ($rootScope, $scope, API, $location, $localStorage, $routeParams) {
/*
	            API.getApiData(function (res) {
	                $scope.api = res.data;
	            }, function () {
	                $rootScope.error = 'Failed to fetch restricted API content.';
	            });
*/
				// console.log('$localStorage.token', $localStorage.token);
				
				$scope.logout = function () {
                    API.logout(function () {
	                    window.location = "/front2#/login";
	                    window.location.reload();
                    });
                };
                
                // Chart Data
	            $scope.MorrisData = function(Morris) {
	                $('#morris-area-chart').text('');
	                Morris.Area({
				        element: 'morris-area-chart',
				        data: [{
				            period: '2010 Q1',
				            iphone: 2666,
				            ipad: null,
				            itouch: 2647
				        }, {
				            period: '2010 Q2',
				            iphone: 2778,
				            ipad: 2294,
				            itouch: 2441
				        }, {
				            period: '2010 Q3',
				            iphone: 4912,
				            ipad: 1969,
				            itouch: 2501
				        }, {
				            period: '2010 Q4',
				            iphone: 3767,
				            ipad: 3597,
				            itouch: 5689
				        }, {
				            period: '2011 Q1',
				            iphone: 6810,
				            ipad: 1914,
				            itouch: 2293
				        }, {
				            period: '2011 Q2',
				            iphone: 5670,
				            ipad: 4293,
				            itouch: 1881
				        }, {
				            period: '2011 Q3',
				            iphone: 4820,
				            ipad: 3795,
				            itouch: 1588
				        }, {
				            period: '2011 Q4',
				            iphone: 15073,
				            ipad: 5967,
				            itouch: 5175
				        }, {
				            period: '2012 Q1',
				            iphone: 10687,
				            ipad: 4460,
				            itouch: 2028
				        }, {
				            period: '2012 Q2',
				            iphone: 8432,
				            ipad: 5713,
				            itouch: 1791
				        }],
				        xkey: 'period',
				        ykeys: ['iphone', 'ipad', 'itouch'],
				        labels: ['iPhone', 'iPad', 'iPod Touch'],
				        pointSize: 2,
				        hideHover: 'auto',
				        resize: true
				    });
					
	            };
                
                // Verify token exists then continue
 				if (! $localStorage.token) {
	 				$scope.token = null;
	 				$scope.tokenActive = 'token-not-active';
 				} else
 				
				API.getApiAccess(function (res) {
					if (res.refreshedToken) 
						$localStorage.token = res.refreshedToken;
					
					$scope.token = $localStorage.token;
					$scope.tokenActive = $scope.token ? 'token-active' : 'token-not-active';
		            // console.log('$scope', $scope);
		                            
		            // DASHBOARD
		            if ( $location.$$path == '/dashboard') {
		                setTimeout(function() {
		                    $scope.MorrisData(Morris);
		                }, 100);
		            }
		                
					// PUBLISHER
					if ($routeParams.publisherId) {
						var elem = document.querySelector('.js-switch');
						if (! $(elem).data('switchery'))
							var init = new Switchery(elem);
							
						API.getPublisher($routeParams.publisherId, function(res) {
							$scope.publisher = res.data;
						});
						
						API.getTerritories(function (res) {
							$scope.territories = res.data;
						});
						
						$scope.updatePublisher = function () {
							console.log($scope.publisher.firstName, $scope.publisher.lastName);
							API.updatePublisher($scope.publisher.publisherId, {"firstName": $scope.publisher.firstName, "lastName": $scope.publisher.lastName}, function (res) {
								window.location.reload();
							});
					    };
					    $scope.assignTerritory = function () {
							API.updateTerritory($scope.newTerritory.terrSelected, {"publisherId": $scope.publisher.publisherId, "date": API.formatDateObj($scope.newTerritory.date)}, function (res) {
								window.location.reload();
								$scope.newTerritory.terrSelected = '';
								$scope.newTerritory.date = '';
							});
					    }; 
					    $scope.unassignTerritory = function (terrSelected) {
							API.updateTerritory(terrSelected, {"publisherId": null, "date": API.formatDateObj(new Date())}, function (res) {
								window.location.reload();
							});
					    };
					    
						$scope.isPassDue = function(date) {
							return API.passDueTerritory(date);
						};
						
						$scope.isPassDueClass = function(date) {
							return API.passDueTerritory(date) ? ' label-danger' : ' not';
						};
						
						$scope.testNG = function() {
							return 'test-ng';
						}
					}
					
					// ALL PUBLISHERS
					if ( $location.$$path == '/publishers') {
						
						API.getPublishers(function (res) {
							if(res.data && res.data.length) {
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
										            assignedBadges += '<span class="badge '+ (API.passDueTerritory(data[t].date) ? 'badge-danger' : '') +'">' + data[t].number + '</span> ';
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
									        return '<a class="btn btn-info btn-sm" href=#/publishers/' + data + '>' + 'Edit' + '</a>';
									    }
									}],
						            searching: false,
						            paging: false,
						            // scrollY: 400,
						            responsive: true
						        });
							}
						});
				        
		            }
		            
		            // TERRITORY
		            if ($routeParams.territoryId) {
						var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

						elems.forEach(function(html) {
							if (! $(html).data('switchery'))
							var switchery = new Switchery(html);
						});
							
						API.getTerritory($routeParams.territoryId, function(res) {
							if(res.data) 
								$scope.territory = res.data;
								 
							if(!$('#dataTables-addresses').is('.dataTable') && res.data.addresses && res.data.addresses.length) {	
								var table = $('#dataTables-addresses').DataTable({
						            "data": res.data.addresses,
						            "columns": [
								        { "data": "name" },
								        { "data": "address" },
								        { "data": "street"},
								        { "data": "phone", "orderable": false },
								        { "data": "notes", "orderable": false},
								        { "data": "addressId", "orderable": false}
								    ],
								    "columnDefs": [{ 
										"orderData": 2, "targets": 1,
									},
								    {
							            "targets": 4,
							            "data": "notes",
							            "render": function(data, type, fullObj) {
								            var notes = '<li class="list-group-item"><a class="btn btn-success btn-sm badge badge-success add-note" href="" data-address-id="' + fullObj.addressId + '">' + 'Add Note' + '</a> &nbsp; </li>';
								            for(var n in data) {
									            if(n < 5) notes += '<li class="list-group-item">'+ data[n].note + (data[n].date != '0000-00-00' ? ' <small class="label label-default">'+ data[n].date  +'</small>' : '') + ' <a class="btn btn-info btn-sm badge badge-info edit-note" href="" data-note-id="' + data[n].noteId + '" data-note-note="' + data[n].note + '" data-note-date="' + data[n].date + '">Edit Note</a></li>'; 
								            }
									        return '<ul class="list-group">' + notes + '</ul>'; // <div class="btn-group flex"><a class="btn btn-info btn-sm" href=#/addresses/' + data + '>' + '<i class="fa fa-edit"></i>' + '</a> <a class="btn btn-danger btn-sm" href=#/addresses/' + data + '>' + '<i class="fa fa-trash"></i>' + '</a></div>';
									    }
									},{
							            "targets": 5,
							            "data": "addressId",
							            "render": function(data, type, fullObj, meta ) {
									        return '<a class="btn btn-info btn-sm edit-address" href="" title="Edit address" data-address-id="' + data.addressId + '"><i class="fa fa-edit"></i></a> <a class="btn btn-danger btn-sm delete-address" href="" title="Remove address" data-address-id="' + data.addressId + '"><i class="fa fa-times"></i></a>';
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
							        console.log(address);
							        // console.log('$scope.territory', $scope.territory); 
							        // console.log('window.$scope.territory', window.$scope.territory);
							        // $scope.editAddress(1); 
							        // $scope.$apply();
							        $('#btnUpdateAddress').attr('data-address-id', address.addressId);
							        $('input[ng-model="editTerritoryAddress.name"]').val(address.name);
							        $('input[ng-model="editTerritoryAddress.phone"]').val(address.phone);
							        $('input[ng-model="editTerritoryAddress.address"]').val(address.address);
							        
						        	$('#targetEditAddress').trigger('click');
						        });
						        
						        $('.delete-address').on('click', function(e){ 
							        e.preventDefault();
							        var address = res.data.addresses[table.row(this.parentNode.parentNode).index()];
							        console.log(address);
							        $scope.removeAddress(address.addressId);
							    });
							    
							    $('.add-note').on('click', function(e){
							        e.preventDefault();
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
							API.updateTerritory($scope.territory.territoryId, {"location": $scope.territory.location}, function (res) {
								window.location.reload();
							});
					    };
					    $scope.addAddress = function () {
						    var notes = $scope.newAddress.notes ? [{
								"note": $scope.newAddress.notes,
								"date": API.formatDateObj($scope.newAddress.date)
							}] : null;
							
							API.addAddress($scope.territory.territoryId, 
							{
								"name": $scope.newAddress.name,
								"address": $scope.newAddress.address,
								"phone": $scope.newAddress.phone,
								"notes": notes
							}, 
							function (res) {
								window.location.reload();
							});
					    };
					    $scope.updateAddress = function () {
							API.updateAddress($scope.territory.territoryId, $('#btnUpdateAddress').attr('data-address-id'),
							{
								"name": $('input[ng-model="editTerritoryAddress.name"]').val(),
								"address": $('input[ng-model="editTerritoryAddress.address"]').val(),
								"phone": $('input[ng-model="editTerritoryAddress.phone"]').val()
							}, 
							function (res) {
								window.location.reload();
							});
					    };
					    $scope.removeAddress = function (addressId) {
							API.removeAddress(addressId, 
							function (res) {
								window.location.reload();
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
									window.location.reload();
								});
							} else {	
								API.updateNote($scope.territory.territoryId, $('#btnSaveNote').attr('data-note-id'),
								{
									"note": $('input[ng-model="editNote.note"]').val(),
									"date": $('input[ng-model="editNote.date"]').val(),
								}, 
								function (res) {
									window.location.reload();
								});
							}
					    };
					    $scope.deleteNote = function (noteId) {
							API.deleteNote(noteId, 
							function (res) {
								// window.location.reload();
							});
					    };
					}
		            
		            // ALL TERRITORIES
		            if ( $location.$$path == '/territories') {
				       
				        API.getTerritories(function (res) {
							if(res.data && res.data.length) {
								$('#dataTables-territories').DataTable({
						            "data": res.data,
						            "columns": [
								        { "data": "number" },
								        { "data": "location", "orderable": false },
								        { "data": "date", "orderable": false },
								        { "data": "territoryId", "orderable": false }
								    ],
								    "columnDefs": [{
							            "targets": 2,
							            "data": "date",
							            "render": function(data, type, fullObj) {
								            var due = API.passDueTerritory(data);
								            if(fullObj.publisherId)
									        	return '<span class="badge badge-success '+ (due ? 'badge-danger' : '') +'">' + (data != '0000-00-00' ? data : '') + '</span>' + (due ? ' <span class="badge badge-danger">Passed Due</span>' : '');
									        else 
									        	return '<span class="badge">No</span>';
									    }
									},{
							            "targets": 3,
							            "data": "territoryId",
							            "render": function(data, type, fullObj, meta ) {
									        return '<a class="btn btn-info btn-sm" href=#/territories/' + data + '>' + 'View' + '</a>';
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
		            }
	
				});
				
        }]);
})();