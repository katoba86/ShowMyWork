<template>


  <div v-if="loaded" id="vmap" rel="vmap" style="height:100vh;width:100%;display:block"></div>

</template>
<script type="text/babel">
/*@ global google */
const loadGoogleMapsApi = require('load-google-maps-api');

// load('AIzaSyCXsoTPEw4OrhMBevARWN76Fz1-4WipVEU');

export default {
  name:'VMap',
  props:{
    isRoute:{
      type:Boolean,
      require:false,
      default:false
    },
    height:{
      type:String,
      required:false,
      default:'50vh'
    },
    detected:{
      type:Array,
      required:false
    },
    lat: {

      required: false
    },
    lon: {

      required: false
    },
  },
  components: {

  },

  watch:{
    detected(){
      if(!this.isRoute) {
        this.drawMarkers();
      }
    }
  },
  data() {
    return {
      map:null,
      infoWindow:null,
      polyline: {
        latlngs: [],
        color: 'green'
      },
      detectedSteps:[],
      zoom: 15,
      loaded:false,
      center: [this.lat, this.lon],
      markers:[],

    }
  },

  mounted(){

    if(!this.isRoute) {

      this.loaded = true;
      this.loadMap();

    }else{
      this.detectedSteps = window.steps;
      this.loaded = true;
      this.loadMap();


    }
  },
  methods:{

    loadMap(){

      loadGoogleMapsApi({
        key:'AIzaSyCXsoTPEw4OrhMBevARWN76Fz1-4WipVEU',

        sensor:'false'
      }).then( (googleMaps)=> {
        this.initMap(googleMaps);
      }).catch(function (error) {
        console.error(error)
      });
    },
    initMap(){


      // eslint-disable-next-line no-undef
      this.map = new google.maps.Map(document.getElementById("vmap"),
          {
            zoom: (this.isRoute)?14:18,
            // eslint-disable-next-line no-undef
            center:(this.isRoute)?new google.maps.LatLng(this.detectedSteps[0].lat,this.detectedSteps[0].lng):new google.maps.LatLng(this.lat,this.lon),
            controlSize: 24,
            draggable:  !this.isRoute,
            zoomControl:  !this.isRoute,
            scrollwheel:  !this.isRoute,
            disableDoubleClickZoom: !this.isRoute,
            disableDefaultUI:  this.isRoute
          });


      let styles = [{
        "featureType": "landscape",
        "stylers": [{"hue": "#F1FF00"}, {"saturation": -27.4}, {"lightness": 9.4}, {"gamma": 1}]
      }, {
        "featureType": "road.highway",
        "stylers": [{"hue": "#89C144"}, {"saturation": -100}, {"lightness": 36.4}, {"gamma": 1}]
      }, {
        "featureType": "road.arterial",
        "stylers": [{"hue": "#00FF4F"}, {"saturation": 0}, {"lightness": 0}, {"gamma": 1}]
      }, {
        "featureType": "road.local",
        "stylers": [{"hue": "#FFB300"}, {"saturation": -38}, {"lightness": 11.2}, {"gamma": 1}]
      }, {
        "featureType": "water",
        "stylers": [{"hue": "#00B6FF"}, {"saturation": 4.2}, {"lightness": -63.4}, {"gamma": 1}]
      }, {
        "featureType": "poi",
        "stylers": [{"hue": "#9FFF00"}, {"saturation": 0}, {"lightness": 0}, {"gamma": 1}]
      }];
      // eslint-disable-next-line no-undef
      let styledMap = new google.maps.StyledMapType(styles,
          {name: "Styled Map"});
      this.map.mapTypes.set('map_style', styledMap);
      this.map.setMapTypeId('map_style');
      // eslint-disable-next-line no-undef
      this.infoWindow =  new google.maps.InfoWindow();
      if(!this.isRoute){
        this.drawMarkers();
      // eslint-disable-next-line no-undef
        google.maps.event.trigger(this.markers[0], 'click');

      }else{
        this.preparePolyLine();
        this.drawMarkers();

      }

    },



    preparePolyLine(){
      for (let item of this.detectedSteps) {
        // eslint-disable-next-line no-undef
        this.polyline.latlngs.push(new google.maps.LatLng(item.lat,item.lng));
      }
// eslint-disable-next-line no-undef
      let path = new google.maps.Polyline({
        path: this.polyline.latlngs,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
      });

      path.setMap(this.map);


    },
    drawMarkers(infoWindows = false){
      // eslint-disable-next-line no-undef
      let bounds = new google.maps.LatLngBounds();

      if(!this.isRoute){
        this.detectedSteps = this.detected;
      }

      for (let item of this.detectedSteps){

        let m = {};
        if(item.hasOwnProperty('location')) {
          m = {
            name: (item.hasOwnProperty('name'))?item.name:null,
            lat: item.location.lat,
            lng: item.location.lng,
            index: item.id,
            // eslint-disable-next-line no-undef
            pos:new google.maps.LatLng(item.location.lat,item.location.lng)
          };
        }else if(item.hasOwnProperty('lat')){
          m = {
            name: (item.hasOwnProperty('name'))?item.name:null,
            lat: item.lat,
            lng: item.lng,
            index: item.id,
            // eslint-disable-next-line no-undef
            pos:new google.maps.LatLng(item.lat,item.lng)
          };
        }else{
          continue;
        }

        this.addMarker(m,infoWindows);
        bounds.extend(m.pos);

      }


      this.map.fitBounds(bounds);

    },

    addMarker(markerConfig){


      let image = {
        url: '/mobile/images/marker-icon.png',
        // eslint-disable-next-line no-undef
        size: new google.maps.Size(25, 41),
        // eslint-disable-next-line no-undef
        origin: new google.maps.Point(0, 0),
        // eslint-disable-next-line no-undef
        anchor: new google.maps.Point(13, 41)
      };

      let options={
        position: markerConfig.pos,
        map: this.map,
        icon:image
      };
// eslint-disable-next-line no-undef
      var marker = new google.maps.Marker(options);
      // eslint-disable-next-line no-undef
      marker.setAnimation(google.maps.Animation.DROP);
      if(markerConfig.hasOwnProperty('name') && markerConfig.name!==null) {
        // eslint-disable-next-line no-undef
        marker.infoWindow = new google.maps.InfoWindow({
          content: "<strong>"+markerConfig.name+"</strong>"
        });
        let self = this;
        // eslint-disable-next-line no-undef
        google.maps.event.addListener(marker, 'click', function () {
          this.infoWindow.open(self.map, this);
        });
      }



      this.markers.push(marker);


    },


    zoomUpdated (zoom) {
      this.zoom = zoom;
    },
    centerUpdated (center) {
      this.center = center;
    },
    boundsUpdated (bounds) {
      this.bounds = bounds;
    }
  }

}
</script>