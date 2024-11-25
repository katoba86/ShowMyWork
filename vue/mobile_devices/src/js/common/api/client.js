import axios from 'axios';

const httpClient = axios.create({
    //baseURL: process.env.VUE_APP_BASE_URL,
    headers: {
        "Content-Type": "application/json",
        // anything you want to add to the headers
    }
});
httpClient.interceptors.response.use(function(response){

    if(typeof response === 'object' && typeof response.data === 'object' && 'data' in response && 'data' in response.data){
        response.data = response.data.data;
    }
    return response;
});

export default httpClient;