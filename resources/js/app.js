import Vue from 'vue'
import App from './components/App.vue'
import router from './router'
import store from './store'
import VueSweetalert2 from 'vue-sweetalert2';
import { VueMaskDirective } from 'v-mask'
import { VueMaskFilter } from 'v-mask'
import i18n from './i18n'


Vue.use(VueSweetalert2);
Vue.directive('mask', VueMaskDirective);
Vue.filter('VMask', VueMaskFilter)

window.axios = require('axios');

window.addEventListener('load', function () {

    const app = new Vue({
        router,
        store,
        i18n,
        render: h => h(App),
        'el': '#app'
    })

})


