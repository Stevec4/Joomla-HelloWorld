var map;
var ajaxurl;


jQuery(document).ready(function() {
    
    // get the data passed from Joomla PHP
    // params is a Javascript object with properties for the map display: 
    // centre latitude, centre longitude and zoom, and the helloworld greeting
    const params = Joomla.getOptions('params');
	ajaxurl = params.ajaxurl; 
    
    // We'll use OpenLayers to draw the map (http://openlayers.org/)
    
    // Openlayers uses an x,y coordinate system for positions
    // We need to convert our lat/long into an x,y pair which is relative
    // to the map projection we're using, viz Spherical Mercator WGS 84
    const x = parseFloat(params.longitude);
    const y = parseFloat(params.latitude);
    const mapCentre = ol.proj.fromLonLat([x, y]); // Spherical Mercator is assumed by default
    
    // To draw a map, Openlayers needs:
    // 1. a target HTML element into which the map is put
    // 2. a map layer, which can be eg a Vector layer with details of polygons for
    //    country boundaries, lines for roads, etc, or a Tile layer, with individual
    //    .png files for each map tile (256 by 256 pixel square).
    // 3. a view, specifying the 2D projection of the map (default Spherical Mercator),
    //    map centre coordinates and zoom level
    map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({  // we'll get the tiles from the OSM server
                source: new ol.source.OSM()
            })
        ],
        view: new ol.View({  // default is Spherical Mercator projection
            center: mapCentre,
            zoom: params.zoom
        })
    });
    
    // Now we add a marker for our Helloworld position
    // To do that, we specify it as a Point Feature, and we add styling 
    // to define how that Feature is presented on the map
    var helloworldPoint = new ol.Feature({geometry: new ol.geom.Point(mapCentre)});
    // we'll define the style as a red 5 point star with blue edging
    const redFill = new ol.style.Fill({
        color: 'red'
    });
    const blueStroke = new ol.style.Stroke({
        color: 'blue',
        width: 3
    });
    const star = new ol.style.RegularShape({
        fill: redFill,
        stroke: blueStroke,
        points: 5,
        radius1: 20,   // outer radius of star
        radius2: 10,   // inner radius of star
    })
    helloworldPoint.setStyle(new ol.style.Style({
        image: star
    }));
    // now we add the feature to the map via a Vector source and Vector layer
    const vectorSource = new ol.source.Vector({});
    vectorSource.addFeature(helloworldPoint);
    const vector = new ol.layer.Vector({
        source: vectorSource
    });
    map.addLayer(vector);
    
    // If a user clicks on the star, then we'll show the helloworld greeting
    // The greeting will go into another HTML element, with id="greeting-container"
    // and this will be shown as an Overlay on the map
    var overlay = new ol.Overlay({
        element: document.getElementById('greeting-container'),
    });
    map.addOverlay(overlay);
        
    // Finally we add the onclick listener to display the greeting when the star is clicked
    // The way this works is that the onclick listener is attached to the map,
    // and then it works out which Feature or Features have been hit
    map.on('click', function(e) {
        let markup = '';
        let position;
        map.forEachFeatureAtPixel(e.pixel, function(feature) {  // for each Feature hit
            markup = params.greeting;
            position = feature.getGeometry().getCoordinates();
        }, {hitTolerance: 5});  // tolerance of 5 pixels
        if (markup) {
            document.getElementById('greeting-container').innerHTML = markup;
            overlay.setPosition(position);
        } else {
            overlay.setPosition();  // this hides it, if we click elsewhere
        }
    });    
});


function getMapBounds(){
    var mercatorMapbounds = map.getView().calculateExtent(map.getSize());
    var latlngMapbounds = ol.proj.transformExtent(mercatorMapbounds,'EPSG:3857','EPSG:4326');
    return { minlat: latlngMapbounds[1],
             maxlat: latlngMapbounds[3],
             minlng: latlngMapbounds[0],
             maxlng: latlngMapbounds[2] }
}
    
function searchHere() {
    var mapBounds = getMapBounds();
    var token = jQuery("#token").attr("name");
    jQuery.ajax({
        url: ajaxurl,
        data: { [token]: "1", task: "mapsearch", view: "helloworld", format: "json", mapBounds: mapBounds },
        success: function(result, status, xhr) { displaySearchResults(result); },
        error: function() { console.log('ajax call failed'); },
    });
}

function displaySearchResults(result) {
    if (result.success) {
        var html = "";
        for (var i=0; i<result.data.length; i++) {
            html += '<p><a href="' + result.data[i].url + '">' +
                result.data[i].greeting + '</a>' +
                " @ " + result.data[i].latitude + 
                ", " + result.data[i].longitude + "</p>";
        }
        jQuery("#searchresults").html(html);
    } else {
        var msg = result.message;
        if ((result.messages) && (result.messages.error)) {
            for (var j=0; j<result.messages.error.length; j++) {
                msg += "<br/>" + result.messages.error[j];
            }
        }
        jQuery("#searchresults").html(msg);
    }
}