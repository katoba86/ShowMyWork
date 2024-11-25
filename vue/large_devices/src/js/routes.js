import Home from '@/js/components/pages/home.vue';
import Hst from '@/js/components/pages/hst.vue';

export const routes = [
    {
        path: '/',
        name: 'home',
        components: {
            default: Home
        }
    },

    {
        path: '/hst',
        name: 'hst',
        components: {
            default: Hst
        }
    },


    {
        path: '*',
        components: {
            default: Home
        }
    }


];
