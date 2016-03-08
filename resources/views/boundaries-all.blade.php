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
			<div style="text-align: left; float: left;">TERITWA # <strong class="number">All Territories</strong> </div>
		</div>
 	</div>
 	
	<div id="content">
  		<div class="territory-map-display" id="territory-map-display"></div>
	</div>
    
    <script src="https://maps.googleapis.com/maps/api/js?libraries=drawing"></script>
    <script src="/spa2/lib/jquery.min.js"></script>

<script>

if(typeof($) == 'undefined') var $ = jQuery.noConflict();

// MAIN METHODS
    
function initializeMap() {
	$('#territory-map-display').css('height', ($(window).height() - 140));
	
	map = new google.maps.Map(document.getElementById('territory-map-display'), {
    	center: {lat: 25.927852049404084, lng: -80.19762380419922 },
    	zoom: 16
  	});
  	
  	colors = {
	  	orange: '#fb8c00',
	  	orangeLite: '#FFE8CE'
  	}
  	 
  	// Load the saved Boundary
  	
  	// Construct the polygons
  	<?php if(!empty($territories)) : ?>
  	<?php foreach($territories as $k => $territory) : ?>
  	  		
  		<?php if(!empty($territory->boundaries)) : ?>
  		
  			drawTerritoryBoundary('<?php echo($territory->id)?>', '<?php echo($territory->number)?>', '<?php echo($territory->boundaries)?>');
			
		<?php endif; ?>
			
	<?php endforeach; ?>
	<?php //foreach($territories as $k => $territory) : ?>
	<?php //if(!empty($territory->boundaries)) echo 'showInfo({new google.maps.LatLng(boundaryPaths[i].lat, boundaryPaths[i].lng)}, '.$territory->id.', '.$territory->number.');'; ?>
	<?php //endforeach; ?>
	
	<?php endif; ?>
 
    isMapInitialized=true; 
    
}

function drawTerritoryBoundary(id, number, path) {
	var boundary = new google.maps.Polygon({
	    strokeColor: colors.orange,
	    strokeWeight: 5,
	    fillColor: colors.orangeLite,
	    fillOpacity: 0.5,
		zIndex: 1
	});
	
	boundary.setPaths(JSON.parse(path));
	boundary.setMap(map);
	boundary.addListener('click', function(event){showInfo(event, id, number)});

	showInfo({latLng: getPathCenter(path)}, id, number);
	// addBoundaryMarker(number, path); 
}

function addBoundaryMarker(number, path) {
	
	if(number==1) { 
		console.log('path', path);
		var boundaryPaths = JSON.parse(path);
		console.log('boundaryPaths', boundaryPaths[0]);
		
	var bounds = new google.maps.LatLngBounds(boundaryPaths[0], boundaryPaths[1]);
	// boundaryPaths.forEach(function(latLng, number) {
	for(i in boundaryPaths) {	
		bounds.extend(new google.maps.LatLng(boundaryPaths[i].lat, boundaryPaths[i].lng));
	};
	console.log('bounds', bounds.getCenter().toString());
	
/*
	var x1, x2, y1, y2, center;
	boundary.getPath().forEach(function(latLng, index) {
		// console.log('latLng.lat() ' + index, latLng.lat());
		
		x1 = (x1 ? (x1 > latLng.lat() ? latLng.lat() : x1 ) : latLng.lat()); // the lowest x coordinate
		console.log('x1', x1);
		
		x2 = (x2 ? (x2 < latLng.lat() ? latLng.lat() : x2 ) : latLng.lat()); // the highest x coordinate
		console.log('x2', x2);
		
		y1 = (y1 ? (y1 > latLng.lng() ? latLng.lng() : y1 ) : latLng.lng()); // the lowest y coordinate
		console.log('y1', y1);
		
		y2 = (y2 ? (y2 < latLng.lng() ? latLng.lng() : y2 ) : latLng.lng()); // the highest y coordinate
		console.log('y2', y2);
	});
	console.log('lowest x coordinate', x1);
	console.log('highest x coordinate', x2);
	console.log('lowest y coordinate', y1);
	console.log('highest y coordinate', y2); 
	var lat = (x1 + ((x2 - x1) / 2));
	var lng = (y1 + ((y2 - y1) / 2));
	console.log('center lat', lat); 
	console.log('center lng', lng); 
	
	center = new google.maps.LatLng(lat, lng);
	
	
*/
	
	getTerritorySymbol(number, function(symbolPath) {
		new google.maps.Marker({
	    	position: bounds.getCenter(),
			map: map,
			icon: {
				path: symbolPath,
				fillColor: 'blue',
				fillOpacity: .5,
				strokeColor: 'white',
				strokeWeight: 1.5,
				scale: 12
	      	}
	    });
	}); 
	//}, 2000);
	}
	
}

function getPathCenter(path) {
	var boundaryPaths = JSON.parse(path);
		// console.log('boundaryPaths', boundaryPaths[0]);
		
	var bounds = new google.maps.LatLngBounds(boundaryPaths[0], boundaryPaths[1]);
	// boundaryPaths.forEach(function(latLng, number) {
	for(i in boundaryPaths) {	
		bounds.extend(new google.maps.LatLng(boundaryPaths[i].lat, boundaryPaths[i].lng));
	};
	// console.log('bounds', bounds.getCenter().toString());
	return bounds.getCenter();
}

function showInfo(event, id, number) {
	infowindows = window.infowindows || [];
  	// console.log('id', id);
  	// console.log('number', number);
  	// console.log('event', event);
  	// console.log('infowindows', infowindows);
  	if(infowindows && infowindows[number]) 
  		infowindows[number].close();
  	else
  		infowindows[number] = new google.maps.InfoWindow;
  	infowindows[number].setContent('<h4>Territory '+ number +'</h4>'); // <h4>ID: '+ id +'</h4>
  	infowindows[number].setPosition(event.latLng);
	infowindows[number].open(map);
}

function getTerritorySymbol(number, callback) {
	// var svgEl = document.getElementById("symbols-1");
    document.getElementById("territory-symbols").addEventListener("load", function() {
	   var svgDoc = this.getSVGDocument();
	   // console.log('svgDoc', svgDoc);
	   var symbol = svgDoc.getElementById("Symbol_" + number);
	   // console.log('Symbol ' + number, symbol);
	   var paths = symbol.getElementsByTagName('path'); 
	   // console.log('path', paths);
	   var symbolPath = paths[0].getAttribute('d');
	   // console.log('data', data);
	   
	   callback(symbolPath);
	   
	});
}
 


$(function() {
    var map, colors, infowindow, infowindows = [];
    initializeMap();
});

</script>   

<object data="/api-assets/boundary-map-markers/territory-symbols.svg" type="image/svg+xml" id="territory-symbols" class="hidden"></object>
 
</body>
</html>