import {createRouter, createWebHistory} from 'vue-router'

const routes = [
	{
		path: '/',
		component: () => import('@/layouts/AppLayout.vue'),
		meta: {auth: true},
		children: [
			{
				path: '',
				name: 'dashboard',
				component: () => import('@/views/Dashboard.vue'),
			},
		],
	},
	{
		// Auth routes
		path: '/auth',
		component: () => import('@/layouts/AuthLayout.vue'),
		children: [
			{
				path: 'login',
				name: 'auth.login',
				component: () => import('@/views/pages/auth/Login.vue'),
			},
			{
				path: 'register',
				name: 'auth.register',
				component: () => import('@/views/pages/auth/Register.vue'),
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
