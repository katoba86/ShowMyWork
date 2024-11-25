import '../../scss/app.scss';


import { createApp } from 'vue'
import { createRouter,createWebHashHistory } from 'vue-router';
import mdiVue from 'mdi-vue/v3'
import * as mdijs from '@mdi/js'

import App from './App.vue'
import {routes} from "../common/routes";
import store from "../common/state/store";

//import {createRippleDirective} from 'vue-create-ripple';


import emitter from "../main/emmiter";

const router = createRouter({
   history: createWebHashHistory(),
   routes,
});
/*const GlobalRipple = createRippleDirective({
   class: 'bg-dark opacity-50'
});*/
router.beforeEach((to, from, next) => {
   store.dispatch('common/SET_PAGE_LOADING',true);
   next()
})
router.afterEach(() => {
   store.dispatch('common/SET_PAGE_LOADING',false);
})


const app = createApp(App);
app.config.devtools = true;
app.performance = true;
app.devTools = true;
app.config.performance = true;
app.use(router);
app.use(store);
app.use(mdiVue, {
   icons: mdijs
});
//app.directive('ripple', GlobalRipple);
app.config.globalProperties.emitter = emitter;
app.config.globalProperties.topFormData = (typeof window.topFormData!=='undefined')?window.topFormData:{};
app.mount('#app');


const elements = document.getElementsByClassName('action');
Array.from(elements).forEach((t) =>{
   t.addEventListener('click', (e)=>{
      e.preventDefault();

      emitter.emit(t.getAttribute("data-trigger"),{
         id:t.getAttribute("data-id")
      });

      return false;
   })});
const elem = document.querySelector('#haltestellen .btn');
if(elem) {
   elem.addEventListener('click', (e) => {
      e.preventDefault();
      emitter.emit("searchHst", {
         id: elem.getAttribute("data-id")
      });
      return false;
   });
}

const addClass = document.querySelectorAll('a[data-class]');

Array.from(addClass).forEach((t) =>{
   t.addEventListener('click', ()=>{
      //e.preventDefault();
      document.getElementById(t.getAttribute('data-target')).classList.add(t.getAttribute('data-class'));
   })
});


function loadAds(){
   l=true;
   let elements = document.getElementsByClassName('ads_defer_by_google');
   console.log(elements);
   Array.from(elements).forEach((t) =>{
      t.classList.remove('ads_defer_by_google');
      t.classList.add('adsbygoogle');
      // eslint-disable-next-line no-undef
      (adsbygoogle = window.adsbygoogle || []).push({});

   });
}
let l = false;
if(window.md === 0) {

   setTimeout(()=>{
      if(!l) {
         loadAds();
      }
   },2500);

   /*document.addEventListener('scroll', () => {
       if(!this.l) {
           this.loadAds();
       }
   }, {
       once: true,
       passive: true
   }); */
}




if ('serviceWorker' in navigator) {
   // Use the window load event to keep the page load performant
   window.addEventListener('load', () => {
      navigator.serviceWorker.register('/mobile/service-worker.js');
   });
}


/*

window.WebFontConfig = {
      google: {
         families: ['Material Icons']
      }
   };

      (function(d) {
      var wf = d.createElement('script'), s = d.scripts[0];
      wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
      wf.async = true;
      s.parentNode.insertBefore(wf, s);
   })(document);
*/
