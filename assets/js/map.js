// google map script
// shows marker on the map for each place

function initMap() {
  const mapElement = document.getElementById('map-container');
  if (!mapElement) return;

  const lat = parseFloat(mapElement.getAttribute('data-lat')) || 7.2515;
  const lng = parseFloat(mapElement.getAttribute('data-lng')) || 80.4491;
  const title = mapElement.getAttribute('data-title') || 'Location';

  const location = { lat: lat, lng: lng };

  // create the map
  const map = new google.maps.Map(mapElement, {
    zoom: 14,
    center: location,
    mapTypeId: 'roadmap',
    styles: [
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
    ]
  });

  // add marker on map
  const marker = new google.maps.Marker({
    position: location,
    map: map,
    title: title,
    animation: google.maps.Animation.DROP
  });

  // popup when marker is clicked
  const infowindow = new google.maps.InfoWindow({
    content: '<div style="padding:8px; font-weight:600; color:#0f766e;">' + title + '</div>'
  });

  marker.addListener('click', function () {
    infowindow.open(map, marker);
  });
}
