import {
    CLEAR_SUGGESTIONS, GET_HST_BY_ID,
    GET_RDR,
    GET_SUGGESTIONS,
    IS_BLOCKED,
    IS_LOADING,
    PAGE_LOADING,
    RETRIEVE_SUGGESTIONS_HST,
    RETRIEVE_SUGGESTIONS_MIXED,
    SET_FOLDED_OUT,
    SET_BLOCK,
    SET_IS_LOADING, SET_PAGE_LOADING,
    SET_RDR,
    SET_SUGGESTIONS, IS_FOLDED_OUT
} from "./common.types";
import {hstById, searchHst, searchMixed} from "../../api/remote";
import {HAS_ERROR, SET_ERROR} from "@/js/common/state/modules/common.types";


const state  = {
    isLoading:false,
    isFoldedOut:false,
    suggestions:[],
    block:false,
    rdrAlt:false,
    error:false,
    pageLoading:false,
}

const getters = {
    [IS_LOADING]( state ) {
        return state.isLoading;
    },
    [IS_FOLDED_OUT]( state ) {
        return state.isFoldedOut;
    },
    [HAS_ERROR]( state ) {
        return state.error;
    },
    [PAGE_LOADING]( state ) {
        return state.pageLoading;
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
    [SET_FOLDED_OUT]: ({commit},payload) => {
        commit(SET_FOLDED_OUT,payload);
    },
    [SET_ERROR]: ({commit},payload) => {
        commit(SET_ERROR,payload);
    },
    [SET_PAGE_LOADING]: ({commit},payload) => {
        commit(SET_PAGE_LOADING,payload);
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
    },


    async [GET_HST_BY_ID]({commit},id){

        return new Promise( (resolve,reject)=>{

            try {
                commit(SET_IS_LOADING,true);
                hstById(id).then((data)=>{
                    commit(SET_IS_LOADING,false);
                    resolve(data);
                });
            }catch (e) {
                reject(e);
            }
        })





    }

}

const mutations = {
    [CLEAR_SUGGESTIONS](state){
      state.suggestions = [];
    },
    [SET_IS_LOADING](state, loading) {
        state.isLoading = (loading === true);
    },
    [SET_FOLDED_OUT](state, folded) {
        state.isFoldedOut = (folded === true);
    },
    [SET_ERROR](state, error) {
        if(error === true){
            if(document.getElementById('globalError')){
                document.getElementById('globalError').classList.remove('d-none');
            }
        }
        state.error = (error === true);
    },
    [SET_PAGE_LOADING](state, loading) {
        state.pageLoading = (loading === true);
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
