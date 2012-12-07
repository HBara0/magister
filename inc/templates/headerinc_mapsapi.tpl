<script src="http://maps.googleapis.com/maps/api/js?key={$this->api_key}&amp;sensor=false" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	//var places = new Array();
	var map;
	{$places_script}

	function mapInitialize(overlayType) {
		var myOptions = {
			center: new google.maps.LatLng({$this->options[mapcenter]}),
			zoom: {$this->options[zoom]},
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		map = new google.maps.Map(document.getElementById("{$this->options[canvas_name]}"), myOptions);
		infoWindow = new google.maps.InfoWindow();

		if(overlayType == '') {
			overlayType = parseMarkers;
		}
		google.maps.event.addListenerOnce(map, 'tilesloaded', overlayType);
	}

	function createMarker(map, place) {
		var marker = new google.maps.Marker({
					position: new google.maps.LatLng(place.lat, place.lng),
					map: map,
					title: place.title,
					icon: {
						path: google.maps.SymbolPath.CIRCLE,
						scale: 2,
						strokeOpacity: 0.5,
						strokeColor: '#993300',
						strokeWeight: 3
				  	}	
		});

		if(place.hasInfoWindow == 1) {
			google.maps.event.addListener(marker, 'click', function() {
				infoWindow.setContent('<div align="left"><a href="'+ place.link +'" target="_blank"><strong>'+ place.title +'</strong></a><br />' + place.otherinfo + '</div>');
				infoWindow.open(map, marker);
			});
		}
	}

	function parseMarkers() {
		for(var key in places) {
			for(var key2 in places[key]) {
				if(typeof places[key][key2].id == 'undefined') {
					for(var key3 in places[key][key2]) {
						createMarker(map, places[key][key2][key3]);
					}
				}
				else
				{
					createMarker(map, places[key][key2]);
				}
			}
		}
	}

	function createPolyline(map, place, poly) {
		var position = new google.maps.LatLng(place.lat, place.lng);

		var path = poly.getPath();
 		path.push(position);


		place.title = path.getLength().toString();
		createMarker(map, place);
	}

	function parsePolylines() {
		var poly = new Array();
		
		for(var key in places) {
			poly[key] = new google.maps.Polyline({strokeColor: '#993300', strokeOpacity: 0.5, strokeWeight: 3});
			poly[key].setMap(map);
		
			for(var key2 in places[key]) {
				if(typeof places[key][key2].id == 'undefined') {
					for(var key3 in places[key][key2]) {
						createPolyline(map, places[key][key2][key3], poly[key]);
					}
				}
				else
				{
					createPolyline(map, places[key][key2], poly[key]);
				}
			}
		}
	}

	/* Get location if browswer supports that - START */
	var current_lat = '';
	var current_lng = '';
	if(typeof navigator.geolocation != "undefined") {
		function showLocation(position) {
			current_lat = position.coords.latitude;
			current_lng = position.coords.longitude;

			createMarker(map, {title:"Current Location",lat:current_lat,lng:current_lng});
		}
		navigator.geolocation.getCurrentPosition(showLocation);
	}
	/* Get location if browswer supports that - END */
});
</script>