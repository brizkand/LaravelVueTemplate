import axios from 'axios'

export default {
	setAuthToken(token) {
		axios.defaults.headers.common.Authorization = `Bearer ${token}`
	},

	removeAuthToken() {
		delete axios.defaults.headers.common.Authorization
	},

	async get(url, params = {}) {
		try {
			const res = await axios.get(url, {params})
			return res.data
		} catch (e) {
			throw e.response?.data || e.message
		}
	},

	async post(url, data = {}) {
		try {
			const res = await axios.post(url, data)
			return res.data
		} catch (e) {
			throw e.response?.data || e.message
		}
	},

	async patch(url, data = {}) {
		try {
			const res = await axios.patch(url, data)
			return res.data
		} catch (e) {
			throw e.response?.data || e.message
		}
	},

	async put(url, data = {}) {
		try {
			const res = await axios.put(url, data)
			return res.data
		} catch (e) {
			throw e.response?.data || e.message
		}
	},

	async delete(url, data = {}) {
		try {
			const res = await axios.delete(url, {data})
			return res.data
		} catch (e) {
			throw e.response?.data || e.message
		}
	},
}
