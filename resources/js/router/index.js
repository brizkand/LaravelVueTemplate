import {createRouter, createWebHistory} from 'vue-router'
import {getAuthRedirect, clearAuthRedirect} from '@/utils/authRedirect'

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
			{
				path: 'forms',
				children: [
					{
						path: '',
						name: 'forms.list',
						component: () => import('@/views/pages/forms/FormListPage.vue'),
					},
					{
						path: 'create',
						name: 'forms.create',
						component: () => import('@/views/pages/forms/FormBuilderPage.vue'),
					},
					{
						path: ':id/builder',
						name: 'forms.builder',
						component: () => import('@/views/pages/forms/FormBuilderPage.vue'),
						props: true,
					},
				],
			},
		],
	},
	{
		path: '/forms/:id/respond',
		name: 'forms.respond',
		component: () => import('@/views/pages/forms/PublicFormPage.vue'),
		props: true,
	},
	{
		path: '/forms/:id/thank-you',
		name: 'forms.thank-you',
		component: () => import('@/views/pages/forms/FormThankYouPage.vue'),
		props: true,
	},
	{
		path: '/auth',
		component: () => import('@/layouts/AuthLayout.vue'),
		meta: {guest: true},
		children: [
			{
				path: 'login',
				name: 'auth.login',
				component: () => import('@/views/pages/auth/Login.vue'),
			},
		],
	},
	{
		path: '/:pathMatch(.*)*',
		name: 'not-found',
		component: () => import('@/views/pages/errors/NotFound.vue'),
	},
]

const router = createRouter({
	history: createWebHistory(),
	routes,
	scrollBehavior(to, from, savedPosition) {
		if (savedPosition) {
			return savedPosition
		}

		return {top: 0}
	},
})

router.beforeEach((to) => {
	const tokenName = import.meta.env.VITE_APP_AUTH_TOKEN_NAME
	const userToken = localStorage.getItem(tokenName)

	if (to.meta.auth && !userToken) {
		return {
			name: 'auth.login',
		}
	}

	if (to.meta.guest && userToken) {
		const redirectTarget = getAuthRedirect()

		if (redirectTarget && redirectTarget !== '/') {
			clearAuthRedirect()
			return redirectTarget
		}

		clearAuthRedirect()
		return {name: 'dashboard'}
	}

	return true
})

export default router
