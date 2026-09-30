// google map script
// handles single place map (details page) and multi-place map (planner page)

function initMap() {
  // check if planner map exists (planner page)
  var plannerMapEl = document.getElementById('planner-map');
  if (plannerMapEl && typeof plannerPlaces !== 'undefined' && Array.isArray(plannerPlaces) && plannerPlaces.length > 0) {
    initPlannerMap(plannerMapEl);
  }

  // check if single place map exists (details page)
  var mapElement = document.getElementById('map-container');
  if (mapElement) {
    initSingleMap(mapElement);
  }
}

// Make initMap available globally for Google Maps callback
window.initMap = initMap;

// single place map on details page
function initSingleMap(mapElement) {
  var lat = parseFloat(mapElement.getAttribute('data-lat'));
  var lng = parseFloat(mapElement.getAttribute('data-lng'));
  var title = mapElement.getAttribute('data-title') || 'Location';

  if (isNaN(lat) || isNaN(lng)) return;

  var location = { lat: lat, lng: lng };

  // create the map
  var map = new google.maps.Map(mapElement, {
    zoom: 14,
    center: location,
    mapTypeId: 'roadmap',
    styles: getMapStyles()
  });

  // add marker on map
  var marker = new google.maps.Marker({
    position: location,
    map: map,
    title: title,
    animation: google.maps.Animation.DROP
  });

  // popup when marker is clicked
  var infoDiv = document.createElement('div');
  infoDiv.style.cssText = 'padding:8px; font-weight:600; color:#0f766e;';
  infoDiv.textContent = title;
  var infowindow = new google.maps.InfoWindow({
    content: infoDiv
  });

  marker.addListener('click', function () {
    infowindow.open(map, marker);
  });
}

// multi-place map on planner page
function initPlannerMap(mapElement) {
  // mawanella town center
  var mawanellaCenter = { lat: 7.2513, lng: 80.4522 };

  var map = new google.maps.Map(mapElement, {
    zoom: 11,
    center: mawanellaCenter,
    mapTypeId: 'roadmap',
    styles: getMapStyles()
  });

  var bounds = new google.maps.LatLngBounds();

  // add mawanella center marker
  var centerMarker = new google.maps.Marker({
    position: mawanellaCenter,
    map: map,
    title: 'Mawanella Town Center (Start Point)',
    icon: {
      url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
    }
  });
  bounds.extend(mawanellaCenter);

  var centerInfo = document.createElement('div');
  centerInfo.style.cssText = 'padding:8px; font-weight:700; color:#1e40af; font-size:14px;';
  centerInfo.textContent = 'Mawanella Town (Start Point)';
  var centerInfoWindow = new google.maps.InfoWindow({ content: centerInfo });
  centerMarker.addListener('click', function() {
    centerInfoWindow.open(map, centerMarker);
  });

  var routePathCoords = [mawanellaCenter];

  // add numbered markers for each selected place
  plannerPlaces.forEach(function(place, index) {
    if (!place.lat || !place.lng) return;

    var pos = { lat: parseFloat(place.lat), lng: parseFloat(place.lng) };
    routePathCoords.push(pos);

    var marker = new google.maps.Marker({
      position: pos,
      map: map,
      title: place.name,
      label: {
        text: String(index + 1),
        color: '#ffffff',
        fontWeight: '700',
        fontSize: '13px'
      },
      animation: google.maps.Animation.DROP
    });

    bounds.extend(pos);

    // info window content
    var infoContent = document.createElement('div');
    infoContent.style.cssText = 'padding:10px; max-width:220px;';
    infoContent.innerHTML = '<div style="font-weight:700; color:#0f766e; font-size:14px; margin-bottom:4px;">' 
      + (index + 1) + '. ' + escapeHtml(place.name) + '</div>'
      + '<div style="font-size:12px; color:#64748b;">'
      + '<i class="fa-solid fa-location-dot"></i> ' + place.distance + ' km from Mawanella<br>'
      + '<i class="fa-solid fa-hourglass-half"></i> Visit: ' + escapeHtml(place.time)
      + '</div>';

    var infowindow = new google.maps.InfoWindow({ content: infoContent });
    marker.addListener('click', function() {
      infowindow.open(map, marker);
    });
  });

  // draw polyline path connecting itinerary locations
  if (routePathCoords.length > 1) {
    var routePolyline = new google.maps.Polyline({
      path: routePathCoords,
      geodesic: true,
      strokeColor: '#0f766e',
      strokeOpacity: 0.8,
      strokeWeight: 4
    });
    routePolyline.setMap(map);
  }

  // fit map to show all markers
  if (plannerPlaces.length > 0) {
    map.fitBounds(bounds);
  }
}

// prevent xss in info windows
function escapeHtml(str) {
  var div = document.createElement('div');
  div.textContent = str || '';
  return div.innerHTML;
}

// shared map theme styles
function getMapStyles() {
  return [
    {
      "featureType": "all",
      "elementType": "geometry.fill",
      "stylers": [{ "weight": "2.00" }]
    },
    {
      "featureType": "all",
      "elementType": "geometry.stroke",
      "stylers": [{ "color": "#9c9c9c" }]
    },
    {
      "featureType": "all",
      "elementType": "labels.text",
      "stylers": [{ "visibility": "on" }]
    },
    {
      "featureType": "landscape",
      "elementType": "all",
      "stylers": [{ "color": "#f2f2f2" }]
    },
    {
      "featureType": "landscape",
      "elementType": "geometry.fill",
      "stylers": [{ "color": "#ffffff" }]
    },
    {
      "featureType": "landscape.man_made",
      "elementType": "geometry.fill",
      "stylers": [{ "color": "#ffffff" }]
    },
    {
      "featureType": "poi",
      "elementType": "all",
      "stylers": [{ "visibility": "off" }]
    },
    {
      "featureType": "road",
      "elementType": "all",
      "stylers": [{ "saturation": -100 }, { "lightness": 45 }]
    },
    {
      "featureType": "road",
      "elementType": "geometry.fill",
      "stylers": [{ "color": "#eeeeee" }]
    },
    {
      "featureType": "road",
      "elementType": "labels.text.fill",
      "stylers": [{ "color": "#7b7b7b" }]
    },
    {
      "featureType": "road",
      "elementType": "labels.text.stroke",
      "stylers": [{ "color": "#ffffff" }]
    },
    {
      "featureType": "road.highway",
      "elementType": "all",
      "stylers": [{ "visibility": "simplified" }]
    },
    {
      "featureType": "road.arterial",
      "elementType": "labels.icon",
      "stylers": [{ "visibility": "off" }]
    },
    {
      "featureType": "transit",
      "elementType": "all",
      "stylers": [{ "visibility": "off" }]
    },
    {
      "featureType": "water",
      "elementType": "all",
      "stylers": [{ "color": "#0f766e" }, { "visibility": "on" }]
    },
    {
      "featureType": "water",
      "elementType": "geometry.fill",
      "stylers": [{ "color": "#ccfbf1" }]
    },
    {
      "featureType": "water",
      "elementType": "labels.text.fill",
      "stylers": [{ "color": "#075985" }]
    },
    {
      "featureType": "water",
      "elementType": "labels.text.stroke",
      "stylers": [{ "color": "#ffffff" }]
    }
  ];
}

// DOM ready listener fallback
document.addEventListener('DOMContentLoaded', function() {
  if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
    initMap();
  }
});
