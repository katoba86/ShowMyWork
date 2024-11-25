import {
    SET_HST,
    GET_HST,
    SET_HST_CITY,
    GET_HST_CITY,
    HST_IS_VALID, GET_HST_CITY_OBJECT, GET_ENTIRE_STATE, GET_HST_OBJECT, GET_HST_DESTINATION, SET_HST_DESTINATION
} from "@/js/state/modules/hst.types";
import {getPlaceholder} from "@/js/Text";

const NAMESPACE = 'hst';




const state = {
    origin:null,
    hst:null,
    destination:null
};

const getters = {


    [HST_IS_VALID](state){
        let temp = {
            origin:true,
            hst:true,
        };
        if(typeof state.origin !== 'object' || state.origin===null || !state.origin.hasOwnProperty('name')){
            temp.origin =false;
        }
        if(typeof state.hst !== 'object' ||state.hst===null ||  !state.hst.hasOwnProperty('name')){
            temp.destination = false;
        }
        return temp;
    },
    [GET_HST_CITY]( state ) {
        return (state.origin === null)?getPlaceholder(NAMESPACE,GET_HST_CITY):state.origin;
    },
    [GET_HST_CITY_OBJECT]( state ) {
        return state.origin;
    },
    [GET_HST]( state ) {
        return (state.hst === null)?getPlaceholder(NAMESPACE,GET_HST):state.hst;
    },
    [GET_HST_OBJECT]( state ) {
        return state.hst;
    },
    [GET_HST_DESTINATION](state){
        return state.destination;
    },
    [GET_ENTIRE_STATE]( state ) {
        return {
            hst:state.hst,
            city:state.origin
        };
    },


}


const actions = {
    [SET_HST_DESTINATION]:({commit},payload) => {
        commit(SET_HST_DESTINATION,payload);
    },
    [SET_HST_CITY]: ({commit},payload) => {
        commit(SET_HST_CITY,payload);
    },
    [SET_HST]: ({commit},payload) => {
        commit(SET_HST,payload);
    },
}

const mutations = {

    [SET_HST_DESTINATION](state,destination){
      state.destination = destination;
    },

    [SET_HST](state, hst) {
        state.hst = hst;
    },
    [SET_HST_CITY](state, origin) {
        state.origin = origin;
    },


}

export default {
    namespaced: true,
    getters,
    state,
    actions,
    mutations
}
