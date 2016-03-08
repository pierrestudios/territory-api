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
		</div>
 	</div>
 	
	<div id="content">
  		<div class="territory-map-display" id="territory-map-display"></div>
	</div>
  
    <script>        
		var DocumentData = {};
		<?php 
			// var_dump($center);
			
		?>
		
		DocumentData.user_marker_image = '/spa2/images/marker-user.gif';
		
	</script>    
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing"></script>
    <script src="/spa2/lib/jquery.min.js"></script>

<script>

if(typeof($) == 'undefined') var $ = jQuery.noConflict();

// MAIN METHODS
    
function initializeMap() {
	$('#territory-map-display').css('height', ($(window).height() - 140));
	
	map = new google.maps.Map(document.getElementById('territory-map-display'), {
    	center: {lat: <?php echo $center['lat']; ?>, lng: <?php echo $center['long']; ?>},
    	zoom: 17
  	});
  	var boundary;
  	
  	var colors = {
	  	orange: '#fb8c00',
	  	orangeLite: '#FFE8CE'
  	}
  	
  	function saveBoundary(event) {
	  	console.log('event', event);
	  	infoWindow = new google.maps.InfoWindow;
	  	infoWindow.setContent('<button class="save-boundary">Save boundary</button>');
	  	infoWindow.setPosition(event.latLng);
		infoWindow.open(map);
	}
  	
  	var drawingManager = new google.maps.drawing.DrawingManager({
	    drawingMode: google.maps.drawing.OverlayType.POLYGON,
	    drawingControl: true,
	    drawingControlOptions: {
	      position: google.maps.ControlPosition.TOP_CENTER,
	      drawingModes: [
	        // google.maps.drawing.OverlayType.MARKER,
	        // google.maps.drawing.OverlayType.CIRCLE,
	        google.maps.drawing.OverlayType.POLYGON,
	        // google.maps.drawing.OverlayType.POLYLINE,
	        // google.maps.drawing.OverlayType.RECTANGLE
	      ]
	    },
	    // markerOptions: {icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'},
/*
	    circleOptions: {
	      fillColor: '#ffff00',
	      fillOpacity: 1,
	      strokeWeight: 5,
	      clickable: false,
	      editable: true,
	      zIndex: 1
	    }
*/
		polygonOptions: {
			fillColor: colors.orangeLite,
			fillOpacity: .5,
			strokeWeight: 5,
			strokeColor: colors.orange,
			// clickable: false,
			editable: true,
			zIndex: 1
		}
	});
	
	drawingManager.setMap(map);
	
	google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
	  	if (event.type == google.maps.drawing.OverlayType.CIRCLE) {
	    var radius = event.overlay.getRadius();
	    	console.log('radius', radius);
	  	}
	  	var paths = event.overlay.getPath();
	  	// var bounds = event.overlay.getBounds();
	  	console.log('paths', paths);
	  	// console.log('bounds', bounds);
	  	boundary = [];
	  	paths.forEach(function(obj, number) {
		  	// console.log('obj', obj);
		  	console.log('number', number);
		  	console.log('Lat', obj.lat());
		  	console.log('Long', obj.lng());
		  	boundary.push({'lat': obj.lat(), 'lng': obj.lng()});
	  	});
	  	console.log('boundary', boundary);
	  	
	  	var infoWindow;
	  	google.maps.event.addListener(event.overlay, 'click', saveBoundary);
	});
	
  	
  	// Load the saved Boundary
  	
  	// boundaries data
  	var savedPoly = '<?php echo $boundaries; ?>'; // '[{"lat":25.889756921543427,"lng":-80.19868556410074},{"lat":25.8898727460337,"lng":-80.19457641988993},{"lat":25.891252979129924,"lng":-80.19464079290628},{"lat":25.891542536472766,"lng":-80.19509140402079},{"lat":25.891957567425507,"lng":-80.19529525190592},{"lat":25.89263319608763,"lng":-80.1953274384141},{"lat":25.892498070664594,"lng":-80.19796673208475},{"lat":25.892160256430195,"lng":-80.19797746092081},{"lat":25.892140952730454,"lng":-80.19878212362528}]';
  	
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
	if(savedPoly) terrCoordinates.setPaths(JSON.parse(savedPoly));
	terrCoordinates.setMap(map);
  	terrCoordinates.addListener('click', saveBoundary);
  	google.maps.event.addListener(terrCoordinates.getPath(), 'set_at', function() {
	  	console.log('Vertex moved on outer path.');
	  	boundary = [];
	  	terrCoordinates.getPath().forEach(function(obj, number) {
		  	// console.log('obj', obj);
		  	console.log('number', number);
		  	console.log('Lat', obj.lat());
		  	console.log('Long', obj.lng());
		  	boundary.push({'lat': obj.lat(), 'lng': obj.lng()});
	  	});
	  	console.log('boundary', boundary);
	});
	
	$(document).on('click', '.save-boundary', function(e) {
	  	e.stopPropagation();
	  	e.preventDefault();
	  	var boundaryString = JSON.stringify(boundary);
	  	console.log('boundary', boundaryString);
	  	updateEntry(boundaryString);
  	});
  	
    isMapInitialized=true; 
    
}

function updateEntry(boundaryString) {
   
    // if(window.confirm("Update boundary for this territory?"))
    // do ajax
    $.ajax({
	    type: 'POST',
	    url: '',
	    data: {
		    boundaries: boundaryString
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


$(function() {
    var map, geocoder, infowindow, tracking, positionTimer, geoLoc;
    initializeMap();

});

</script>   

	</body>
</html>