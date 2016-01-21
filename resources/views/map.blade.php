<html>
<head>
  <style>
.MDC-Map-display {
    width: 80%;
    min-width: 600px;
    height: 500px;
}    
  </style>
<body>
 
 
  <div id="content">
  	<div class="MDC-Map-display" id="MDC-Map-display"></div>
  </div>
  
    <script>        
		var DocumentData = {};
		@if($addresses)
		DocumentData.map_data = [
		@foreach($addresses as $street => $address)
			@foreach($address as $i => $home)
			    {
	                "address": "405 NE 191 ST, Miami", 
	                "name": "My home", 
	                "id": "001"
	            },
			@endforeach
		@endforeach
		];
		@endif
		
		DocumentData.map_marker_image = 'http://www.pierrestudios.com/beta_sites/mdc/wp-content/plugins/meta-data-console/images/marker-icon.png';
		
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    
<script type='text/javascript' src='http://www.pierrestudios.com/beta_sites/mdc/wp-includes/js/jquery/jquery.js?ver=1.11.1'></script>
<script type='text/javascript' src='http://www.pierrestudios.com/beta_sites/mdc/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.2.1'></script>

<script>

if(typeof($) == 'undefined') var $ = jQuery.noConflict();

// MAIN METHODS
    
function initializeMap() {
    isMapInitialized=true; 

    if(DocumentData.map_data && DocumentData.map_data[0]) {
	    geocoder = new google.maps.Geocoder();
	    
	    var mapOptions = {
	        zoom: 17,
	    }
	    
	    map = new google.maps.Map(document.getElementById("MDC-Map-display"), mapOptions);
	    
	    geocodeAddress(geocoder, DocumentData.map_data[0], map, true);
	     
		// loop
		var m = 0;
		for(m in DocumentData.map_data) {
			if(m==0) continue;
			geocodeAddress(geocoder, DocumentData.map_data[m], map);
		}
	    
    }
}

function createMarker(map, myLatlng, data) {
	var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: data.name,
        id: data.id,
        icon: DocumentData.map_marker_image,
        animation: google.maps.Animation.DROP,
	});
	
	// return marker;
}

function geocodeAddress(geocoder, data, map, center) {
  	geocoder.geocode({'address': data.address}, function(results, status) {
    	if (status === google.maps.GeocoderStatus.OK) {
	    	if(center) map.setCenter(results[0].geometry.location);
	    	createMarker(map, results[0].geometry.location, data);
    	} else {
      		alert('Geocode was not successful for the following reason: ' + status);
    	}
  	});
}

$(function() {
    var map, geocoder;
    // initializeMap();
});

</script>   
 
</body>
</html>