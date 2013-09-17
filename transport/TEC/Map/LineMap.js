// Graphics data
var stopIcon = L.icon({
    iconUrl:  '/images/commons/City%20locator%2015.svg-28px.png',
});

function DrawMap (line, infoLayerGroup) {
	// Map centered to Charleroi
	var map = L.map('map').setView([50.411143, 4.447746], 12);

	// OSM and Google Maps tiles
	var OpenStreetMap = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
	    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
	});
	var GoogleMapsRoadmap = new L.Google('ROADMAP');
	var GoogleMapsSat = new L.Google('SATELLITE');
	var GoogleMapsHybrid = new L.Google('HYBRID');

//        map.addLayer(GoogleMapsHybrid);
        map.addLayer(OpenStreetMap);
        map.addLayer(infoLayerGroup);

}

// Gets line data
function DrawLine (line) {
  require(["dojo/request/xhr"], function(xhr){
    xhr("GetLineCoordinates.php?line=" + line, {
        handleAs: "json"
    }).then(function (data) {
        // Processes data
        var markersStop = new L.LayerGroup();
        for (var i = 0 ; i < data.length ; i++) {
            //Each stop is an object with the following properties: id desc lng lat
            var stop = data[i];
            markersStop.addLayer(L.marker([stop.lat, stop.lng], {icon: stopIcon})
               .bindPopup(stop.desc)
               .openPopup());
        }
	DrawMap(line, markersStop);
    }, function (err) {
        // TODO: handles error
    }, function (evt) {
        // TODO: handles progress event (XHR2)
    });
  });
}