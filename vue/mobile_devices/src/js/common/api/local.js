import httpClient from './client';

const HOST = process.env.MIX_API_FRONT;

// you can pass arguments to use as request parameters/data
const getDtcCode = async function(size,mod,fallback=false){
    const data = await httpClient.get(`${HOST}/rest/mon`,{
        params: {
            mon: mod,
            notify: true,
            size: size,
            fallback: (fallback) ? 1 : 0
        }
    });
    if(data.status===200 && data.hasOwnProperty('data')){
        return data.data;
    }
    return '';
};

const getInterchange = (id) => httpClient.get(`${HOST}/busabfahrt`,{
    params:{
        id:id
    }
});



const getHstLink = async function({hst,city}){
    const data = await httpClient.get(`${HOST}/rest/hst/link`,{
        params:{
            hst:hst.id,
            city:city.id
        }
    });
    if(data.status===200){
        return data.data;
    }
    return '';
}



export {
    getInterchange,
    getDtcCode,
    getHstLink
}
