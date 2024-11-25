import {
    CLEAR_SUGGESTIONS, GET_RDR,
    GET_SUGGESTIONS, IS_BLOCKED,
    IS_LOADING, RETRIEVE_SUGGESTIONS_HST, RETRIEVE_SUGGESTIONS_MIXED, RETRIEVE_SUGGESTIONS_STREETS, SET_BLOCK,
    SET_IS_LOADING, SET_RDR,
    SET_SUGGESTIONS
} from "@/js/state/modules/common.types";
import {searchHst, searchMixed} from "@/js/api/remote";


const state  = {
    isLoading:false,
    suggestions:[],
    block:false,
    rdrAlt:false,
}

const getters = {
    [IS_LOADING]( state ) {
        return state.isLoading;
    },
    [GET_SUGGESTIONS]( state ) {
        return state.suggestions;
    },
    [IS_BLOCKED](state){
        return state.block;
    },
    [GET_RDR](state){
        return state.rdrAlt;
    }
}


const actions = {
    [SET_IS_LOADING]: ({commit},payload) => {
       commit(SET_IS_LOADING,payload);
    },
    [CLEAR_SUGGESTIONS]: ({commit}) => {
        commit(CLEAR_SUGGESTIONS);
    },
    [SET_SUGGESTIONS]: ({commit},payload) => {
        commit(SET_SUGGESTIONS,payload);
    },
    [SET_BLOCK]: ({commit},payload) => {
        commit(SET_BLOCK,payload);
    },
    [SET_RDR]: ({commit},payload) => {
        commit(SET_RDR,payload);
    },

    async [RETRIEVE_SUGGESTIONS_MIXED]({commit},value){

        commit(CLEAR_SUGGESTIONS);
        commit(SET_IS_LOADING,true);
        try {
            let data = await searchMixed(value);
            commit(SET_SUGGESTIONS,data.data);
        }catch(e){
            console.error(e);
        }finally {
            commit(SET_IS_LOADING,false);
        }
    },

    async [RETRIEVE_SUGGESTIONS_HST]({commit,rootState},value){

        commit(SET_IS_LOADING,true);

        try {
            let data = await searchHst(value,rootState.hst.origin.id);
            commit(SET_SUGGESTIONS,data.data);
        }catch(e){
            console.error(e);
        }finally {
            commit(SET_IS_LOADING,false);
        }
    }
}

const mutations = {
    [CLEAR_SUGGESTIONS](state){
      state.suggestions = [];
    },
    [SET_IS_LOADING](state, loading) {
        state.isLoading = (loading === true);
    },
    [SET_SUGGESTIONS](state,suggestions){
        state.suggestions = suggestions;
    },
    [SET_BLOCK](state, blk) {
        state.block = (blk===true);
    },
    [SET_RDR](state, rdr) {
        state.rdrAlt = (rdr===true);
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
    SET_IS_LOADING,
    IS_LOADING,
    SET_SUGGESTIONS,
    GET_SUGGESTIONS,
    CLEAR_SUGGESTIONS
}
