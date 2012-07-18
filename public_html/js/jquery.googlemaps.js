function initialize() {
  var latLng = new google.maps.LatLng(-36.827281,-73.055001);
$('#cordenadagooglemaps').val('-36.827281,-73.055001');  
  var map = new google.maps.Map(document.getElementById('mapCanvas'), {
    zoom: 16,
    center: latLng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });
  var marker = new google.maps.Marker({
    position: latLng,
    title: 'Point A',
    map: map,
    draggable: true
  });  
  google.maps.event.addListener(marker, 'drag', function() {
      var cordenada = marker.getPosition();
      $('#cordenadagooglemaps').val(cordenada.lat()+','+cordenada.lng());
  });
}
google.maps.event.addDomListener(window, 'load', initialize);
$(document).ready(function(){
   $('#cordenadagooglemaps-element').append('<div id="mapCanvas" style="width: 780px; height: 250px"></div>');
});
