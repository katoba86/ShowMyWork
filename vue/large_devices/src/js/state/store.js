import Vue from 'vue';
import Vuex from 'vuex';
import createLogger from 'vuex/dist/logger';
import dtc from './modules/dtc.store';
import common from './modules/common.store';
import hst from './modules/hst.store';
Vue.use(Vuex);
const debug = process.env.NODE_ENV !== 'production';



export default new Vuex.Store({
    modules: {
        dtc,
        common,
        hst
    },
    strict: debug,
    plugins: debug? [ createLogger() ] : [],
});