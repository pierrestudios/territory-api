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
			<div class="right" style="float: left; margin-left: 10px">{{$location}}</div>	 
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
		var DocumentData = {}, territoryList = '';
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
		
		<?php if($territories) : $territoryList = ''; ?>
			<?php 
				foreach($territories as $k => $territory) :  
					$territoryList .= '<option value="'. $territory->id .'">'. $territory->number .'</option>';
				endforeach; 
			?>
			territoryList = '<?php echo $territoryList; ?>';
		<?php endif; ?>
		
		DocumentData.user_marker_image = '/spa2/images/marker-user.gif';
		
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing,geometry"></script>
    <script src="/spa2/lib/jquery.min.js"></script>

<script>

if(typeof($) == 'undefined') var $ = jQuery.noConflict();

// MAIN METHODS
    
function initializeMap() {
	$('#territory-map-display').css('height', ($(window).height() - 140))
	
    isMapInitialized=true; 

    if(DocumentData.map_data && DocumentData.map_data[0]) {
		 
	    var mapOptions = {
	        zoom: 17,
	        center: {lat: <?php echo $center['lat']; ?>, lng: <?php echo $center['long']; ?>}
	    }
	    
	    map = new google.maps.Map(document.getElementById("territory-map-display"), mapOptions);

	    markers = DocumentData.map_data, 
	    bounds = new google.maps.LatLngBounds();
	    
	    
	    //******* Now draw boudaries **********
	    
	    var boundary = [];
  	
	  	var colors = {
		  	orange: '#fb8c00',
		  	orangeLite: '#FFE8CE'
	  	}
	  	
	  	var drawingManager = new google.maps.drawing.DrawingManager({
		    drawingMode: google.maps.drawing.OverlayType.POLYGON,
		    drawingControl: true,
		    drawingControlOptions: {
		      position: google.maps.ControlPosition.TOP_CENTER,
		      drawingModes: [
		        google.maps.drawing.OverlayType.POLYGON
		      ]
		    },
			polygonOptions: {
				fillColor: colors.orangeLite,
				fillOpacity: .5,
				strokeWeight: 5,
				strokeColor: colors.orange,
				editable: true,
				zIndex: 1
			}
		});
		
		drawingManager.setMap(map);
		
		google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
		  	var paths = event.overlay.getPath();
		  	// console.log('paths', paths);
		  	boundary = [];
		  	paths.forEach(function(Latlng, number) {
			  	boundary.push({'lat': Latlng.lat(), 'lng': Latlng.lng()});
		  	});
		  	// console.log('boundary', boundary);
		  	if(boundary.length) terrCoordinates.setPaths(boundary);
		  	google.maps.event.addListener(event.overlay, 'click', function(e) {
			  	saveBoundary(e, terrCoordinates);
		  	});
		});
	  	
	  	// Load the saved Boundary
	  	
	  	// boundaries data
	  	var savedPoly = '<?php echo $boundaries; ?>'; 
	  	
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
		
	  	google.maps.event.addListener(terrCoordinates, 'click', function(e) {
		  	saveBoundary(e, terrCoordinates);
	  	});
	  	
	  	google.maps.event.addListener(terrCoordinates.getPath(), 'set_at', function() {
		  	// console.log('Vertex moved on outer path.');
		  	boundary = [];
		  	terrCoordinates.getPath().forEach(function(Latlng, number) {
			  	boundary.push({'lat': Latlng.lat(), 'lng': Latlng.lng()});
		  	});
		  	console.log('boundary', boundary);
		});
		
		$(document).on('click', '.save-boundary', function(e) {
		  	e.stopPropagation();
		  	e.preventDefault();
		  	var boundaryString = JSON.stringify(boundary);
		  	// console.log('boundary', boundaryString);
		  	updateBoundary(boundaryString);
	  	});

	    
	    /***** Add Markers ********/
	    
	    var markerClicked;
	    
	    for(m in markers) {
	        markers[m].myLatlng = new google.maps.LatLng(DocumentData.map_data[m].lat,DocumentData.map_data[m].long);
	        var markerColor = google.maps.geometry.poly.containsLocation(markers[m].myLatlng, terrCoordinates) ? 'blue' : 'red';
	        markers[m].marker = createMarker(map, markers[m], markerColor);
			bounds.extend(markers[m].myLatlng);
	        <?php if(!empty($editable)) : ?>
		        google.maps.event.addListener(markers[m].marker, "dragend", function(e) {
			        var marker = this;
		            updateMarkerCoordinates(this, e);
		            
		            // loop thru markers to update same address
				    for(m in markers) {
					    console.log('markers[m].id', markers[m].id);
			        	if(markers[m].address == marker.address && markers[m].id != marker.id && !(markers[m].marker.position == marker.position)) {
				        	console.log('markers[m]', markers[m]);
				        	markers[m].marker.position = marker.position;
				        	updateMarkerCoordinates(markers[m].marker, e);
				        	// break;
			        	}
			        }
		            
		        });
			<?php endif; ?>
			
			infowindow = new google.maps.InfoWindow();
			editText = '<br><br>Move to Territory: <br><br><select class="territory-list"><option>Select Territory</option>'+ territoryList +'</select> <button class="save-move">Save</button>';
			markers[m].marker.addListener('click', function(e) {
				infowindow.setContent(this.title + editText);
				infowindow.open(map, this);
				markerClicked = this;
			});
	
	    }
	    
	    
		$(document).on('click', '.save-move', function(e) {
		  	e.stopPropagation();
		  	e.preventDefault();
		  	// console.log('markerClicked', markerClicked);
		  	var addressData = {
			  	"id": markerClicked.id,
			  	"territoryId": $('.territory-list').val()
		  	};
		  	updateAddressTerritory(addressData, function(success) {
			  	if(success) markerClicked.setMap(null);
			  	markers = window.markers;
			  	for(m in markers) {
		        	if(markers[m].address == markerClicked.address && markers[m].id != markerClicked.id) {
			        	console.log('markers[m]', markers[m]);
			        	// markerClicked = markers[m].marker;
			        	updateAddressTerritory({"id": markers[m].id, "territoryId": addressData.territoryId}, function(success) {
							if(success) markers[m].marker.setMap(null);
			  			});
			        	// break;
		        	}
		        }
		  	});
	  	});
	    
		map.fitBounds(bounds);
	    
    }
     
}



function saveBoundary(event, terrCoordinates) {
  	boundary = [];
  	terrCoordinates.getPath().forEach(function(Latlng, number) {
	  	boundary.push({'lat': Latlng.lat(), 'lng': Latlng.lng()});
  	});
  	// console.log('boundary', boundary);
  	infoWindow = new google.maps.InfoWindow;
  	infoWindow.setContent('<button class="save-boundary">Save boundary</button>');
  	infoWindow.setPosition(event.latLng);
	infoWindow.open(map);
}
	
	
function updateMarkerCoordinates(marker, e) {
    if(window.confirm("Update coodinates for this address?"))
  
    // do ajax
    $.ajax({
	    type: 'POST',
	    url: '',
	    data: {
		    id: marker.id,
		    lat: marker.position.lat(),
		    long: marker.position.lng(),
		    'action': 'update-marker'
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


function createMarker(map, data, markerColor) {
	var marker = new google.maps.Marker({
        position: new google.maps.LatLng(data.lat, data.long),
        map: map,
        title: <?php if(!empty($editable)) : ?>data.id + ': ' + <?php endif; ?> data.name + ' - ' + data.address,
        id: data.id,
        address: data.address,
        <?php if(!empty($editable)) : ?>
        draggable:true,
        <?php endif; ?>
        // icon: DocumentData.map_marker_image,
        animation: google.maps.Animation.DROP,
        icon: {
	        path: google.maps.SymbolPath.CIRCLE,
	        // path: 'M -2,-2 2,2 M 2,-2 -2,2', // XX
	        // path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z', // GoldStar
	        fillColor: markerColor,
	        fillOpacity: .62,
	        strokeColor: 'white',
	        strokeWeight: 2.5,
	        scale: 10
	    }
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
            enableHighAccuracy: true,
            timeout: 60000,
            maximumAge: 0
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

function updateBoundary(boundaryString) {
   
    // if(window.confirm("Update boundary for this territory?"))
    // do ajax
    $.ajax({
	    type: 'POST',
	    url: '',
	    data: {
		    boundaries: boundaryString,
		    'action': 'update-boundary'
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

function updateAddressTerritory(addressData, callback) {
	addressData['action'] = 'update-address-territory';
	
	$.ajax({
	    type: 'POST',
	    url: '',
	    data: addressData,
	    dataType: 'json',
	    error: function(jQxhr, status, error) {
		    console.log(status, error);
	    },
	    success: function(ret, status, jQxhr) {
		    console.log(status, ret);
		    callback(ret.data);
	    }
    });
}

$(function() {
    var map, markers, geocoder, infowindow, tracking, positionTimer, geoLoc, editText;
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