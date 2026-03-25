import {ref} from 'vue'
import {defineStore} from 'pinia'
import {getPublicForm, submitFormResponse} from '@/api/forms'

export const useFormResponseStore = defineStore('formResponse', () => {
	const form = ref(null)
	const loading = ref(false)
	const submitting = ref(false)

	const fetchForm = async (id) => {
		loading.value = true

		try {
			const {data} = await getPublicForm(id)
			form.value = data.data
			return data.data
		} finally {
			loading.value = false
		}
	}

	const submit = async (id, payload) => {
		submitting.value = true

		try {
			const {data} = await submitFormResponse(id, payload)
			return data
		} finally {
			submitting.value = false
		}
	}

	const clearForm = () => {
		form.value = null
	}

	return {
		form,
		loading,
		submitting,
		fetchForm,
		submit,
		clearForm,
	}
})
