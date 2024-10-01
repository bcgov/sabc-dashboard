window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Vue = require('vue');
const SupportRouter = require('vue-router').default;
Vue.use(SupportRouter);
//const VueNestable = require('vue-nestable').default;
//Vue.use(VueNestable)


// Vue.component('top-nav-layout', require('./components/admin/layouts/TopNavLayout.vue').default);
// Vue.component('left-nav-layout', require('./components/admin/layouts/LeftNavLayout.vue').default);
// Vue.component('main-body-layout', require('./components/admin/layouts/MainBodyLayout.vue').default);
// Vue.component('body-footer-layout', require('./components/admin/layouts/BodyFooterLayout.vue').default);
//
// Vue.component('main-component', require('./components/admin/MainComponent.vue').default);
Vue.component('side-pages', require('./components/admin/SidePages.vue').default);
Vue.component('side-pages-new', require('./components/admin/SidePagesNew.vue').default);
Vue.component('side-pages-edit', require('./components/admin/SidePagesEdit.vue').default);

Vue.component('alerts', require('./components/admin/Alerts.vue').default);
Vue.component('alerts-new', require('./components/admin/AlertsNew.vue').default);
Vue.component('alerts-edit', require('./components/admin/AlertsEdit.vue').default);

const router = new SupportRouter({
    mode: 'history',
    routes: [

    ]
});


const support_app = new Vue({
    el: '#support_app',
    router,
});
