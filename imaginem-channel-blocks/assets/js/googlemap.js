jQuery( function( $ ) {

    var geocoder;

    if ( undefined !== window.elementor ) {

        elementor.hooks.addAction('panel/open_editor/widget', function (panel, model, view) {


            $('#googlemapaddress').on("submit", function( event ) {
                event.preventDefault();
                mthemeGmapFindAddress( $(this) );
            });

            function mthemeGmapFindAddress(ob) {
              var address = $(ob).parent().find('input').attr('value');
              if(address!=''){

                  geocoder = new google.maps.Geocoder;
                
                  geocoder.geocode({ 'address': address }, function(results, status) {
                      if (status == 'OK') {

                          var latiude = results[0].geometry.location.lat();
                          var longitude = results[0].geometry.location.lng();
                          model.setSetting('map_lat', latiude);
                          model.setSetting('map_lng', longitude);

                  } else {
                          alert("Geocode was not successful: " + status);
                      }
                  });
              }
            }
        });
    }
});