import axios from 'axios'

const tokenName = import.meta.env.VITE_APP_AUTH_TOKEN_NAME
const token = localStorage.getItem(tokenName)

const api = axios.create({
	baseURL: '/',
	headers: {
		Accept: 'application/json',
	},
})

if (token) {
	api.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

export const setAuthToken = (token) => {
	if (token) {
		api.defaults.headers.common['Authorization'] = `Bearer ${token}`
	} else {
		delete api.defaults.headers.common['Authorization']
	}
}

export default api
