import {defineStore} from 'pinia'
import {ref, computed} from 'vue'
import api from '@/api'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
	// STATE
	const user = ref(null)
	const userLoading = ref(false)
	const authToken = ref(null)
	// Auth Token Name (used for localStorage) from .env
	const authTokenName = import.meta.env.VITE_APP_AUTH_TOKEN_NAME

	// GETTERS (computed)
	const TOKEN_NAME = computed(() => authTokenName)
	const TOKEN = computed(() => authToken.value)
	const USER = computed(() => user.value)
	const USER_LOADING = computed(() => userLoading.value)

	// ACTIONS

	// Authenticate Token
	const AUTHENTICATE_TOKEN = (token) => {
		api.setAuthToken(token)
		authToken.value = token
		localStorage.setItem(authTokenName, token)
	}

	// Revoke Token
	const REVOKE_TOKEN = async () => {
		await api.post('/api/auth/logout')

		api.removeAuthToken()
		authToken.value = null
		user.value = null
		localStorage.removeItem(authTokenName)

		router.replace('/auth/login')
	}

	// Get User Data from API
	const GET_USER_DATA = async () => {
		userLoading.value = true
		const response = await api.get('/api/auth/user')
		user.value = response
		userLoading.value = false
		return response
	}

	// Attempt Login
	const ATTEMPT_LOGIN = async (credentials) => {
		return await api.post('/api/auth/login', credentials)
	}

	// Attempt Register
	const ATTEMPT_REGISTER = async (credentials) => {
		return await api.post('/api/auth/register', credentials)
	}

	// Set Authenticated User
	// const LOGIN = (userData) => {
	// 	user.value = userData
	// }

	// Logout User - just clear user data and token, no API call needed here since we already have REVOKE_TOKEN for that
	// const LOGOUT = () => {
	// 	user.value = null
	// }

	return {
		// state
		user,
		userLoading,
		authToken,
		authTokenName,

		// getters
		TOKEN_NAME,
		TOKEN,
		USER,
		USER_LOADING,

		// actions
		AUTHENTICATE_TOKEN,
		REVOKE_TOKEN,
		GET_USER_DATA,
		ATTEMPT_LOGIN,
		// LOGIN,
		ATTEMPT_REGISTER,
		// LOGOUT,
	}
})
