import {GET_DESTINATION, GET_ORIGIN} from "@/js/state/modules/dtc.types";
import {GET_HST, GET_HST_CITY} from "@/js/state/modules/hst.types";


export const passenger = {
    s:{
        adult:'Erwachsener',
            child:'Kind',
            baby:'Kleinkind'
    },
    p:{
        adult:'Erwachsene',
            child:'Kinder',
            baby:'Kleinkinder'
    },
    nobody:'niemand :('
}


const Placeholders = {
    dtc: {
        [GET_ORIGIN]: 'Abfahrtsort z.B. Berlin',
        [GET_DESTINATION]: 'Ankunftsort z.B. Hamburg',
    },
    hst: {
        [GET_HST_CITY]: 'Stadt Ihrer Haltestelle, z.B. Hamburg',
        [GET_HST]: 'Haltestelle, z.B. Marktplatz',
    }

}

const ModalHeadlines = {
    dtc:{
        [GET_ORIGIN]:'Von wo aus starten Sie Ihre Reise?',
        [GET_DESTINATION]:'Wohin möchten Sie reisen?',
    },
    hst:{
        [GET_HST_CITY]:'In welcher Stadt suchen Sie?',
        [GET_HST]:'Wählen SIe Ihre Haltestelle',
    }
};


export function getModalHeadline(namespace,str){
    return ModalHeadlines[namespace][str];
}

export function getPlaceholder(namespace,str){

    return Placeholders[namespace][str];
}
