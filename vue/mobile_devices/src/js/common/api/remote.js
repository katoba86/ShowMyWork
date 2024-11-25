import httpClient from './client';


const HOST = process.env.MIX_API_BACK;

// you can pass arguments to use as request parameters/data
const searchMixed = (value) => httpClient.get(`${HOST}/search/mixed/${value}`);
const searchHst = (value,cid) => httpClient.get(`${HOST}/search/hst/${value}`,{params: {
        rel: cid
    }});

const searchHstNear = (id,value = null,rel = null) => httpClient.get(`${HOST}/hst/${id}/nearhst`,{params: {
        rel: rel,
        value:value,
        upTo:20,
        radius:10
    }});


const searchCitiesNear = (id) => httpClient.get(`${HOST}/city/near/${id}`);

const hstById = (id) => httpClient.get(`${HOST}/hst/${id}`);



const convertTravelers = (passengers) => {
 return Object.keys(passengers).map(key => key + '|' + passengers[key]).join(',')
};

const calc = async function(input,what,hash = null){
    if(process.env.MIX_IS_MOCK==='true'){


        return httpClient.get('http://test.fahrplan-bus-bahn.de:81/mocks/result1.json');
    }

    let date = input.ts;
    let params = {
        date:date,
        travelers:convertTravelers(input.travelers),
        return:input.end,
        way:input.way
    };
    let url = `${HOST}/calc/${what}/${input.origin.type.substring(0,1).toUpperCase()}${input.origin.id}/${input.destination.type.substring(0,1).toUpperCase()}${input.destination.id}`;
    if(hash!==null){
        params.tchange = hash;
    }
    return httpClient.get(url,{
        params:params
    });
}

export {
    searchCitiesNear,
    calc,
    hstById,
    searchHstNear,

    searchMixed,
    searchHst,

}
