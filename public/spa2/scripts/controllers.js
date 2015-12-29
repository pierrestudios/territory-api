(function () {
    'use strict';

    angular.module('app')
        .controller('HomeController', ['$rootScope', '$scope', '$location', '$localStorage', 'Auth',
            function ($rootScope, $scope, $location, $localStorage, Auth) {
                function successAuth(res) {
                    $localStorage.token = res.token;
                    setTimeout(function() {
	                    window.location = "/front2#/";
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

                $scope.logout = function () {
                    Auth.logout(function () {
                        window.location = "/front2#/login"
                    });
                };
                $scope.token = $localStorage.token;
                // $scope.tokenClaims = Auth.getTokenClaims();
                
                
            }])

        .controller('RestrictedController', ['$rootScope', '$scope', 'API', '$location', '$localStorage', '$routeParams', 
        	function ($rootScope, $scope, API, $location, $localStorage, $routeParams) {
/*
	            API.getApiData(function (res) {
	                $scope.api = res.data;
	            }, function () {
	                $rootScope.error = 'Failed to fetch restricted API content.';
	            });
*/
				console.log('$localStorage.token', $localStorage.token);
 				if (! $localStorage.token) 
 					window.location = "/front2#/login";
 				
				API.getApiAccess(function (res) {
					if (res.refreshedToken) 
						$localStorage.token = res.refreshedToken;
					
					$scope.token = $localStorage.token;
		            // console.log('$scope', $scope);
		            
		            // API.getPublishers(function (res) {});
		            
		            // API.getTerritories(function (res) {});
		
		            // API.getAddresses(function (res) {});
		            
		            // console.log('$location', $location);
		            
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
		                            
		            console.log('path', $location.$$path);
		            if ( $location.$$path == '/') {
		                setTimeout(function() {
		                    $scope.MorrisData(Morris);
		                }, 100);
		            }
		                
		
					if ( $location.$$path == '/publishers') {
						console.log('path', $routeParams);
						if ($routeParams.publisherId) {
							console.log('publisherId', $routeParams.publisherId);
						}
						
						API.getPublishers(function (res) {
							if(res.data && res.data.length) {
								$('#dataTables-publishers').DataTable({
						            "data": res.data,
						            "columns": [
								        { "data": "firstName" },
								        { "data": "lastName" },
								        { "data": "publisherType" },
								        { "data": "publisherId" },
								        { "data": "publisherId" }
								    ],
								    "columnDefs": [ {
							            "targets": 4,
							            "data": "publisherId",
							            "render": function(data, type, full, meta ) {
									        return '<a class="btn btn-info btn-sm" href=#/publishers/' + data + '>' + 'Edit' + '</a>';
									    }
							        }],
							        
						            searching: false,
						            responsive: true
						        });
							}
						});
				        
		            }
		            
		            if ( $location.$$path == '/territories') {
				        $('#dataTables-territories').DataTable({
				            searching: false,
				            responsive: true
				        });
		            }
	
				}
			);
				
        }]);
})();