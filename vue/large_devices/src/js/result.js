import Vue  from 'vue'
import Main from './result/main.vue'
import Page from "./outside/Outside";

Vue.prototype.$eventBus = new Vue();

new Vue({

    el: '#result',
    render: h => h(Main)
});
(new Page());
