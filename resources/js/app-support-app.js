import { createApp } from 'vue'
import axios from 'axios'
import { createRouter, createWebHistory } from 'vue-router'

// Axios global config
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios = axios

// Import components
import ApplicationsPage from './components/app_support/ApplicationsPage.vue'
import ApplicationDetailPage from './components/app_support/ApplicationDetailPage.vue'
import AppendixListPage from './components/app_support/AppendixListPage.vue'
import AppendixDetailPage from './components/app_support/AppendixDetailPage.vue'

const routes = [
    {
        path: '/',
        name: 'applications',
        component: ApplicationsPage
    },
    {
        path: '/applications/:id',
        name: 'application-detail',
        component: ApplicationDetailPage,
        props: true
    },
    {
        path: '/appendices',
        name: 'appendix-list',
        component: AppendixListPage
    },
    {
        path: '/appendices/:id',
        name: 'appendix-detail',
        component: AppendixDetailPage,
        props: true
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Create app
const app = createApp({})

// Register components
app.component('app-support-applications', ApplicationsPage)
app.component('app-support-application-detail', ApplicationDetailPage)
app.component('app-support-appendix-list', AppendixListPage)
app.component('app-support-appendix-detail', AppendixDetailPage)


// Use router
app.use(router)

// Disable devtools in Prod
if (process.env.MIX_APP_ENV === 'production') {
    app.config.devtools = false
    app.config.warnHandler = () => {}
    app.config.performance = false
}

// Mount app
app.mount('#app_support_app')
