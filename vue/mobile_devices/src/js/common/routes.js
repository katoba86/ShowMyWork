

import Home from '../main/pages/Home.vue';

const Hst = () => import('../main/pages/Hst.vue');

const AsStart = () => import('../main/pages/AsStart.vue');
const Select = () => import('../main/pages/Select.vue');
const Passenger = () => import('../main/pages/Passenger.vue');
const Redirect = () => import('../main/pages/Redirect.vue');
const Linie = () => import('../main/pages/Linie.vue');
const Map = () => import('../main/pages/Map.vue');
const Result = () => import('../main/pages/Result.vue');
const Details = () => import('../main/pages/Details.vue');

export const routes = [
    {
        path: '/redir/:url',
        name: 'redirect',
        component: Redirect,
        props: (route) => ({url: route.params.url,fetch:route.query.m})
    },
    {
        name:'Details',
        path: '/details/:jid', component: Details, props: (route) => ({ jid: route.params.jid })
    },
    {path: '/map/:id', name: 'map', component: Map, props: (route) => ({id: route.params.id})},
    {path: '/asstart/:id', name: 'asstart', component: AsStart, props: (route) => ({id: route.params.id})},
    {
        path: '/linie/:id',
        name: 'linie',
        component: Linie,
        props: (route) => ({id: route.params.id})
    },
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/hst',
        name: 'hst',
        component: Hst
    },
    {
        path: '/result',
        name: 'result',
        component: Result
    },
    {
        path: '/passenger',
        name: 'passenger',
        component: Passenger
    },
    {
        path: '/select',
        name: 'select',
        component: Select,
        props: (route) => ({type: route.query.type,ns:route.query.ns})
    },
];