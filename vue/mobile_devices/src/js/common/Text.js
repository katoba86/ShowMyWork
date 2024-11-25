import {GET_DESTINATION, GET_ORIGIN} from "./state/modules/dtc.types";
import {GET_HST, GET_HST_CITY} from "./state/modules/hst.types";


export const FIELD_DTC_Passenger = {
    'head':'Wer reist mit?',
};
export const FIELD_DTC_Date = {
    head:'Datum / Uhrzeit',
default: 'jetzt'
};

export const FIELD_HST_ORIGIN = {
    head:'In welcher Stadt?',
        placeholder:'z.B. Berlin',
        type:'city',
        error:'Ort der Haltestelle fehlt',
        modal:{
        head:'Stadt Ihrer Haltestelle?',
            results: 'Folgende Städte  wurden gefunden',
            noResult: 'Leider nichts gefunden',
            placeholder:'Stadt / Ort '
    }
};

export const FIELD_HST_DESTINATION = {
    head:'Haltestelle',
        placeholder:'z.B. München Frühlingsplatz',
        type:'hst',
        error:'Welche Haltestelle?',
        modal:{
        head:'Name der Haltestelle?',
            results: 'Folgende Haltestellen  wurden gefunden',
            noResult: 'Leider nichts gefunden',
            placeholder:'Name (z.B. Marktplatz o.ä.) '
    }
};

export const FIELD_DTC_DESTINATION = {
    head:'Wohin',
        placeholder:'z.B. München',
        type:'city',
        error:'Ankunft bitte ausfüllen',
        modal:{
        head:'Wohin möchten Sie reisen?',
            results: 'Folgende Städte / Bahnhöfe wurden gefunden',
            noResult: 'Leider nichts gefunden',
            placeholder:'Stadt / Ort / Bahnhof'
    }
};

export const FIELD_DTC_ORIGIN = {
    head: 'Von Wo',
    placeholder: 'z.B. Berlin',
    type:'city',
    error:'Abfahrt bitte ausfüllen',
    modal:{
        head:'Von wo aus startet Ihre Reise?',
        results: 'Folgende Städte / Bahnhöfe wurden gefunden',
        noResult: 'Leider nichts gefunden',
        placeholder:'Stadt / Ort / Bahnhof'
    }
};

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
        [GET_HST_CITY]: 'z.B. Berlin',
        [GET_HST]: 'z.B. Marktplatz oder Bahnhofstr.',
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
