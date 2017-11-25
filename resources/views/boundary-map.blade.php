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
			<div>
				<strong class="number">Territory # {{$number}}</strong> 
				<span>{{$location}}</span>
			</div>
			<div>
				<span>To edit current boundary, click on <strong>Hand</strong> tool.</span> <!-- <img src="/theme-all/images/map-boundary-edit.png" style="height: 25px"/> -->
				<span>Then click on drawn Polygon, click on <strong>Save Boundary</strong> to save changes.</span> 
				<span>To add new boundary, click on <strong>Polygon</strong> tool.</span> 
			</div>

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
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing&key=AIzaSyATUXZryBeH2aG9JfWLefyqh0r6-u85N40"></script>
    <script src="/spa2/lib/jquery.min.js"></script>

<script>

if(typeof($) == 'undefined') var $ = jQuery.noConflict();

// MAIN METHODS
    
function initializeMap() {
	/*
	var newHeight = ($(window).height() - 140);
	console.log('screen.height', screen.height )
	console.log('$(document).height()', $(document).height())
	console.log('$(window).height()', $(window).height())
	console.log('($(window).height() - 140)', ($(window).height() - 140))
	console.log('initializeMap', newHeight)
	*/
	$('#territory-map-display').css('height', ($(document).height() - 140));
	
	map = new google.maps.Map(document.getElementById('territory-map-display'), {
    	center: {lat: <?php echo empty($center) ? '' : $center['lat']; ?>, lng: <?php echo empty($center) ? '' : $center['long']; ?>},
    	zoom: 17
  	});
  	var boundary;
  	
  	var colors = {
	  	orange: '#fb8c00',
	  	orangeLite: '#FFE8CE'
  	}
  	
  	function saveBoundary(event) {
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
	        google.maps.drawing.OverlayType.POLYGON,
	      ]
	    },

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
	  	var paths = event.overlay.getPath();
	  	// console.log('paths', paths);
	  	boundary = [];
	  	paths.forEach(function(Latlng, number) {
		  	boundary.push({'lat': Latlng.lat(), 'lng': Latlng.lng()});
	  	});
	  	// console.log('boundary', boundary);
	  	
	  	var infoWindow;
	  	google.maps.event.addListener(event.overlay, 'click', saveBoundary);
	});
	
  	
  	// Load the saved Boundary
  	
  	// boundaries data
  	var savedPoly = '<?php echo $boundaries; ?>'; 
  	
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
	  	// console.log('Vertex moved on outer path.');
	  	boundary = [];
	  	terrCoordinates.getPath().forEach(function(Latlng, number) {
		  	boundary.push({'lat': Latlng.lat(), 'lng': Latlng.lng()});
	  	});
	  	// console.log('boundary', boundary);
	});
	
	$(document).on('click', '.save-boundary', function(e) {
	  	e.stopPropagation();
	  	e.preventDefault();
	  	var boundaryString = JSON.stringify(boundary);
	  	// console.log('boundary', boundaryString);
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