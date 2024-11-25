

const loadGoogleMapsApi = require('load-google-maps-api');


class Linie{



    register(path){

            let g = document.createElement('link');
            g.rel = 'stylesheet';
            g.href = path;
            g.media = "screen";
            g.type = 'text/css';
            let gd = document.getElementsByTagName('link')[0];
            gd.parentNode.insertBefore(g, gd);



    }

    constructor() {

        this.map = null;
       // this.tileLayerPath = "http://159.69.27.34:8080/styles/klokantech-basic/{z}/{x}/{y}.png";

        this.steps = window.steps;
        this.markers = [];
        this.initUI();




    }

        async zoomTo(targetId){

           if(null === this.map){
              await this.prepare();
           }


            for(let i=0;i<this.markers.length;i++){

                if(this.markers[i].id===targetId){


                    this.map.panTo(this.markers[i].position);
                    this.map.setZoom(18);


                    return true;

                }

            }


        }


    async addMarker(pos,text,image,id){
        return new Promise((resolve)=>{
            if(typeof image==="undefined" || null===image) {
                image = {
                    url: '/desktop/images/hst.png',
                    // This marker is 20 pixels wide by 32 pixels tall.
                    size: new google.maps.Size(32, 37),
                    // The origin for this image is 0,0.
                    origin: new google.maps.Point(0, 0),
                    // The anchor for this image is the base of the flagpole at 0,32.
                    anchor: new google.maps.Point(16, 34)
                };
            }


            if(Array.isArray(pos)){
                pos = new google.maps.LatLng(pos[0],pos[1]);
            }
            var options={
                position: pos,
                map: this.map,
                icon:image
            };
            if(typeof text!=="undefined" && text!=null){
                options.title=text;
            }
            if(typeof id!=="undefined" && id!=null){
                options.id=id;
            }

            var marker = new google.maps.Marker(options);
            this.markers.push(marker);
            resolve(true);
        });




    }

    showTrack(){



        let bounds = new google.maps.LatLngBounds();
        if(typeof steps === 'undefined'){return false;}
        for(let i=0;i<steps.length;i++){

            let pos = new google.maps.LatLng(steps[i]["lat"], steps[i]["lng"]);
            this.addMarker(pos, steps[i]["name"], null, steps[i]["id"]);
            bounds.extend(pos);
        }
        this.map.fitBounds(bounds);

        let path = new google.maps.Polyline({
            path: steps,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        path.setMap(this.map);
    }

    async initMap(){

        return new Promise(async (resolve)=>{
            let mapOptions = {
                scrollwheel: false,
                zoom: 14,
                center:new google.maps.LatLng(this.steps[0]["lat"], this.steps[0]["lng"]),
                controlSize: 24,
            };

            this.map = new google.maps.Map(document.getElementById("map"),
                mapOptions);

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
            let styledMap = new google.maps.StyledMapType(styles,
                {name: "Styled Map"});
            this.map.mapTypes.set('map_style', styledMap);
            this.map.setMapTypeId('map_style');


            await this.showTrack();
            resolve(true);
        });

    }


    async prepare(){
        return new Promise((resolve,reject)=>{
            loadGoogleMapsApi({
                key:'AIzaSyCXsoTPEw4OrhMBevARWN76Fz1-4WipVEU',
                sensor:'false'
            }).then( async (googleMaps)=> {
                await this.initMap(googleMaps);
                resolve(true);
            }).catch(function (error) {

                reject(false);
            });
        });
    }

    initUI(){


        let loader = document.getElementById('loaderBtn');
        loader.addEventListener('click',async (e)=>{
           e.preventDefault();
            await this.prepare();
        });



        let items = document.querySelectorAll('.action');
        items.forEach(
            (elem)=> {
                elem.addEventListener('click', (e)=> {

                    e.preventDefault();
                    let t = e.currentTarget;
                    let id = t.getAttribute('data-id');
                    let action = t.getAttribute('data-trigger');

                    let event = new CustomEvent(action, {
                        detail: {
                            id:id
                        }
                    });
                    window.document.dispatchEvent(event);

                    return false;

                }, true);
            }
        );

        let zooms = document.querySelectorAll('.mapZoom');

        zooms.forEach(
            (elem)=> {
                elem.addEventListener('click', (e)=> {

                    e.preventDefault();
                    let t = e.currentTarget;
                    let id = t.getAttribute('data-id');

                    this.zoomTo(id);


                    return false;

                }, true);
            }
        );




    }





}
new Linie();
