<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

	<style>
		body { font-family: arial, sans-serif; font-size: 14px;}
	.territory-map-display {
	    width: 95%;
	    margin: 20px auto;
	}    
	.top-content { margin: 20px auto; width: 95%; height: 30px}
	.button {
		padding: 5px 10px;
	    border: none;
	    border-radius: 3px;
	    font-size: 14px;
	    text-align: center;
	    white-space: nowrap;
	    vertical-align: middle;
	    -ms-touch-action: manipulation;
	    touch-action: manipulation;
	    cursor: pointer;
	    -webkit-user-select: none;
	    color: #fff;
		background-color: #5bc0de;
		border-color: #46b8da;
	}
	button.green {
	    background-color: #5cb85c;
	}
  	</style>
<body>
 
 	<div id="header">
		<div class="top-content">
			<div style="text-align: left; float: left;">TERITWA # <strong class="number">{{$number}}</strong> </div>
				 
			<div class="right" style="float: left; margin-left: 10px">
				@if($publisher)
					{{$publisher['first_name']}} {{$publisher['last_name']}} &nbsp; 
				@endif
				@if($date)
					Date: {{$date}}
				@endif
			</div>
			<button class="button" id="track-user" style="float: right; margin-left: 10px">Track</button>	 
		</div>
 	</div>
 	
	<div id="content">
  		<div class="territory-map-display" id="territory-map-display"></div>
	</div>
  
    <script>        
		var DocumentData = {};
		<?php if($addresses) : ?>
		DocumentData.map_data = [
		<?php foreach($addresses as $street => $addresses) : ?>
			<?php foreach($addresses as $i => $address) : ?>
			    {
	                "address": "<?php echo ($address['street']['is_apt_building'] ? ($address['street']['street']) : ($address['address'] . ' ' . $address['street']['street'])); ?>", 
	                "name": "<?php echo ($address['street']['is_apt_building'] ? 'Apartment' : ($address['name'] ? $address['name'] : "Home")); ?>", 
	                "lat": "<?php echo $address['lat']; ?>",
	                "long": "<?php echo $address['long']; ?>",
	                "id": "<?php echo $address['id']; ?>"   
	            }, 
			<?php endforeach; ?> 
		<?php endforeach; ?>
		];
		<?php endif; ?>
		
		DocumentData.user_marker_image = '/spa2/images/marker-user.gif';
		
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script src="/spa2/lib/jquery.min.js"></script>

<script>

if(typeof($) == 'undefined') var $ = jQuery.noConflict();

// MAIN METHODS
    
function initializeMap() {
	$('#territory-map-display').css('height', ($(window).height() - 140))
	
    isMapInitialized=true; 

    if(DocumentData.map_data && DocumentData.map_data[0]) {
		
		var centerLatlng = new google.maps.LatLng(DocumentData.map_data[0].lat, DocumentData.map_data[0].long),
			bounds = new google.maps.LatLngBounds();
		
	    var mapOptions = {
	        zoom: 18,
	        center: centerLatlng
	    }
	    
	    map = new google.maps.Map(document.getElementById("territory-map-display"), mapOptions);

	    var markers = DocumentData.map_data;
	    
	    for(m in markers) {
	        markers[m].myLatlng = new google.maps.LatLng(DocumentData.map_data[m].lat,DocumentData.map_data[m].long);
	        markers[m].marker = createMarker(map, markers[m]);
	        
			bounds.extend(markers[m].myLatlng);
	        
	        <?php if(!empty($editable)) : ?>
		        google.maps.event.addListener(markers[m].marker, "dragend", function(e) {
		            updateEntry(this, e);
		        });
			<?php endif; ?>
			
			infowindow = new google.maps.InfoWindow();

			markers[m].marker.addListener('click', function(e) {
				infowindow.setContent(this.title);
				infowindow.open(map, this);
			});
	
	    }
	    
		map.fitBounds(bounds);
	    
    }
     
}

function updateEntry(marker, e) {
    if(window.confirm("Update coodinates for this address?"))
    // do ajax
    $.ajax({
	    type: 'POST',
	    url: '',
	    data: {
		    id: marker.id,
		    lat: marker.position.lat(),
		    long: marker.position.lng()
	    },
	    dataType: 'json',
	    error: function(jQxhr, status, error) {
		    console.log(status, error);
	    },
	    success: function(data, status, jQxhr) {
		    console.log(status, data);
	    }
    });
    
}


function createMarker(map, data) {
	var marker = new google.maps.Marker({
        position: new google.maps.LatLng(data.lat, data.long),
        map: map,
        title: <?php if(!empty($editable)) : ?>data.id + ': ' + <?php endif; ?> data.name + ' - ' + data.address,
        id: data.id,
        <?php if(!empty($editable)) : ?>
        draggable: true,
        <?php endif; ?>
        // icon: DocumentData.map_marker_image,
        animation: google.maps.Animation.DROP,
	});
	
	return marker;
}

function initTrackUser(geoLoc) {
    if (geoLoc) {
        geoLoc.getCurrentPosition(function(position) {
		    // set current position
		    setUserLocation(position); 
		    
		    // watch position
		    watchCurrentPosition(geoLoc);
		}, logError, {
            enableHighAccuracy : true,
            timeout : 60000,
            maximumAge : 0
        });
    } else {
        alert("Your phone does not support the Geolocation API");
    }
}
 
function setUserLocation(pos) {
    userLocation = new google.maps.Marker({
       map : map,
       position : new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude),
       title : "You are here",
       optimized: false, // <-- required for animated gif
       icon : DocumentData.user_marker_image,
    }); 
    
    console.log('userLocation', userLocation);
    
	// scroll to userLocation
	map.panTo(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));
}

function watchCurrentPosition(geoLoc) {
    positionTimer = geoLoc.watchPosition(function(position) {
        setMarkerPosition(userLocation, position);
        map.panTo(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
    });
}

function setMarkerPosition(marker, position) {
     marker.setPosition(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
}

function logError(error){
	console.log('error', error)
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
    var map, geocoder, infowindow, tracking, positionTimer, geoLoc;
    initializeMap();
     
    // Add user marker
    $('#track-user').on('click', function(e) {
	    e.preventDefault();
	    
	    geoLoc = navigator.geolocation;

	    if(!tracking) {
		    initTrackUser(geoLoc);
		    tracking = true;
		    $(this).text("Tracking: ON").addClass('green');
	    }  else {
		    geoLoc.clearWatch(positionTimer);
		    userLocation.setMap(null);
		    tracking = false;
		    $(this).text("Track").removeClass('green');
	    } 
	    
    });
});

</script>   
 
</body>
</html>