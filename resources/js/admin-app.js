import { createApp } from 'vue'
import axios from 'axios'
import { createRouter, createWebHistory } from 'vue-router'

// Axios global config
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios = axios

import AdminUsers from './components/admin/Users.vue'
import AdminUsersNew from './components/admin/UsersNew.vue'
import AdminUsersEdit from './components/admin/UsersEdit.vue'

import SidePages from './components/admin/SidePages.vue'
import SidePagesNew from './components/admin/SidePagesNew.vue'
import SidePagesEdit from './components/admin/SidePagesEdit.vue'

import Alerts from './components/admin/Alerts.vue'
import AlertsNew from './components/admin/AlertsNew.vue'
import AlertsEdit from './components/admin/AlertsEdit.vue'

import Declarations from './components/admin/Declarations.vue'
import DeclarationsNew from './components/admin/DeclarationsNew.vue'
import DeclarationsEdit from './components/admin/DeclarationsEdit.vue'

import Forms from './components/admin/Forms.vue'
import FormsNew from './components/admin/FormsNew.vue'
import FormsEdit from './components/admin/FormsEdit.vue'

import Categories from './components/admin/Categories.vue'
import CategoriesNew from './components/admin/CategoriesNew.vue'
import CategoriesEdit from './components/admin/CategoriesEdit.vue'

import Settings from './components/admin/Settings.vue'

// Create app
const app = createApp({})

const routes = [
    // Users
    { path: '/dashboard/admin/users', component: AdminUsers },
    { path: '/dashboard/admin/users/new', component: AdminUsersNew },
    { path: '/dashboard/admin/users/edit/:id/', component: AdminUsersEdit, props: true },

    // Side Pages
    { path: '/dashboard/admin/side-pages', component: SidePages },
    { path: '/dashboard/admin/side-pages/new', component: SidePagesNew },
    { path: '/dashboard/admin/side-pages/edit/:id/', component: SidePagesEdit, props: true },

    // Alerts
    { path: '/dashboard/admin/alerts', component: Alerts },
    { path: '/dashboard/admin/alerts/new', component: AlertsNew }, // <-- Route manquante
    { path: '/dashboard/admin/alerts/edit/:id/', component: AlertsEdit, props: true },

    // Declarations
    { path: '/dashboard/admin/declarations', component: Declarations },
    { path: '/dashboard/admin/declarations/new', component: DeclarationsNew },
    { path: '/dashboard/admin/declarations/edit/:id/', component: DeclarationsEdit, props: true },

    // Forms
    { path: '/dashboard/admin/forms', component: Forms },
    { path: '/dashboard/admin/forms/new', component: FormsNew },
    { path: '/dashboard/admin/forms/edit/:id/', component: FormsEdit, props: true },

    // Categories
    { path: '/dashboard/admin/categories', component: Categories },
    { path: '/dashboard/admin/categories/new', component: CategoriesNew },
    { path: '/dashboard/admin/categories/edit/:id/', component: CategoriesEdit, props: true },

    // Settings
    { path: '/dashboard/admin/settings', component: Settings },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Register components
app.component('admin-users', AdminUsers)
app.component('admin-users-new', AdminUsersNew)
app.component('admin-users-edit', AdminUsersEdit)

app.component('side-pages', SidePages)
app.component('side-pages-new', SidePagesNew)
app.component('side-pages-edit', SidePagesEdit)

app.component('alerts', Alerts)
app.component('alerts-new', AlertsNew)
app.component('alerts-edit', AlertsEdit)

app.component('declarations', Declarations)
app.component('declarations-new', DeclarationsNew)
app.component('declarations-edit', DeclarationsEdit)

app.component('forms', Forms)
app.component('forms-new', FormsNew)
app.component('forms-edit', FormsEdit)

app.component('categories', Categories)
app.component('categories-new', CategoriesNew)
app.component('categories-edit', CategoriesEdit)

app.component('settings', Settings)

// Use router
app.use(router)

// Disable devtools in Prod env
if (process.env.MIX_APP_ENV === 'production') {
    app.config.devtools = false
    app.config.warnHandler = () => {}
    app.config.performance = false
}
// Mount app
app.mount('#admin_app')
