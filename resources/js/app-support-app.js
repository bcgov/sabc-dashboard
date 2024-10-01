window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

//window.Vue = require('vue');
import Vue from 'vue/dist/vue'
import AdminRouter from 'vue-router/dist/vue-router';
Vue.use(AdminRouter);

Vue.component('app-support-applications', require('./components/app_support/ApplicationsPage.vue').default);
Vue.component('app-support-application-detail', require('./components/app_support/ApplicationDetailPage.vue').default);

Vue.component('app-support-appendix-list', require('./components/app_support/AppendixListPage.vue').default);
Vue.component('app-support-appendix-detail', require('./components/app_support/AppendixDetailPage.vue').default);


const router = new AdminRouter({
    mode: 'history',
    routes: [

    ]
});

if (process.env.MIX_APP_ENV === 'production') {
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true;
}
const admin_app = new Vue({
    el: '#app_support_app',
    router,
});
