<html>
<head>
	<meta name="viewport" content="width=device-width">
	<style>
	.territory-map-display {
	    width: 95%;
	    margin: 20px auto;
	    min-width: 600px;
	    height: 800px;
	}    
  	</style>
<body>
 
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
		
		DocumentData.map_marker_image = '/spa2/images/marker-icon.png';
		
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script src="/spa2/lib/jquery.min.js"></script>

<script>

if(typeof($) == 'undefined') var $ = jQuery.noConflict();

// MAIN METHODS
    
function initializeMap() {
	$('#territory-map-display').css('height', $(window).height())
	
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
	        
	        // extend the bounds to include each marker's position
			bounds.extend(markers[m].myLatlng);
			
	        // console.log(markers[m]);
	        // google.maps.event.addListener(markers[m].marker, "click", toggleBounce);
	        
	        <?php if(!empty($editable)) : ?>
		        // console.log('$editable');
		        google.maps.event.addListener(markers[m].marker, "dragend", function(e) {
		            // console.log(e);
		            // console.log(this);
		            // console.log(this.position.lat());
		            updateEntry(this, e);
		        });
			<?php endif; ?>
			
			infowindow = new google.maps.InfoWindow();
			// infowindow.setContent(marker.title);
				
/*
			google.maps.event.addListener(markers[m].marker, 'click', (function(map, marker) {
				console.log('marker', marker);
				
				// infowindow.open(map, marker);
			})(map, markers[m].marker));
*/
			
			markers[m].marker.addListener('click', function(e) {
				// console.log('e', e);
				// console.log('this', this);
				infowindow.setContent(this.title);
				infowindow.open(map, this);
			});
	
	    }
	    
	    // now fit the map to the newly inclusive bounds
		map.fitBounds(bounds);
	    
    }
}

function updateEntry(marker, e) {
  
    console.log(marker.id);
    console.log(marker.position.lat());
    console.log(marker.position.lng());
    
    // do ajax
    
}


function createMarker(map, data) {
	var marker = new google.maps.Marker({
        position: new google.maps.LatLng(data.lat, data.long),
        map: map,
        title: data.id + ': ' + data.name + ' - ' + data.address,
        id: data.id,
        <?php if(!empty($editable)) : ?>
        draggable:true,
        <?php endif; ?>
        // icon: DocumentData.map_marker_image,
        animation: google.maps.Animation.DROP,
	});
	
	return marker;
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
    var map, geocoder, infowindow;
    initializeMap();
});

</script>   
 
</body>
</html>