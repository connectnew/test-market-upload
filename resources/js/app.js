require('./bootstrap');

import Vue from 'vue';
import VueProgressBar from 'vue-progressbar';
import store from '@stor/app';
import Core from '@comp/Core';

Vue.use(VueProgressBar, {
    color: '#F85A40',
    failedColor: 'red',
    height: '3px',
    autoFinish: false,
});

Vue.component('Import', require('@comp/Import.vue').default);

const appVue = new Vue({ // eslint-disable-line no-unused-vars
    el: '#app-vue',
    store,
    components: {
        Core
    },
});

window.appVue = appVue;
