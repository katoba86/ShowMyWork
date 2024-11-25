

import dtc from './modules/dtc.store';
import common from './modules/common.store';
import hst from './modules/hst.store';
import {createStore,createLogger} from 'vuex';
const debug = process.env.NODE_ENV !== 'production';



export default createStore({
    modules: {
        dtc,
        common,
        hst
    },
    strict: debug,
    plugins: debug? [ createLogger() ] : [],
});