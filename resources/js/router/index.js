import {createRouter, createWebHistory} from 'vue-router'

import Home from '../pages/Home.vue'
import About from '../pages/About.vue'
// import Login from '../pages/Login.vue'

// import MainLayout from '@/layouts/MainLayout.vue'
import Dashboard from '@/views/Dashboard.vue'
import Dashboard2 from '@/views/Dashboard2.vue'

import AppLayout from '@/layouts/AppLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import Login from '@/views/pages/auth/Login.vue'
import Register from '@/views/pages/auth/Register.vue'

const routes = [
	// {path: '/', name: 'home', component: Home},
	// {path: '/about', name: 'about', component: About},
	// {path: '/login', name: 'login', component: Login},

	// {
	// 	path: '/',
	// 	component: MainLayout,
	// 	children: [
	// 		{
	// 			path: '',
	// 			component: Dashboard,
	// 		},
	// 	],
	// },

	{
		path: '/',
		component: AppLayout,
		meta: {auth: true},
		children: [
			{
				path: '',
				name: 'dashboard',
				component: Dashboard2,
			},
		],
	},
	{
		// Auth routes
		path: '/auth',
		component: AuthLayout,
		children: [
			{
				path: 'login',
				name: 'auth.login',
				component: Login,
			},
			{
				path: 'register',
				name: 'auth.register',
				component: Register,
			},
		],
	},
]

const router = createRouter({
	history: createWebHistory(),
	routes,
})

// ROUTER GUARDS
router.beforeEach((to, from, next) => {
	const tokenName = import.meta.env.VITE_APP_AUTH_TOKEN_NAME
	const userToken = localStorage.getItem(tokenName)

	if (to.meta.auth) {
		if (!userToken) {
			next({name: 'auth.login'})
		}
		next()
	} else {
		if (userToken) {
			next({name: 'dashboard'})
		}
		next()
	}
})

export default router
