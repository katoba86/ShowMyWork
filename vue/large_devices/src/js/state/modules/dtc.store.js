import {
    GET_ORIGIN,
    SET_ORIGIN,
    SET_DESTINATION,
    GET_DESTINATION,
    IS_VALID,
    GET_PASSENGER,
    GET_PASSENGER_STR,
    SET_PASSENGER,
    GET_DATE,
    SET_DATE,
    GET_LINK,
    GET_PRESELECT
} from "@/js/state/modules/dtc.types";
import {getPlaceholder,passenger} from "@/js/Text";
import {getDistanceBetween} from "geolocation-distance-between";
import {SET_RDR} from "@/js/state/modules/common.types";
import {omio} from "@/js/api/remote";


const NAMESPACE = 'dtc';




const state = {
    origin:null,
    destination:null,
    date:null,
    passenger:{
        child:0,
        baby:0,
        adult:1
    },
    oneway:true,
    u:null,
    endDate:false,
    preSelect:[
        {"id":4184,"name":"Hamburg","urlname":null,"type":"city","station":8002549,"bundesland":{"bundesland_id":6,"name":"Hamburg"},"location":{"lat":53.5505,"lng":9.99207},"distance":null,"near":null},
        {"id":893,"name":"Berlin","urlname":null,"type":"city","station":8011160,"bundesland":{"bundesland_id":3,"name":"Berlin"},"location":{"lat":52.5162,"lng":13.377},"distance":null,"near":null},
        {"id":7306,"name":"M\u00fcnchen","urlname":"muenchen","type":"city","station":8000261,"bundesland":{"bundesland_id":2,"name":"Bayern"},"location":{"lat":48.1372,"lng":11.5753},"distance":null,"near":null},
        {"id":12711,"name":"Frankfurt am Main","urlname":"frankfurt-main","type":"city","station":8000105,"bundesland":{"bundesland_id":7,"name":"Hessen"},"location":{"lat":50.1065,"lng":8.66214},"distance":null,"near":null},
        {"id":1426,"name":"Bremen","urlname":null,"type":"city","station":8000050,"bundesland":{"bundesland_id":5,"name":"Bremen"}},
        {"id":2179,"name":"Dortmund","urlname":null,"type":"city","station":8000080,"bundesland":{"bundesland_id":1,"name":"NRW"},"location":{"lat":51.5139,"lng":7.46465},"distance":null,"near":null},
        {"id":5746,"name":"K\u00f6ln","urlname":"koeln","type":"city","station":8000207,"bundesland":{"bundesland_id":1,"name":"NRW"},"location":{"lat":50.9413,"lng":6.95814},"distance":null,"near":null},
        {"id":2232,"name":"Dresden","urlname":null,"type":"city","station":8010085,"bundesland":{"bundesland_id":12,"name":"Sachsen"},"location":{"lat":51.0518,"lng":13.7411},"distance":null,"near":null}

    ]
};

const getters = {

    [GET_PRESELECT](state){
        return state.preSelect;
    },

    [GET_DATE](state){
        return (state.date===null)?'jetzt':state.date;
    },
      [GET_LINK](state){

        let url = '/result/new';


        let oid = ("id" in state.origin)?state.origin.id:state.origin.identifer;
        let did = ("id" in state.destination)?state.destination.id:state.destination.identifer;


        if(oid === undefined || did === undefined){
            return "#";
        }

        let params = {
            travelers: Object.keys(state.passenger).map(key => key + '|' + state.passenger[key]).join(','),
            origin: state.origin.type.substr(0, 1).toUpperCase() + "" + oid,
            destination: state.destination.type.substr(0, 1).toUpperCase() + "" + did,
            way: (state.oneway) ? 1 : 0,
            start: state.date,
            endDate: (state.oneway) ? null : state.endDate
        };

        url = url + '?' + Object.keys(params).map(key => key + '=' + params[key]).join('&');
        return url;
    },

    [IS_VALID](state){
        let temp = {
            origin:true,
            destination:true,
            passenger:true
        };

        if(typeof state.origin !== 'object' || state.origin===null || !state.origin.hasOwnProperty('name')){
            temp.origin =false;
        }
        if(typeof state.destination !== 'object' ||state.destination===null ||  !state.destination.hasOwnProperty('name')){
            temp.destination = false;
        }
        if(state.passenger.adult+state.passenger.child+state.passenger.baby === 0){
            temp.passenger = false;
        }
        return temp;
    },
    [GET_ORIGIN]( state ) {
        return (state.origin === null)?getPlaceholder(NAMESPACE,GET_ORIGIN):state.origin;
    },
    [GET_DESTINATION]( state ) {
        return (state.destination === null)?getPlaceholder(NAMESPACE,GET_DESTINATION):state.destination;
    },
    [GET_PASSENGER](state){
        return state.passenger;
    },
    [GET_PASSENGER_STR](state){
        let g=[];
        if(state.passenger.adult>0){
            if(state.passenger.adult > 1){
                g.push(state.passenger.adult+" "+passenger.p.adult);
            }else{
                g.push(state.passenger.adult+" "+passenger.s.adult);
            }

        }
        if(state.passenger.child>0){
            if(state.passenger.child > 1){
                g.push(state.passenger.child+" "+passenger.p.child);
            }else{
                g.push(state.passenger.child+" "+passenger.s.child);
            }
        }
        if(state.passenger.baby>0){
            if(state.passenger.baby > 1){
                g.push(state.passenger.baby+" "+passenger.p.baby);
            }else{
                g.push(state.passenger.baby+" "+passenger.s.baby);
            }
        }
        if(g.length===0){return passenger.nobody;}
        return g.join(", ");
    }
}


const actions = {
    [SET_DATE]: ({commit},payload) => {
        commit(SET_DATE,payload);
    },
    [SET_ORIGIN]: ({commit},payload) => {
        commit(SET_ORIGIN,payload);
    },

    [SET_DESTINATION]: ({commit,getters,dispatch},payload) => {
        commit(SET_DESTINATION,payload);

        const valid = getters[IS_VALID];
        if(valid.origin && valid.destination){
            const o = getters[GET_ORIGIN];
            const d = getters[GET_DESTINATION];

            if(o.hasOwnProperty('location') && d.hasOwnProperty('location')) {
                const distanceBetween = getDistanceBetween(
                    {latitude: o.location.lat, longitude: o.location.lng},
                    {latitude: d.location.lat, longitude: d.location.lng}
                );
                if (distanceBetween < 100) {
                    dispatch(["common", SET_RDR].join("/"), true, {root: true});
                }
            }
        }



    },
    [SET_PASSENGER]:({commit},payload) => {
       commit(SET_PASSENGER,payload);
    },

}

const mutations = {

    [SET_PASSENGER](state,passenger){
        let m = (passenger.type)?1:-1;
        let v = state.passenger[passenger.who]+m;
        if(v<0){v=0;}
      state.passenger[passenger.who]=v;
    },
    [SET_ORIGIN](state, origin) {
        state.origin = origin;
    },
    [SET_DESTINATION](state, destination) {
        state.destination = destination;
    },
    [SET_DATE](state, date) {
        state.date = date;
    },

}

export default {
    namespaced: true,
    getters,
    state,
    actions,
    mutations
}
