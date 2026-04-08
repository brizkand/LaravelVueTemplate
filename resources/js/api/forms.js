import api from '@/api'

export const getForms = (params = {}) => api.get('/api/forms', params)

export const getForm = (id) => api.get(`/api/forms/${id}`)

export const createForm = (payload) => api.post('/api/forms', payload)

export const updateForm = (id, payload) => api.put(`/api/forms/${id}`, payload)

export const deleteForm = (id) => api.delete(`/api/forms/${id}`)

export const getPublicForm = (id) => api.get(`/api/forms/${id}/respond`)

export const submitFormResponse = async (id, payload) => {
	if (payload instanceof FormData) {
		try {
			const res = await api.instance.post(`/api/forms/${id}/submit`, payload, {
				headers: {
					'Content-Type': 'multipart/form-data',
				},
			})

			return res.data
		} catch (error) {
			const response = error?.response

			throw {
				status: response?.status ?? null,
				message: response?.data?.message || error?.message || 'An unexpected error occurred.',
				errors: response?.data?.errors || null,
				data: response?.data || null,
			}
		}
	}

	return api.post(`/api/forms/${id}/submit`, payload)
}
