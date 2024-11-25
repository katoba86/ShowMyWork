
export default class Utils{


    static hideFooter(){
        let f = document.getElementById('footerMobile');
        if(f){
            f.classList.add('d-none');
        }
    }
    static showFooter(){
        let f = document.getElementById('footerMobile');
        if(f){
            f.classList.remove('d-none');
        }

    }


    static getIcon(product){
        if('flixInfos' in product && product.flixInfos!==null){
            return "directions_bus";
        }
        if(product.catOut === "Bus" || product.catOutS==="Bus" || product.catOutL==="Bus"){
            return "directions_bus";
        }
        if(product.cls===99999){
            return "directions_walk";
        }
        if(product.cls<5) {
            return "directions_train";
        }
        return "directions_railway";

    }

    static ensureId(obj){
        if(obj!==null && 'identifer' in obj && !('id' in obj)){
            obj.id = obj.identifer;
        }
        return obj;
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
    static removeSecondsFromDate(dStr){
        return dStr.substring(0, dStr.indexOf(':', dStr.indexOf(':')+1));
    }

    static getDate(ts,type){



        let options = {
            timeZone:"Europe/Berlin",
            hour12 : true,
            hour:  "2-digit",
            minute: "2-digit",
            second: "2-digit"
        };



        return (type==='time')?this.removeSecondsFromDate(ts.toLocaleTimeString(options)):ts.toLocaleDateString('de-DE');
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

    static knownProductConversion(productName){

        let known = [
            'ICE','IC','RE','S','RB'
        ];
        let replace = {
            'WALK':"Fußweg",
            'FLIX':"Flixbus"
        };
        // let unknown = 'Nahverkehr';


        for (const [key, value] of Object.entries(replace)) {
            if(productName === key){
                productName = value;break;
            }
        }

        if(known.includes(productName)){
            return productName;
        }else{

            return productName;
        }



    }


    static getProductName(product){

        if('flixInfos' in product && product.flixInfos!==null){
            return "Flixbus";
        }
        let name = (typeof product.name !=='undefined')?product.name:null;
        if (typeof product === 'string' || product instanceof String){
            name = product;
        }

        if(name === null){return false;}

        const regex = /([A-Z]{2,4})\s?(\d+)/gm;
        let res = regex.exec(name);

        if(Array.isArray(res) && typeof res[1]!=='undefined'){
            return this.knownProductConversion(res[1]);
        }


        return this.knownProductConversion(name);


    }




    static getInterchange(interchange,direct="Direktverbindung"){
        if(parseInt(interchange)===0){
            return direct;
        }
        return interchange+'x Umsteigen';

    }
    static getExactFare(fare,prepend="",append="",noFare=" - "){
        if (fare===null || fare.euro === null || fare.euro === 0) {
            return noFare;
        }
        let s = new Intl.NumberFormat('de-DE', { style: 'decimal',minimumFractionDigits:2,maximumFractionDigits:2}).format(fare.cent/100);

        return prepend+s+append;
    }

    static getFare(fare,long = false){
        if (fare===null || fare.euro === null || fare.euro === 0) {
            return 'n/a';
        }

        return (!long)?fare.euro + '':fare.euro+' EUR';
    }

    static getDuration(split){
        if(typeof split === 'object' && 'humanSplit' in split){
            split = split.humanSplit;
        }

        if(split.hours === 0){
            return `${split.minutes} min.`;
        }
        return (split.hours === 0) ? `${split.minutes} min.` : `${split.hours}h ${split.minutes}min.`;
    }


    static buildLink(origin,destination,passengers,startDate){


        if('id' in origin){
            origin.identifer = origin.id;
        }
        if('id' in destination){
            destination.identifer = destination.id;
        }


        let url = "/result/new";
        let params = {
            travelers:Object.keys(passengers).map(key => key + '|' + passengers[key]).join(','),
            origin:origin.type.substr(0,1).toUpperCase()+""+origin.identifer,
            destination:destination.type.substr(0,1).toUpperCase()+""+destination.identifer,
            way:1,
            start:startDate,
            endDate: null
        };

        return url+'?'+Object.keys(params).map(key => key + '=' + params[key]).join('&');
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

        return "https://shop.flixbus.de/search?"+Object.keys(params).map(key => key + '=' + encodeURIComponent(params[key])).join('&');
    }
    static getDBLink(origin,destination,date = null,time = null){




        if(date === null && time === null) {

            let d = new Date();
            //    let lng = 126;
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



    static getTransport(journey){
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


        let k = [];
        for (let index in p) {
            k[index] = this.getProductName(p[index]);
        }
        k = k.reduce((x, y) => x.includes(y) ? x : [...x, y], []);
        return this.concatHuman(k);

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


    static checkJourneys(journeys) {


        journeys.forEach((j,i)=>{
            if(!("origin" in j)){

                if(!Array.isArray(j.trips)){
                    j.trips = [j.trips];
                }

                journeys[i].origin = j.trips[0].origin;
                journeys[i].destination = j.trips[0].destination;
            }
        });


        return journeys;
    }
}
