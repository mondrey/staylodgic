(function($) {
    var mthemeGoogleMaps = function($scope, $) {

        var snazzymap = [
  {
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#ebe3cd"
      }
    ]
  },
  {
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#523735"
      }
    ]
  },
  {
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#f5f1e6"
      }
    ]
  },
  {
    "featureType": "administrative",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#c9b2a6"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#dcd2be"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#ae9e90"
      }
    ]
  },
  {
    "featureType": "landscape.natural",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#93817c"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "geometry.fill",
    "stylers": [
      {
        "color": "#a5b076"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#447530"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f5f1e6"
      }
    ]
  },
  {
    "featureType": "road.arterial",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#fdfcf8"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#f8c967"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#e9bc62"
      }
    ]
  },
  {
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#e98d58"
      }
    ]
  },
  {
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#db8555"
      }
    ]
  },
  {
    "featureType": "road.local",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#806b63"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#8f7d77"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#ebe3cd"
      }
    ]
  },
  {
    "featureType": "transit.station",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#dfd2ae"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "geometry.fill",
    "stylers": [
      {
        "color": "#b9d3c2"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#92998d"
      }
    ]
  }
];
        var mapid = $scope.find('.mtheme-map'),
            maptype = $(mapid).data("mtheme-map-type"),
            zoom = $(mapid).data("mtheme-map-zoom"),
            map_lat = $(mapid).data("mtheme-map-lat"),
            map_lng = $(mapid).data("mtheme-map-lng"),
            defaultui = $(mapid).data("mtheme-map-defaultui"),
            zoomcontrol = $(mapid).data("mtheme-map-zoom-control"),
            zoomcontrolposition = $(mapid).data("mtheme-map-zoom-control-position"),
            maptypecontrol = $(mapid).data("mtheme-map-type-control"),
            maptypecontrolstyle = $(mapid).data("mtheme-map-type-control-style"),
            maptypecontrolposition = $(mapid).data("mtheme-map-type-control-position"),
            streetview = $(mapid).data("mtheme-map-streetview-control"),
            streetviewposition = $(mapid).data("mtheme-map-streetview-position"),
            customstyles = ($(mapid).data("mtheme-map-style") != '') ? $(mapid).data("mtheme-map-style") : '',
            infowindow_max_width = $(mapid).data("mtheme-map-infowindow-width"),
            active_info,
            infowindow,
            map;

            if ( customstyles != "" ) {
                snazzymap = customstyles;
            }

        var curPosition = new google.maps.LatLng(map_lat, map_lng);
        var myLatLng = { lat: parseFloat(map_lat), lng: parseFloat(map_lng) };

        if (maptypecontrolstyle == 'DROPDOWN_MENU') {
            maptypecontrolstyle = google.maps.MapTypeControlStyle.DROPDOWN_MENU;
        } else if (maptypecontrolstyle == 'HORIZONTAL_BAR') {
            maptypecontrolstyle = google.maps.MapTypeControlStyle.HORIZONTAL_BAR;
        } else {
            maptypecontrolstyle = google.maps.MapTypeControlStyle.DEFAULT;
        }

        if (maptypecontrolposition == 'TOP_CENTER') {
            maptypecontrolposition = google.maps.ControlPosition.TOP_CENTER;
        } else if (maptypecontrolposition == 'TOP_RIGHT') {
            maptypecontrolposition = google.maps.ControlPosition.TOP_RIGHT;
        } else if (maptypecontrolposition == 'LEFT_CENTER') {
            maptypecontrolposition = google.maps.ControlPosition.LEFT_CENTER;
        } else if (maptypecontrolposition == 'RIGHT_CENTER') {
            maptypecontrolposition = google.maps.ControlPosition.RIGHT_CENTER;
        } else if (maptypecontrolposition == 'BOTTOM_CENTER') {
            maptypecontrolposition = google.maps.ControlPosition.BOTTOM_CENTER;
        } else if (maptypecontrolposition == 'BOTTOM_RIGHT') {
            maptypecontrolposition = google.maps.ControlPosition.BOTTOM_RIGHT;
        } else if (maptypecontrolposition == 'BOTTOM_LEFT') {
            maptypecontrolposition = google.maps.ControlPosition.BOTTOM_LEFT;
        } else {
            maptypecontrolposition = google.maps.ControlPosition.TOP_LEFT;
        }

        if (zoomcontrolposition == 'TOP_CENTER') {
            zoomcontrolposition = google.maps.ControlPosition.TOP_CENTER;
        } else if (zoomcontrolposition == 'TOP_RIGHT') {
            zoomcontrolposition = google.maps.ControlPosition.TOP_RIGHT;
        } else if (zoomcontrolposition == 'LEFT_CENTER') {
            zoomcontrolposition = google.maps.ControlPosition.LEFT_CENTER;
        } else if (zoomcontrolposition == 'RIGHT_CENTER') {
            zoomcontrolposition = google.maps.ControlPosition.RIGHT_CENTER;
        } else if (zoomcontrolposition == 'BOTTOM_CENTER') {
            zoomcontrolposition = google.maps.ControlPosition.BOTTOM_CENTER;
        } else if (zoomcontrolposition == 'BOTTOM_RIGHT') {
            zoomcontrolposition = google.maps.ControlPosition.BOTTOM_RIGHT;
        } else if (zoomcontrolposition == 'BOTTOM_LEFT') {
            zoomcontrolposition = google.maps.ControlPosition.BOTTOM_LEFT;
        } else if (zoomcontrolposition == 'TOP_LEFT') {
            zoomcontrolposition = google.maps.ControlPosition.TOP_LEFT;
        } else {
            zoomcontrolposition = google.maps.ControlPosition.RIGHT_BOTTOM;
        }

        if (streetviewposition == 'TOP_CENTER') {
            streetviewposition = google.maps.ControlPosition.TOP_CENTER;
        } else if (streetviewposition == 'TOP_RIGHT') {
            streetviewposition = google.maps.ControlPosition.TOP_RIGHT;
        } else if (streetviewposition == 'LEFT_CENTER') {
            streetviewposition = google.maps.ControlPosition.LEFT_CENTER;
        } else if (streetviewposition == 'RIGHT_CENTER') {
            streetviewposition = google.maps.ControlPosition.RIGHT_CENTER;
        } else if (streetviewposition == 'BOTTOM_CENTER') {
            streetviewposition = google.maps.ControlPosition.BOTTOM_CENTER;
        } else if (streetviewposition == 'BOTTOM_RIGHT') {
            streetviewposition = google.maps.ControlPosition.BOTTOM_RIGHT;
        } else if (streetviewposition == 'BOTTOM_LEFT') {
            streetviewposition = google.maps.ControlPosition.BOTTOM_LEFT;
        } else if (streetviewposition == 'TOP_LEFT') {
            streetviewposition = google.maps.ControlPosition.TOP_LEFT;
        } else {
            streetviewposition = google.maps.ControlPosition.RIGHT_BOTTOM;
        }

        CustomMarker.prototype = new google.maps.OverlayView();

        function CustomMarker(opts) {
            this.setValues(opts);
        }

        CustomMarker.prototype.draw = function() {
            var self = this;
            var div = this.div;
            if (!div) {
                div = this.div = $("" +
                    "<div class=\'map-marker\'>" +
                    "<div class=\'map-dot\'></div>" +
                    "<div class=\'map-pulse\'></div>" +
                    "</div>" +
                    "")[0];
                this.pinWrap = this.div.getElementsByClassName("pin-wrap");
                this.pin = this.div.getElementsByClassName("pin");
                this.pinShadow = this.div.getElementsByClassName("shadow");
                div.style.position = "absolute";
                div.style.cursor = "pointer";
                var panes = this.getPanes();
                panes.overlayImage.appendChild(div);
                google.maps.event.addDomListener(div, "click", function(event) {
                    google.maps.event.trigger(self, "click", event);
                });
            }
            var point = this.getProjection().fromLatLngToDivPixel(curPosition);

            if (point) {
                div.style.left = point.x + "px";
                div.style.top = point.y + "px";
            }
        };

        function initMap() {

            var map = new google.maps.Map(mapid[0], {
                center: myLatLng,
                zoom: zoom,
                disableDefaultUI: defaultui,
                zoomControl: zoomcontrol,
                zoomControlOptions: {
                    position: zoomcontrolposition
                },
                mapTypeId: maptype,
                mapTypeControl: maptypecontrol,
                mapTypeControlOptions: {
                    style: maptypecontrolstyle,
                    position: maptypecontrolposition
                },
                streetViewControl: streetview,
                streetViewControlOptions: {
                    position: streetviewposition
                },
                styles: snazzymap,
            });

            var marker = new CustomMarker({
                map: map,
                position: myLatLng,
            });
        }
        
        function addInfoWindow(marker, content) {
            google.maps.event.addListener(marker, 'click', function() {
                if (!infowindow) {
                    infowindow = new google.maps.InfoWindow({
                        maxWidth: infowindow_max_width
                    });
                }
                infowindow.setContent(content);
                infowindow.open(map, marker);
            });
        }

        initMap();

    };

    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/google-map.default', mthemeGoogleMaps);
    });

})(jQuery);