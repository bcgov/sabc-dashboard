/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
require('./dynamic-events');


//window.Vue = require('vue');
import Vue from 'vue/dist/vue'
import VueRouter from 'vue-router/dist/vue-router';

window.Vue = Vue

Vue.use(VueRouter);


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component('dashboard-alerts', require('./components/DashboardAlerts.vue').default);

Vue.component('dashboard-login', require('./components/auth/DashboardLogin.vue').default);
Vue.component('dashboard-register', require('./components/auth/DashboardRegister.vue').default);
Vue.component('dashboard-create-user', require('./components/auth/DashboardCreateUser.vue').default);
Vue.component('dashboard-create-bcsc-user', require('./components/auth/DashboardCreateBcscUser.vue').default);
Vue.component('create-challenge-questions', require('./components/auth/CreateChallengeQuestions.vue').default);
Vue.component('create-user-id', require('./components/auth/CreateUserId.vue').default);
Vue.component('password-strength', require('./components/PasswordStrength.vue').default);

Vue.component('dashboard-bcsc-forgot', require('./components/auth/DashboardBcscForgot.vue').default);
Vue.component('dashboard-bcsc-forgot-step-1', require('./components/auth/DashboardBcscForgotStep1.vue').default);
Vue.component('dashboard-bcsc-forgot-step-2', require('./components/auth/DashboardBcscForgotStep2.vue').default);
Vue.component('dashboard-bcsc-forgot-step-3', require('./components/auth/DashboardBcscForgotStep3.vue').default);

Vue.component('dashboard-forgot-password', require('./components/auth/DashboardForgotPassword.vue').default);
Vue.component('dashboard-forgot-password-step-1', require('./components/auth/DashboardForgotPasswordStep1.vue').default);
Vue.component('dashboard-forgot-password-step-2', require('./components/auth/DashboardForgotPasswordStep2.vue').default);
Vue.component('dashboard-forgot-password-step-3', require('./components/auth/DashboardForgotPasswordStep3.vue').default);
Vue.component('dashboard-forgot-password-step-4', require('./components/auth/DashboardForgotPasswordStep3Reset.vue').default);
Vue.component('dashboard-forgot-password-step-5', require('./components/auth/DashboardForgotPasswordStep3Delete.vue').default);
Vue.component('dashboard-forgot-password-step-5-1', require('./components/auth/DashboardForgotPasswordStep3Removed.vue').default);
Vue.component('dashboard-forgot-password-step-6', require('./components/auth/DashboardForgotPasswordStep6.vue').default);
Vue.component('dashboard-forgot-password-step-7', require('./components/auth/DashboardForgotPasswordStep7.vue').default);

Vue.component('dashboard-404', require('./components/Dashboard404.vue').default);
Vue.component('dashboard-405', require('./components/Dashboard405.vue').default);
Vue.component('dashboard-500', require('./components/Dashboard500.vue').default);
Vue.component('dashboard-503', require('./components/Dashboard503.vue').default);

Vue.component('dashboard-navbar-no-sin', require('./components/NavbarNoSin.vue').default);
Vue.component('dashboard-navbar', require('./components/Navbar.vue').default);
Vue.component('dashboard-footer', require('./components/Footer.vue').default);
Vue.component('dashboard-footer-none-auth', require('./components/FooterNoneAuth.vue').default);
Vue.component('aside-component', require('./components/AsideComponent.vue').default);

Vue.component('dashboard-links', require('./components/DashboardLinks.vue').default);
Vue.component('dashboard-apps', require('./components/DashboardApps.vue').default);
Vue.component('application-page', require('./components/ApplicationPage.vue').default);
Vue.component('application-submit-checklist-page', require('./components/ApplicationSubmitChecklistPage.vue').default);
Vue.component('appendix1-submit-checklist-page', require('./components/Appendix1SubmitChecklistPage.vue').default);
Vue.component('appendix2-submit-checklist-page', require('./components/Appendix2SubmitChecklistPage.vue').default);
Vue.component('appendix-submit-success-page', require('./components/AppendixSubmitSuccessPage.vue').default);
Vue.component('application-action-buttons', require('./components/ApplicationActionButtons.vue').default);
Vue.component('appendix-page', require('./components/AppendixPage.vue').default);
Vue.component('appendix-action-buttons', require('./components/AppendixActionButtons.vue').default);

Vue.component('application-collapse-five', require('./components/ApplicationCollapseFive.vue').default);
Vue.component('appendix-collapse-two', require('./components/AppendixCollapseTwo.vue').default);
Vue.component('appendix-collapse-two-legacy', require('./components/AppendixCollapseTwoLegacy.vue').default);

Vue.component('application-apply', require('./components/ApplicationApplyPage.vue').default);

Vue.component('profile', require('./components/ProfilePage.vue').default);
Vue.component('profile-challenge-questions', require('./components/ProfileChallengeQuestions.vue').default);

Vue.component('notifications', require('./components/NotificationPage.vue').default);

Vue.component('file-uploads', require('./components/FileUploadsPage.vue').default);
Vue.component('file-uploads-list', require('./components/FileUploadsList.vue').default);

Vue.component('student-apply', require('./components/StudentApplyPage.vue').default);

Vue.component('bcsc-verification-required', require('./components/BcscRequiredPage.vue').default);
Vue.component('interest-free', require('./components/InterestFreePage.vue').default);
Vue.component('appendix1-claim', require('./components/Appendix1Claim.vue').default);
Vue.component('appendix2-claim', require('./components/Appendix2Claim.vue').default);
Vue.component('appendix1-no-sin-claim', require('./components/Appendix1NoSinClaim.vue').default);
Vue.component('appendix2-no-sin-claim', require('./components/Appendix2NoSinClaim.vue').default);
Vue.component('no-sin-declaration-page', require('./components/NoSinDeclarationPage.vue').default);

Vue.component('form-page', require('./components/FormPage.vue').default);
Vue.component('form-start-page', require('./components/FormStartPage.vue').default);
Vue.component('form-new-page', require('./components/FormNewPage.vue').default);
Vue.component('form-show-page', require('./components/FormShowPage.vue').default);
Vue.component('forms-list', require('./components/FormsList.vue').default);

Vue.component('appeal-form-new-2021', require('./components/forms/AppealFormNew2021.vue').default);
Vue.component('appeal-form-show-2021', require('./components/forms/AppealFormShow2021.vue').default);
Vue.component('appeal-form-show', require('./components/forms/AppealFormShow.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = new VueRouter({
    mode: 'history',
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
    },
    routes: [
        //{ path: '/dashboard', component: require('./components/LandingPage.vue').default},
    ]
});
if (process.env.MIX_APP_ENV === 'production') {
    Vue.config.devtools = false;
    Vue.config.debug = false;
    Vue.config.silent = true;
}
const app = new Vue({
    el: '#app',
    router,
});

//https://laracasts.com/discuss/channels/vue/laravel-not-detecting-ajax-request-when-made-with-vue-axios
//window.axios = require('axios');
//axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
