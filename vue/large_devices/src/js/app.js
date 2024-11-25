
import Vue  from 'vue'

import App from '@/js/components/app.vue'
import store from '@/js/state/store';
import VueRouter from 'vue-router';
import { routes } from './routes';

import VModal from 'vue-js-modal'
import Outside from "@/js/outside/Outside";
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

if (browserSupportsAllFeatures()) {
        startPage();
    } else {
        // All other browsers loads polyfills and then run `main()`.
        loadScript('https://polyfill.io/v3/polyfill.min.js?features=es6%2Ces2017', startPage);
    }


function browserSupportsAllFeatures(){
    return window.Promise && window.fetch && window.Symbol;
}


function loadScript(src, done) {
    var js = document.createElement('script');
    js.src = src;
    js.onload = function() {
        done();
    };
    js.onerror = function() {
        done(new Error('Failed to load script ' + src));
    };
    document.head.appendChild(js);
}
if(window.bus!=='undefined' && window.bus===true){
    location.href="#/hst";
}
function startPage()
{
    let check = document.getElementById("app");
    if(null === check || false === check){
        return;
    }
    Vue.use(VModal, {dynamic: true, injectModalsContainer: true});
    //Vue.component('vue-instant', VueInstant);

    Vue.use(VueRouter);
    const router = new VueRouter({
        routes
    });



    Vue.prototype.formData = (typeof window.topFormData!=='undefined')?window.topFormData:null;


    Vue.prototype.block = function(isBlocked){
        if(isBlocked) {

            document.getElementById("body").classList.add("blocked");
        }else{
            document.getElementById("body").classList.remove('blocked');
        }
    }

    let v = new Vue({
        router,
        el: '#app',
        store: store,
        render: h => h(App)
    });
    (new Outside(v)).initEvents().check();


        window.addEventListener("load", function(){


            let num = document.getElementsByClassName('adsbygoogle').length;

               for(let i = 0;i<num;i++) {
                       try {
                           (adsbygoogle = window.adsbygoogle || []).push({});
                       } catch (e) {
                           return false;
                       }
               }
        });





}
if(process.env.NODE_ENV === 'production') {
    if ('serviceWorker' in navigator) {
        console.log("Service worker exist");
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/desktop/service-worker.js');
        });
    }
}

