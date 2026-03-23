import {defineStore} from 'pinia'
import * as formApi from '@/api/forms'

export const useFormStore = defineStore('form', {
	state: () => ({
		forms: [],
		form: null,
		loading: false,
		saving: false,
		meta: null,
	}),

	actions: {
		async fetchForms(params = {}) {
			this.loading = true

			try {
				const {data} = await formApi.getForms(params)
				this.forms = data.data
				this.meta = data.meta ?? null
				return data
			} finally {
				this.loading = false
			}
		},

		async fetchForm(id) {
			this.loading = true

			try {
				const {data} = await formApi.getForm(id)
				this.form = data.data
				return data.data
			} finally {
				this.loading = false
			}
		},

		async saveForm(payload, id = null) {
			this.saving = true

			try {
				const response = id ? await formApi.updateForm(id, payload) : await formApi.createForm(payload)

				return response.data.data
			} finally {
				this.saving = false
			}
		},

		async removeForm(id) {
			await formApi.deleteForm(id)
			this.forms = this.forms.filter((form) => Number(form.id) !== Number(id))
		},
	},
})
