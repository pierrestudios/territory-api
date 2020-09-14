
<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Territory Api | Territory management api documentation</title>
  <meta name="description" content="Territory management documentation for very a simple RESTful API.">
  <meta name="author" content="Pierre Studios">
  <meta name="viewport" content="width=980">

  <link rel="stylesheet" href="doc/css/style.css">

</head>

<body id="api">

  <header>
    <div class="container">
      <h1><a >Territory Api</a></h1>
      <nav>
        <ul>
          	<li class="active"><a href="/docs/">API Documentation</a></li>
		   	<li><a href="/en">View Web UI</a></li>
        </ul>

        <ul id="signup-or-login">
          <li class="link-signup"><a href="/en#/signup">Sign Up</a> <span>or</span></li>
<li class="link-login"><a href="/en#/signin">Log In</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="page">
    <div class="container">
 
      <section class="docs-content docs-content--2-col">

        <!-- Docs Section -->
        <div class="docs-section docs-section--title">
          <div class="docs-description">
            <h2>Introduction</h2>
          </div>
        </div>
        <!-- End Docs Section -->

        <!-- Docs Section -->
        <div class="docs-section">

          	<div class="docs-description">
	            <p>Territory Services API is a RESTful api developed to provide easy and accessible storage and management for congregation territories. Although many systems can connect to the api via mobile and web client, the api does provide a web UI client interface. The web UI client interface can be viewed <a href="/en">here</a>.</p>
	
	            <h3>Core Entities</h3>
	            <ul>
	              <li>
	                <strong>Users</strong> userId, userType, email, publisherId</li>
	              <li>
	                <strong>Publishers</strong> publisherId, firstName, lastName, publisherType </li>
	              <li>
	                <strong>Territories.</strong> territoryId, publisherId, date, number, location, cityState, boundaries</li>
	              <li>
	                <strong>Addresses</strong> addressId, territoryId, inActive, name, address, apt, lat, long, phone, streetId</li>
	              <li>
	                <strong>Streets</strong> streetId, isAptBuilding, street</li>
	              <li>
	                <strong>Records</strong>, recordId, userId, territoryId, publisherId, activityType</li>
	              <li>
	                <strong>Notes</strong>, note, date</li>
	            </ul>
	            
	            <h3>API URL</h3>
	            <ul>
	              <li>
	                <strong><?php echo $api_url; ?></strong></li>
	            </ul>  
          	
	            <h3>Get started</h3>
	            
	            <div class="api-methods-list">
		            
		              
		            <div class="api-methods-details">  
		                <h4>Sign up</h4>
		                <div class="api-methods-url">URL: <strong>/signup</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>{email: (string), password: (string)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"token":(string)}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Sign in</h4>
		                <div class="api-methods-url">URL: <strong>/signin</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>{email: (string), password: (string)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"token":(string)}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get Access</h4>
		                <div class="api-methods-url">URL: <strong>/auth-user</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": {"email":(string),"userId":(int),"userType":(string)}}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get All Users</h4>
		                <div class="api-methods-url">URL: <strong>/users</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"userId":(int),"userType":(string),"email":(string),"publisher":{
		"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string),"territories":[
			{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string)},...
		]
	}},...
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Save User</h4>
		                <div class="api-methods-url">URL: <strong>/users/{userId}/save</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), userId (int), {"email": (string), "userType": (string)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"userId":(int), "userType":(string), "email":(string), "publisherId":(int)}
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Delete User</h4>
		                <div class="api-methods-url">URL: <strong>/users/{userId}/delete</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), userId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": true}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Attach User to Publisher</h4>
		                <div class="api-methods-url">URL: <strong>/publishers/attach-user</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), {"userId": (int), "publisherId": (int)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string)}
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get All Publishers</h4>
		                <div class="api-methods-url">URL: <strong>/publishers</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string),"territories":[
		{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string)},...
	]}
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get Publisher</h4>
		                <div class="api-methods-url">URL: <strong>/publishers/{publisherId}</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), publisherId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":
	{"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string),"territories":[
		{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string)},...
	]}
}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Add Publisher</h4>
		                <div class="api-methods-url">URL: <strong>/publishers/add</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), {"firstName": (string), "lastName": (string)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":
	{"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string),"territories":[
		{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string)},...
	]}
}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Update Publisher</h4>
		                <div class="api-methods-url">URL: <strong>/publishers/{publisherId}/save</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), publisherId (int), {"firstName": (string), "lastName": (string)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":
	{"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string),"territories":[
		{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string)},...
	]}
}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Delete Publisher</h4>
		                <div class="api-methods-url">URL: <strong>/publishers/{publisherId}/delete</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), publisherId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": true}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get All Territories</h4>
		                <div class="api-methods-url">URL: <strong>/territories</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string)},...
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get Available Territories</h4>
		                <div class="api-methods-url">URL: <strong>/available-territories</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string)},...
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get All Territories W/Filter</h4>
		                <div class="api-methods-url">URL: <strong>/territories/filter</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), {"userId": (int)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string)},...
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get Territory</h4>
		                <div class="api-methods-url">URL: <strong>/territories/{territoryId}</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), territoryId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":
	{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string),"addresses":[
		{"addressId":(int),"territoryId":(int),"inActive":(bool),"name":(string),"address":"400","apt":(string),"lat":(float),"long":"0.000000","phone":(string),"street":{"streetId":(int),"isAptBuilding":(bool),"street":(string)},"streetId":(int),"streetName":(string),"notes":""},...
	],"publisher":{"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string)},"records":""}
}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get Territory Activities</h4>
		                <div class="api-methods-url">URL: <strong>/all-activities</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":
	{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string), addresses:[
		{"addressId":(int), "territoryId":(int), "inActive":(bool), "name":(string), "address": (int), "apt":(string), "lat":(float), "long": (float), "phone":(string), "streetId":(int), "streetName":(string), "notes":(string)
			"street":{"streetId":(int),"isAptBuilding":(bool),"street":(string)},
		},...
	], records: [
		{recordId: (int), userId: (int), territoryId: (int), publisherId: (int), activityType: (string), 
			publisher: {publisherId: (int), firstName: (string), lastName: (string), publisherType: (string)}, 
			user: {userId: (int), userType: (string), email: (string)}
		},...	
	], publisher :{"publisherId":(int),"firstName":(string),"lastName":(string),"publisherType":(string)}
}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get Recent Activities</h4>
		                <div class="api-methods-url">URL: <strong>/activities</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":[
	{"territoryId":(int),"publisherId":(int),"date":(date),"number":(int),"location":(string),"cityState":(string),"boundaries":(string), records: [
		{recordId: (int), userId: (int), territoryId: (int), publisherId: (int), activityType: (string), 
			publisher: {publisherId: (int), firstName: (string), lastName: (string), publisherType: (string)}, 
			user: {userId: (int), userType: (string), email: (string)}
		}	
	]},...
]}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Get Territory Map</h4>
		                <div class="api-methods-url">URL: <strong>/territories/{territoryId}/map</strong></div> 
		                <div class="api-methods-type">type: <strong>GET</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), territoryId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":{
	territory: {"territoryId":(int), "publisherId":(int), "date":(date), "number":(int), "location":(string), "cityState":(string), "boundaries":(string),  
		publisher: {publisherId: (int), firstName: (string), lastName: (string), publisherType: (string)}
	},
	map: [
		{address: (string), name: (string), lat: (float), long: (float), id: (int)},...
	]
	
}}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Add Territory</h4>
		                <div class="api-methods-url">URL: <strong>/territories/add</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), {"location": (string), "number": (int)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":{
	territory: {"territoryId":(int), "publisherId":(int), "date":(date), "number":(int), "location":(string), "cityState":(string), "boundaries":(string)}	
}}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Update Territory</h4>
		                <div class="api-methods-url">URL: <strong>/territories/{territoryId}</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), territoryId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data":{
	territory: {"territoryId":(int), "publisherId":(int), "date":(date), "number":(int), "location":(string), "cityState":(string), "boundaries":(string)}	
}}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Add Address</h4>
		                <div class="api-methods-url">URL: <strong>/territories/{territoryId}/addresses/add</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), territoryId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": {address: (string), name: (string), lat: (float), long: (float), addressId: (int)}}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Update Address</h4>
		                <div class="api-methods-url">URL: <strong>/territories/{territoryId}/addresses/edit/{addressId}</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), territoryId (int), addressId (int)</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": {address: (string), name: (string), lat: (float), long: (float), addressId: (int)}}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Remove Addresses</h4>
		                <div class="api-methods-url">URL: <strong>/addresses/{addressId}/remove</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), {"addressId": (int), "delete" : (bool), "note": (string)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": true}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                <div class="api-methods-details">  
		                <h4>Add Note</h4>
		                <div class="api-methods-url">URL: <strong>/territories/{territoryId}/addresses/{addressId}/notes/add</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), territoryId (int), addressId (int), {"note": (string), "date": (date)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": {address: (string), name: (string), lat: (float), long: (float), id: (int), "notes":(string)}}</pre>
			                </div>
			            </div>  
	                </div>
	               
	               
	                <div class="api-methods-details">  
		                <h4>Update Note</h4>
		                <div class="api-methods-url">URL: <strong>/territories/{territoryId}/notes/edit/{noteId}</strong></div> 
		                <div class="api-methods-type">type: <strong>POST</strong></div>
		                <div class="api-methods-params">@params: <strong>JWToken (header), territoryId (int), noteId (int), {"note": (string), "date": (date)}</strong></div>
		                <div class="api-methods-return">@return: 
			                <div class="api-methods-return source-code">
				                <pre>{"data": {address: (string), name: (string), lat: (float), long: (float), id: (int), "notes":(string)}}</pre>
			                </div>
			            </div>  
	                </div>
	                
	                
	                
	                

	            </div>

          	</div>

        </div>
        <!-- End Docs Section -->

      </section>
    </div>
  </div>

<footer>
  <div class="container">
    
    <div class="copyright">
      <p>&copy; <a href="http://www.pierrestudios.com/">Pierre Studios</a>, <?php echo date('Y'); ?>. All rights reserved.</p>
    </div>
  </div>

</footer>

</body>

</html>
