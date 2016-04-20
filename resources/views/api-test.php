<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Territory Services API - Test</title>
    
    <!-- Bootstrap Core CSS -->
    <link href="/theme-creole/css/bootstrap.min.css" rel="stylesheet">

</head>

<body ng-app="app">
    
    <div id="wrapper">

       <a href="" class="call-api">Call Api</a>
		
    </div>
    	
    <div class="footer" style="display: none">
        <div class="container">
            <p class="muted credit">Developed by <a href="http://www.pierrestudios.com" title="Pierre Studios">Pierre Studios</a></p>
        </div>
    </div>

    <script src="/theme-creole/lib/jquery.min.js"></script>
    <script src="/theme-creole/lib/bootstrap.min.js"></script>
	<script> 
		$(function() {
			$('.call-api').click(function() {
				console.log('call-api')
				 
				$.ajax({
			      	type: 'GET',
			      	// crossDomain:'true',
					// withCredentials :'true',
			      	url: 'http://territory.prositestudios.com/v1/auth-user', 
			      	dataType: 'json',
			      	beforeSend: function(xhr){
				      	// Authorization = 'Bearer ' + $localStorage.token
				      	token = localStorage.getItem('ngStorage-token') || 'test-token';
				      	xhr.setRequestHeader('Authorization', 'Bearer ' + token);
				    },
			      	error: function (jqXHR, textStatus, errorThrown) {
				      	console.log(textStatus + ': ', errorThrown);
				  	},
			      	success: function (data, textStatus, jqXHR) {
				      	console.log(data, textStatus, jqXHR);
				      	if(data && data.data && data.data.refreshedToken) {
					      	localStorage.setItem('ngStorage-token', 'Bearer ' + data.data.refreshedToken)
					      	localStorage.setItem('ngStorage-token-date', new Date().toString())
				      	}
				  	}
				});
				return false
			})
		})	
	</script>
</body>
</html>