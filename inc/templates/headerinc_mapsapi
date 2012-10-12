<script src="http://maps.googleapis.com/maps/api/js?key={$this->api_key}&amp;sensor=false" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	var places = new Array();
	{$places_script}
	var map;
		
	initialize();
	
	function initialize() {
		var myOptions = {
			center: new google.maps.LatLng({$this->options[mapcenter]}),
			zoom: 2,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		map = new google.maps.Map(document.getElementById("{$this->options[canvas_name]}"), myOptions);
		infoWindow = new google.maps.InfoWindow();
		
		google.maps.event.addListenerOnce(map, 'tilesloaded', parseMarkers);
		
		
	}
	
	function createMarker(map, place) {
		var marker = new google.maps.Marker({
					position: new google.maps.LatLng(place.lat, place.lng), 
					map: map,
					title: place.title
		});
		
		google.maps.event.addListener(marker, 'click', function() {
			infoWindow.setContent('<div align="left"><a href="'+ place.link +'" target="_blank"><strong>'+ place.title +'</strong></a><br />' + place.otherinfo + '</div>');
			infoWindow.open(map, marker);
		});
	}
	
	function parseMarkers() {
		for(var i=0;i<places.length; i++) {
			createMarker(map, places[i]);
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