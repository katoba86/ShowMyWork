export const IS_LOADING = 'IS_LOADING';
export const SET_IS_LOADING = 'SET_IS_LOADING';
export const PAGE_LOADING = 'PAGE_LOADING';
export const SET_PAGE_LOADING = 'SET_PAGE_LOADING';
export const SET_FOLDED_OUT = 'SET_FOLDED_OUT';
export const IS_FOLDED_OUT = 'IS_FOLDED_OUT';

export const HAS_ERROR = 'HAS_ERROR';
export const SET_ERROR = 'SET_ERROR';

export const SET_BLOCK = 'SET_BLOCK';
export const IS_BLOCKED = 'IS_BLOCKED';

export const SET_RDR = 'SET_RDR';
export const GET_RDR = 'GET_RDR';

export const SET_SUGGESTIONS = 'SET_SUGGESTIONS';
export const GET_SUGGESTIONS = 'GET_SUGGESTIONS';
export const CLEAR_SUGGESTIONS = 'CLEAR_SUGGESTIONS';


export const RETRIEVE_SUGGESTIONS_MIXED = 'RETRIEVE_SUGGESTIONS_MIXED';
export const RETRIEVE_SUGGESTIONS_CITY = 'RETRIEVE_SUGGESTIONS_CITY';
export const RETRIEVE_SUGGESTIONS_HST = 'RETRIEVE_SUGGESTIONS_HST';
export const RETRIEVE_SUGGESTIONS_STREETS= 'RETRIEVE_SUGGESTIONS_STREETS';


export const GET_HST_BY_ID = 'GET_HST_BY_ID';


export const loadTop = (input) => {
    if(typeof window.topFormData==='undefined'){
        return null;
    }
    if(typeof window.topFormData[input] !== 'undefined'){
        return window.topFormData[input];
    }
    return null;
};
