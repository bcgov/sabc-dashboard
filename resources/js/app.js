/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
require('./dynamic-events');


import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

// Create Vue instance
const app = createApp({});

// Import and register components
import DashboardAlerts from './components/DashboardAlerts.vue';
import DashboardLogin from './components/auth/DashboardLogin.vue';
import DashboardRegister from './components/auth/DashboardRegister.vue';
import DashboardCreateUser from './components/auth/DashboardCreateUser.vue';
import DashboardCreateBcscUser from './components/auth/DashboardCreateBcscUser.vue';
import CreateChallengeQuestions from './components/auth/CreateChallengeQuestions.vue';
import CreateUserId from './components/auth/CreateUserId.vue';
import PasswordStrength from './components/PasswordStrength.vue';

import DashboardBcscForgot from './components/auth/DashboardBcscForgot.vue';
import DashboardBcscForgotStep1 from './components/auth/DashboardBcscForgotStep1.vue';
import DashboardBcscForgotStep2 from './components/auth/DashboardBcscForgotStep2.vue';
import DashboardBcscForgotStep3 from './components/auth/DashboardBcscForgotStep3.vue';

import DashboardForgotPassword from './components/auth/DashboardForgotPassword.vue';
import DashboardForgotPasswordStep1 from './components/auth/DashboardForgotPasswordStep1.vue';
import DashboardForgotPasswordStep2 from './components/auth/DashboardForgotPasswordStep2.vue';
import DashboardForgotPasswordStep3 from './components/auth/DashboardForgotPasswordStep3.vue';
import DashboardForgotPasswordStep4 from './components/auth/DashboardForgotPasswordStep3Reset.vue';
import DashboardForgotPasswordStep5 from './components/auth/DashboardForgotPasswordStep3Delete.vue';
import DashboardForgotPasswordStep51 from './components/auth/DashboardForgotPasswordStep3Removed.vue';
import DashboardForgotPasswordStep6 from './components/auth/DashboardForgotPasswordStep6.vue';
import DashboardForgotPasswordStep7 from './components/auth/DashboardForgotPasswordStep7.vue';

import Dashboard404 from './components/Dashboard404.vue';
import Dashboard405 from './components/Dashboard405.vue';
import Dashboard500 from './components/Dashboard500.vue';
import Dashboard503 from './components/Dashboard503.vue';

import DashboardNavbarNoSin from './components/NavbarNoSin.vue';
import DashboardNavbar from './components/Navbar.vue';
import DashboardFooter from './components/Footer.vue';
import DashboardFooterNoneAuth from './components/FooterNoneAuth.vue';
import AsideComponent from './components/AsideComponent.vue';

import DashboardLinks from './components/DashboardLinks.vue';
import DashboardApps from './components/DashboardApps.vue';
import ApplicationPage from './components/ApplicationPage.vue';
import ApplicationSubmitChecklistPage from './components/ApplicationSubmitChecklistPage.vue';
import Appendix1SubmitChecklistPage from './components/Appendix1SubmitChecklistPage.vue';
import Appendix2SubmitChecklistPage from './components/Appendix2SubmitChecklistPage.vue';
import AppendixSubmitSuccessPage from './components/AppendixSubmitSuccessPage.vue';
import ApplicationActionButtons from './components/ApplicationActionButtons.vue';
import AppendixPage from './components/AppendixPage.vue';
import AppendixActionButtons from './components/AppendixActionButtons.vue';

import ApplicationCollapseFive from './components/ApplicationCollapseFive.vue';
import AppendixCollapseTwo from './components/AppendixCollapseTwo.vue';
import AppendixCollapseTwoLegacy from './components/AppendixCollapseTwoLegacy.vue';

import ApplicationApply from './components/ApplicationApplyPage.vue';

import Profile from './components/ProfilePage.vue';
import ProfileChallengeQuestions from './components/ProfileChallengeQuestions.vue';

import Notifications from './components/NotificationPage.vue';

import FileUploads from './components/FileUploadsPage.vue';
import FileUploadsList from './components/FileUploadsList.vue';

import StudentApply from './components/StudentApplyPage.vue';

import BcscVerificationRequired from './components/BcscRequiredPage.vue';
import InterestFree from './components/InterestFreePage.vue';
import Appendix1Claim from './components/Appendix1Claim.vue';
import Appendix2Claim from './components/Appendix2Claim.vue';
import Appendix1NoSinClaim from './components/Appendix1NoSinClaim.vue';
import Appendix2NoSinClaim from './components/Appendix2NoSinClaim.vue';
import NoSinDeclarationPage from './components/NoSinDeclarationPage.vue';

import FormPage from './components/FormPage.vue';
import FormStartPage from './components/FormStartPage.vue';
import FormNewPage from './components/FormNewPage.vue';
import FormShowPage from './components/FormShowPage.vue';
import FormsList from './components/FormsList.vue';

import AppealFormNew2021 from './components/forms/AppealFormNew2021.vue';
import AppealFormShow2021 from './components/forms/AppealFormShow2021.vue';
import AppealFormShow from './components/forms/AppealFormShow.vue';

app.component('dashboard-alerts', DashboardAlerts)
    .component('dashboard-login', DashboardLogin)
    .component('dashboard-register', DashboardRegister)
    .component('dashboard-create-user', DashboardCreateUser)
    .component('dashboard-create-bcsc-user', DashboardCreateBcscUser)
    .component('create-challenge-questions', CreateChallengeQuestions)
    .component('create-user-id', CreateUserId)
    .component('password-strength', PasswordStrength)
    .component('dashboard-bcsc-forgot', DashboardBcscForgot)
    .component('dashboard-bcsc-forgot-step-1', DashboardBcscForgotStep1)
    .component('dashboard-bcsc-forgot-step-2', DashboardBcscForgotStep2)
    .component('dashboard-bcsc-forgot-step-3', DashboardBcscForgotStep3)
    .component('dashboard-forgot-password', DashboardForgotPassword)
    .component('dashboard-forgot-password-step-1', DashboardForgotPasswordStep1)
    .component('dashboard-forgot-password-step-2', DashboardForgotPasswordStep2)
    .component('dashboard-forgot-password-step-3', DashboardForgotPasswordStep3)
    .component('dashboard-forgot-password-step-4', DashboardForgotPasswordStep4)
    .component('dashboard-forgot-password-step-5', DashboardForgotPasswordStep5)
    .component('dashboard-forgot-password-step-5-1', DashboardForgotPasswordStep51)
    .component('dashboard-forgot-password-step-6', DashboardForgotPasswordStep6)
    .component('dashboard-forgot-password-step-7', DashboardForgotPasswordStep7)
    .component('dashboard-404', Dashboard404)
    .component('dashboard-405', Dashboard405)
    .component('dashboard-500', Dashboard500)
    .component('dashboard-503', Dashboard503)
    .component('dashboard-navbar-no-sin', DashboardNavbarNoSin)
    .component('dashboard-navbar', DashboardNavbar)
    .component('dashboard-footer', DashboardFooter)
    .component('dashboard-footer-none-auth', DashboardFooterNoneAuth)
    .component('aside-component', AsideComponent)
    .component('dashboard-links', DashboardLinks)
    .component('dashboard-apps', DashboardApps)
    .component('application-page', ApplicationPage)
    .component('application-submit-checklist-page', ApplicationSubmitChecklistPage)
    .component('appendix1-submit-checklist-page', Appendix1SubmitChecklistPage)
    .component('appendix2-submit-checklist-page', Appendix2SubmitChecklistPage)
    .component('appendix-submit-success-page', AppendixSubmitSuccessPage)
    .component('application-action-buttons', ApplicationActionButtons)
    .component('appendix-page', AppendixPage)
    .component('appendix-action-buttons', AppendixActionButtons)
    .component('application-collapse-five', ApplicationCollapseFive)
    .component('appendix-collapse-two', AppendixCollapseTwo)
    .component('appendix-collapse-two-legacy', AppendixCollapseTwoLegacy)
    .component('application-apply', ApplicationApply)
    .component('profile', Profile)
    .component('profile-challenge-questions', ProfileChallengeQuestions)
    .component('notifications', Notifications)
    .component('file-uploads', FileUploads)
    .component('file-uploads-list', FileUploadsList)
    .component('student-apply', StudentApply)
    .component('bcsc-verification-required', BcscVerificationRequired)
    .component('interest-free', InterestFree)
    .component('appendix1-claim', Appendix1Claim)
    .component('appendix2-claim', Appendix2Claim)
    .component('appendix1-no-sin-claim', Appendix1NoSinClaim)
    .component('appendix2-no-sin-claim', Appendix2NoSinClaim)
    .component('no-sin-declaration-page', NoSinDeclarationPage)
    .component('form-page', FormPage)
    .component('form-start-page', FormStartPage)
    .component('form-new-page', FormNewPage)
    .component('form-show-page', FormShowPage)
    .component('forms-list', FormsList)
    .component('appeal-form-new-2021', AppealFormNew2021)
    .component('appeal-form-show-2021', AppealFormShow2021)
    .component('appeal-form-show', AppealFormShow);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const router = createRouter({
    history: createWebHistory(),
    scrollBehavior(to, from, savedPosition) {
        return { left: 0, top: 0 }
    },
    routes: [
    ]
});

if (process.env.MIX_APP_ENV === 'production') {
    app.config.devtools = false;
}

// Inject router
app.use(router);

// Mount app
app.mount('#app');
