import api from '@/api'

export const getForms = (params = {}) => {
	return api.get('/api/forms', params)
}

export const getForm = (id) => {
	return api.get(`/api/forms/${id}`)
}

export const createForm = (payload) => {
	return api.post('/api/forms', payload)
}

export const updateForm = (id, payload) => {
	return api.put(`/api/forms/${id}`, payload)
}

export const deleteForm = (id) => {
	return api.delete(`/api/forms/${id}`)
}

export const getPublicForm = (id) => {
	return api.get(`/api/forms/${id}/respond`)
}

export const submitFormResponse = (id, payload) => {
	return api.post(`/api/forms/${id}/submit`, payload)
}
