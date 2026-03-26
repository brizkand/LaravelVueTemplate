import {defineStore} from 'pinia'
import {ref, computed} from 'vue'
import api from '@/api'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
	const user = ref(null)
	const userLoading = ref(false)
	const authToken = ref(localStorage.getItem(import.meta.env.VITE_APP_AUTH_TOKEN_NAME) || null)

	const authTokenName = import.meta.env.VITE_APP_AUTH_TOKEN_NAME

	const TOKEN_NAME = computed(() => authTokenName)
	const TOKEN = computed(() => authToken.value)
	const USER = computed(() => user.value)
	const USER_LOADING = computed(() => userLoading.value)
	const IS_AUTHENTICATED = computed(() => !!authToken.value)

	const AUTHENTICATE_TOKEN = (token) => {
		api.setAuthToken(token)
		authToken.value = token
		localStorage.setItem(authTokenName, token)
	}

	const CLEAR_AUTH = () => {
		api.removeAuthToken()
		authToken.value = null
		user.value = null
		localStorage.removeItem(authTokenName)
	}

	const RESTORE_AUTH = () => {
		const token = api.restoreAuthToken()
		authToken.value = token
		return token
	}

	const REVOKE_TOKEN = async () => {
		try {
			await api.post('/api/auth/logout')
		} finally {
			CLEAR_AUTH()
			router.replace({name: 'auth.login'})
		}
	}

	const GET_USER_DATA = async () => {
		userLoading.value = true

		try {
			const response = await api.get('/api/auth/user')
			user.value = response
			return response
		} finally {
			userLoading.value = false
		}
	}

	const ATTEMPT_LOGIN = async (credentials) => {
		return await api.post('/api/auth/login', credentials)
	}

	const ATTEMPT_REGISTER = async (credentials) => {
		return await api.post('/api/auth/register', credentials)
	}

	return {
		user,
		userLoading,
		authToken,
		authTokenName,

		TOKEN_NAME,
		TOKEN,
		USER,
		USER_LOADING,
		IS_AUTHENTICATED,

		AUTHENTICATE_TOKEN,
		CLEAR_AUTH,
		RESTORE_AUTH,
		REVOKE_TOKEN,
		GET_USER_DATA,
		ATTEMPT_LOGIN,
		ATTEMPT_REGISTER,
	}
})
