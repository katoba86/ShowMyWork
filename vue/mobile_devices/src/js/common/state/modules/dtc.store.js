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
    GET_RESULTS_FLIX,
    GET_RESULTS_DB,
    SET_RESULTS, GET_RESULTS, GET_RESULT_SORT, GET_DATE_OBJ
} from "./dtc.types";
import {getPlaceholder,passenger} from "../../Text";
import {SET_ERROR, SET_IS_LOADING} from "./common.types";
import {calc} from "@/js/common/api/remote";
import {SET_RESULT_SORT, SORT_JOURNEYS} from "@/js/common/state/modules/dtc.types";
import {loadTop} from "@/js/common/state/modules/common.types";
import Utils from "@/js/common/Utils";

const NAMESPACE = 'dtc';



const convertLocal = (date) => {

    let a = Date.parse(date);
    let x = new Date();
    x.setTime(a);
    const d = x.toLocaleDateString("de-DE",{year:'2-digit',month:'2-digit',day:'numeric'});
    const time = x.toLocaleTimeString().split(":").slice(0,2).join(":");
    //const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return d+" "+time;

};

const state = {
    origin:(loadTop('from')!==null)?loadTop('from'):null,
    destination:(loadTop('to')!==null)?loadTop('to'):null,
    date:null,
    passenger:{
        adult:1,
        child:0,
        baby:0

    },
    sortFunc:null,
    results:{prev:null,next:null,journeys:[]},
    oneway:true,
    endDate:false
};

const getters = {

    [GET_RESULTS](state){
        return state.results;
    },
    [GET_DATE](state){
        return (state.date===null)?'jetzt':convertLocal(state.date);
    },
    [GET_DATE_OBJ](state){

        return (state.date===null)?new Date():state.date;
    },

    [GET_LINK](state){

        let url = '#/result';

        let params = {
            travelers: Object.keys(state.passenger).map(key => key + '|' + state.passenger[key]).join(','),
            origin: state.origin.type.substr(0, 1).toUpperCase() + "" + state.origin.id,
            destination: state.destination.type.substr(0, 1).toUpperCase() + "" + state.destination.id,
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
    [SET_RESULT_SORT]:({commit},payload) => {
        commit(SET_RESULT_SORT,payload);
        commit(SORT_JOURNEYS);
    },
    [GET_RESULTS_DB]({commit,dispatch},payload){
        dispatch(["common", SET_IS_LOADING].join("/"), true, {root: true});
        calc(payload,"db").then((d)=>{
            commit(SET_RESULTS,d.data);
            dispatch(["common", SET_IS_LOADING].join("/"), false, {root: true});
        }).catch(()=>{
            dispatch(["common",SET_ERROR].join("/"),true,{root:true});
            dispatch(["common", SET_IS_LOADING].join("/"), false, {root: true});
        });
    },
    [GET_RESULTS_FLIX]({commit,dispatch},payload){
        dispatch(["common", SET_IS_LOADING].join("/"), true, {root: true});
        calc(payload,"flix").then((d)=>{
            commit(SET_RESULTS,d.data);
            dispatch(["common", SET_IS_LOADING].join("/"), false, {root: true});
        }).catch((e)=>{
            console.log(e);
            dispatch(["common",SET_ERROR].join("/"),true,{root:true});
            dispatch(["common", SET_IS_LOADING].join("/"), false, {root: true});
        });
    },

    [SET_DATE]: ({commit},payload) => {
        commit(SET_DATE,payload);
    },
    [SET_ORIGIN]: ({commit},payload) => {
        commit(SET_ORIGIN,payload);
    },
    [SET_RESULTS]:({commit},payload) => {
      commit(SET_RESULTS,payload)
    },
    [GET_RESULT_SORT]:({commit}) => {
        commit(GET_RESULT_SORT);
    },
    [SET_DESTINATION]: ({commit},payload) => {
        commit(SET_DESTINATION,payload);
        /*
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
              }*/



    },

    [SORT_JOURNEYS]:({commit}) => {
        commit(SORT_JOURNEYS);
    },
    [SET_PASSENGER]:({commit},payload) => {
       commit(SET_PASSENGER,payload);
    },

}

const mutations = {
    [SORT_JOURNEYS](state){
        state.results.journeys =  state.results.journeys.sort(state.sortFunc);
    },

    [SET_RESULTS](state,payload){

        if('journeys' in payload) {

            payload.journeys = Utils.checkJourneys(payload.journeys);

        }
        state.results.journeys = [...state.results.journeys, ...payload.journeys];
        if(payload.hasOwnProperty('prev')){
            state.results.prev = payload.prev;
        }
        if(payload.hasOwnProperty('next')){
            state.results.next = payload.next;
        }
    },

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
    [SET_RESULT_SORT](state,sort){
        state.sortFunc = sort;
    }

}

export default {
    namespaced: true,
    getters,
    state,
    actions,
    mutations
}
