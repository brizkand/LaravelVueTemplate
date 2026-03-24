import {computed, ref} from 'vue'
import {defineStore} from 'pinia'
import * as formApi from '@/api/forms'

const cloneDeep = (value) => JSON.parse(JSON.stringify(value))

export const useFormStore = defineStore('form', () => {
	const forms = ref([])
	const form = ref(null)
	const loading = ref(false)
	const saving = ref(false)
	const meta = ref(null)

	const formCount = computed(() => forms.value.length)

	const fetchForms = async (params = {}) => {
		loading.value = true

		try {
			const {data} = await formApi.getForms(params)
			forms.value = data.data
			meta.value = data.meta ?? null
			return data
		} finally {
			loading.value = false
		}
	}

	const fetchForm = async (id) => {
		loading.value = true

		try {
			const {data} = await formApi.getForm(id)
			form.value = data.data
			return data.data
		} finally {
			loading.value = false
		}
	}

	const saveForm = async (payload, id = null) => {
		saving.value = true

		try {
			const response = id ? await formApi.updateForm(id, payload) : await formApi.createForm(payload)

			const savedForm = response.data.data

			// keep list in sync if already loaded
			if (id) {
				const index = forms.value.findIndex((item) => Number(item.id) === Number(id))
				if (index > -1) {
					forms.value[index] = savedForm
				}
			} else {
				const exists = forms.value.some((item) => Number(item.id) === Number(savedForm.id))
				if (!exists) {
					forms.value.unshift(savedForm)
				}
			}

			form.value = savedForm

			return savedForm
		} finally {
			saving.value = false
		}
	}

	const updateForm = async (id, payload) => {
		return await saveForm(payload, id)
	}

	const removeForm = async (id) => {
		await formApi.deleteForm(id)
		forms.value = forms.value.filter((item) => Number(item.id) !== Number(id))

		if (form.value && Number(form.value.id) === Number(id)) {
			form.value = null
		}
	}

	const duplicateForm = async (id) => {
		// Load the full original form first so fields/options are included
		const original = await fetchForm(id)

		const duplicatePayload = cloneDeep({
			title: `${original.title} (Copy)`,
			description: original.description ?? '',
			is_active: original.is_active,
			is_public: original.is_public,
			fields: (original.fields || []).map((field, fieldIndex) => ({
				type: field.type,
				label: field.label,
				description: field.description ?? '',
				is_required: field.is_required,
				placeholder: field.placeholder ?? '',
				sort_order: field.sort_order ?? fieldIndex + 1,
				is_active: field.is_active !== false,
				validation_rules: field.validation_rules ?? {
					min: null,
					max: null,
					max_size_kb: null,
					allowed_types: [],
				},
				options: (field.options || []).map((option, optionIndex) => ({
					label: option.label,
					value: option.value,
					sort_order: option.sort_order ?? optionIndex + 1,
				})),
			})),
		})

		const duplicated = await saveForm(duplicatePayload)

		return duplicated
	}

	const clearForm = () => {
		form.value = null
	}

	return {
		forms,
		form,
		loading,
		saving,
		meta,
		formCount,
		fetchForms,
		fetchForm,
		saveForm,
		updateForm,
		removeForm,
		duplicateForm,
		clearForm,
	}
})
