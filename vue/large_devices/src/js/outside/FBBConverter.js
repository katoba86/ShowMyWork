


export class FBBConverter{


    static getFare(fare,useTarif = true){
        if (fare===null || fare.euro === null || fare.euro === 0) {

            if(useTarif && fare.hasOwnProperty('tarif') && null!==fare.tarif){

                if(fare.tarif.includes('Preisauskunft nicht')){
                    return '';
                }
                return fare.tarif;
            }
            return 'n/a';
        }

        let cent = (fare.cent%100);
        if(cent < 10){
            cent = "0"+cent;
        }

        return Math.floor(fare.cent/100) + ','+cent+' €';
    }

    static getDirectionString(){
        if (typeof window.input==='undefined' || (typeof window.input !== 'undefined' && window.input.hasOwnProperty('way') && window.input.way === true)){
            return "Nur Hinreise";
        }else{
            return "Hin & Zurück";
        }
    }


    static getTravelers(travelers){



      let list = travelers.split(",");


      let childs = 0;
      let adults = 0;
      let babys = 0;


      let s = "";
      for(let i=0;i<list.length;i++){
          s = list[i].split("|");

          if(s[0]==="adult"){
              adults = parseInt(s[1]);
          }else if(s[0]==="child"){
              childs = parseInt(s[1]);
          }else{
              babys = parseInt(s[1]);
          }
      }
      let l = [];

      if(adults>0){
          if(adults > 1){
              l.push(adults+" Erwachsene");
          }else{
              l.push("Ein Erwachsener");
          }
      }


        if(childs>0){
            if(childs > 1){
                l.push(childs+" Kinder");
            }else{
                l.push("Ein Kind");
            }
        }

        if(babys>0){
            if(babys > 1){
                l.push(babys+" Kleinkinder");
            }else{
                l.push("Ein Kleinkind");
            }
        }

        return l.join(", ");
    }
    static getFlixLink(origin, destination,date) {

          let params = {
            'route': origin.name + '-' + destination.name,
            'rideDate': date,
            'backRideDate': date,
            'adult': '1',
              '_locale':'de',
            'is_train_relation':'false',
            'backRide': '0',
            'currency': 'EUR',

            'departureStation': origin.id,
            'arrivalStation': destination.id,

        };

       let url = "https://shop.flixbus.de/search?"+Object.keys(params).map(key => key + '=' + encodeURIComponent(params[key])).join('&');
       // return "https://partners.webmasterplan.com/click.asp?ref=773228&site=2894&type=text&tnb=5&diurl="+encodeURIComponent(url);
        return url;
    }
    static getDBLink(origin,destination,date = null,time = null){

        if(date === null && time === null) {

            let d = new Date();
            d.setTime(d.getTime() + 10 * 60 * 1000); // +10Min.
            time = d.getHours() + ":" + d.getMinutes();

            date = d.getDate() + "." + (d.getMonth() + 1) + "." + d.getFullYear();
        }

        //let url="https://partners.webmasterplan.com/click.asp?ref=715105&site=2894&type=text&tnb=126&prd=yes&";
        let url = "https://reiseauskunft.bahn.de/bin/query.exe/dn?";
        url += "S=" + encodeURIComponent(origin);
        url += "&Z=" + encodeURIComponent(destination);
        url += "&date=" + encodeURIComponent(date);
        url += "&time=" + encodeURIComponent(time);
        url += "&start=1";

        return url;



    }

    /**
     *
     * @param list
     * @param concat
     * @returns {*}
     */
    static concatHuman(list,concat = ' und '){

        if(list.length === 0){return null;}
        if(list.length === 1){return list[0];}


        let length = list.length;
        let t = list.slice(0,length-1);
        if(Array.isArray(t)) {
            let tStr = t.join(", ");

            tStr += concat + list[list.length - 1];
            return tStr;
        }
        return null;
    }

    static knownProductConversion(productName,removeWalk = false){
        const skip = ['Fußweg','Fussweg'];
        if(removeWalk && skip.includes(productName)){
            return null;
        }

        let known = [
            'ICE','IC','RE','S','RB'
        ];
        let replace = {
            'WALK':"Fußweg",
            'FLIX':"Flixbus"
        };
        let unknown = 'Nahverkehr';


        for (const [key, value] of Object.entries(replace)) {
          if(productName === key){
              productName = value;break;
          }
        }

        if(known.includes(productName)){
            return productName;
        }else{
            return productName;
         /*  if(true) { //@todo change it!
                return unknown;
            }else{
                return productName;
            } */
        }



    }


    static getProductName(product,short = false){

        if(product.hasOwnProperty('flixInfos') && product.flixInfos!==null){
            return "Flixbus";
        }
        let name = (typeof product.name !=='undefined')?product.name:null;
        if (typeof product === 'string' || product instanceof String){
            name = product;
        }

        if(name === null){

            return false;
        }

        const regex = /([A-Z]{2,4})\s?(\d+)/gm;
        let res = regex.exec(name);

        if(Array.isArray(res) && typeof res[1]!=='undefined'){
            return this.knownProductConversion(res[1]);
        }


        return this.knownProductConversion(name,short);


    }


    static getProducts(journey){
        let p = [];

        if(journey.trips !== null && !Array.isArray(journey.trips)){
            let _t = journey.trips;
            journey.trips = [_t];
        }

        journey.trips.forEach((trip)=>{
            trip.steps.forEach((step)=>{

              //  let name = step.product;
               p.push(step.product);
            });
        });

        for (let index in p) {
            // noinspection JSUnfilteredForInLoop
            p[index] = this.getProductName(p[index]);
        }
        const unique = new Set(p);
       return this.concatHuman([...unique]);

    }

    static applyType(suggestions,type='city'){
        let ret = [];

        suggestions.forEach((suggest)=>{
            suggest.type = type;
           ret.push(suggest);
        });
        return ret;
    }

    static convertSuggest(suggestions,type='city'){
        let ret = [];

        suggestions.forEach((suggest)=>{
            let o = {
                name:null,
                bundesland:null,
                id:null,
                type:null
            };

            const regex = /(.*?)\s\((.*?)\)/gm;

            let res = regex.exec(suggest.cityname);
            o.name = res[1];
            o.bundesland = res[2];
            o.id = suggest.identifer;
            o.type = type;
            ret.push(o);

        });

        return ret;

    }


}
