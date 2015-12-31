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
					}
					
					// ALL PUBLISHERS
					if ( $location.$$path == '/publishers') {
						
						API.getPublishers(function (res) {
							if(res.data && res.data.length) {
								$('#dataTables-publishers').DataTable({
						            "data": res.data,
						            "columns": [
								        { "data": "name" },
								        { "data": "publisherType" },
								        { "data": "publisherId" },
								        { "data": "publisherId" }
								    ],
								    "columnDefs": [ {
							            "targets": 0,
							            "data": null,
							            "render": function(data, type, fullObj, meta ) {
									        return fullObj.firstName + ' ' + fullObj.lastName;
									    }
							        },{
							            "targets": 3,
							            "data": "publisherId",
							            "render": function(data, type, fullObj, meta ) {
									        return '<a class="btn btn-info btn-sm" href=#/publishers/' + data + '>' + 'Edit' + '</a>';
									    }
									}],
						            searching: false,
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
							if(res.data && res.data.length) 
								$scope.territory = res.data;
								
							if(res.data.addresses && res.data.addresses.length) {	
								$('#dataTables-addresses').DataTable({
						            "data": res.data.addresses,
						            "columns": [
								        { "data": "name" },
								        { "data": "address" },
								        { "data": "phone" },
								        { "data": "addressId" },
								        { "data": "addressId" }
								    ],
								    "columnDefs": [{
							            "targets": 4,
							            "data": "addressId",
							            "render": function(data, type, fullObj, meta ) {
									        return '<a class="btn btn-info btn-sm" href=#/addresses/' + data + '>' + 'Delete' + '</a>';
									    }
									}],
						            searching: false,
						            responsive: true
						        });
							}
						}); 
					}
		            
		            // ALL TERRITORIES
		            if ( $location.$$path == '/territories') {
				       
				        API.getTerritories(function (res) {
							if(res.data && res.data.length) {
								$('#dataTables-territories').DataTable({
						            "data": res.data,
						            "columns": [
								        { "data": "number" },
								        { "data": "location" },
								        { "data": "publisherId" },
								        { "data": "territoryId" }
								    ],
								    "columnDefs": [{
							            "targets": 3,
							            "data": "territoryId",
							            "render": function(data, type, fullObj, meta ) {
									        return '<a class="btn btn-info btn-sm" href=#/territories/' + data + '>' + 'View' + '</a>';
									    }
									}],
						            searching: false,
						            responsive: true
						        });
							}
						});
		            }
	
				});
				
        }]);
})();