import axios from 'axios'

const tokenName = import.meta.env.VITE_APP_AUTH_TOKEN_NAME

const instance = axios.create({
	baseURL: '/',
	headers: {
		Accept: 'application/json',
	},
})

const setAuthToken = (token) => {
	if (token) {
		instance.defaults.headers.common.Authorization = `Bearer ${token}`
	} else {
		delete instance.defaults.headers.common.Authorization
	}
}

const removeAuthToken = () => {
	delete instance.defaults.headers.common.Authorization
}

const restoreAuthToken = () => {
	const token = localStorage.getItem(tokenName)

	if (token) {
		setAuthToken(token)
		return token
	}

	return null
}

restoreAuthToken()

const normalizeError = (error) => {
	const response = error?.response

	throw {
		status: response?.status ?? null,
		message: response?.data?.message || error?.message || 'An unexpected error occurred.',
		errors: response?.data?.errors || null,
		data: response?.data || null,
	}
}

export default {
	instance,

	setAuthToken,
	removeAuthToken,
	restoreAuthToken,

	async get(url, params = {}) {
		try {
			const res = await instance.get(url, {params})
			return res.data
		} catch (error) {
			normalizeError(error)
		}
	},

	async post(url, data = {}) {
		try {
			const res = await instance.post(url, data)
			return res.data
		} catch (error) {
			normalizeError(error)
		}
	},

	async patch(url, data = {}) {
		try {
			const res = await instance.patch(url, data)
			return res.data
		} catch (error) {
			normalizeError(error)
		}
	},

	async put(url, data = {}) {
		try {
			const res = await instance.put(url, data)
			return res.data
		} catch (error) {
			normalizeError(error)
		}
	},

	async delete(url, data = {}) {
		try {
			const res = await instance.delete(url, {data})
			return res.data
		} catch (error) {
			normalizeError(error)
		}
	},
}
