window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


import Vue from 'vue/dist/vue'
import AdminRouter from 'vue-router/dist/vue-router';
Vue.use(AdminRouter);

Vue.component('admin-users', require('./components/admin/Users.vue').default);
Vue.component('admin-users-new', require('./components/admin/UsersNew.vue').default);
Vue.component('admin-users-edit', require('./components/admin/UsersEdit.vue').default);

Vue.component('side-pages', require('./components/admin/SidePages.vue').default);
Vue.component('side-pages-new', require('./components/admin/SidePagesNew.vue').default);
Vue.component('side-pages-edit', require('./components/admin/SidePagesEdit.vue').default);

Vue.component('alerts', require('./components/admin/Alerts.vue').default);
Vue.component('alerts-new', require('./components/admin/AlertsNew.vue').default);
Vue.component('alerts-edit', require('./components/admin/AlertsEdit.vue').default);

Vue.component('declarations', require('./components/admin/Declarations.vue').default);
Vue.component('declarations-new', require('./components/admin/DeclarationsNew.vue').default);
Vue.component('declarations-edit', require('./components/admin/DeclarationsEdit.vue').default);

Vue.component('forms', require('./components/admin/Forms.vue').default);
Vue.component('forms-new', require('./components/admin/FormsNew.vue').default);
Vue.component('forms-edit', require('./components/admin/FormsEdit.vue').default);

Vue.component('categories', require('./components/admin/Categories.vue').default);
Vue.component('categories-new', require('./components/admin/CategoriesNew.vue').default);
Vue.component('categories-edit', require('./components/admin/CategoriesEdit.vue').default);

Vue.component('settings', require('./components/admin/Settings.vue').default);

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
    el: '#admin_app',
    router,
});
