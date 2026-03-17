import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

const STORAGE_KEY = 'crmis_forms'

const defaultForms = [
	{
		id: 1,
		title: 'Customer Satisfaction Feedback Form',
		description: 'We value your feedback. Please answer the questions below.',
		is_active: true,
		is_public: true,
		fields_count: 5,
		responses_count: 132,
		created_by: {
			id: 1,
			name: 'Kevin Holgado',
		},
		created_at: '2026-03-16 08:00:00',
		updated_at: '2026-03-16 08:30:00',
		fields: [
			{
				id: 101,
				type: 'short_text',
				label: 'Full Name',
				description: 'Optional',
				is_required: false,
				placeholder: 'Enter your full name',
				is_active: true,
				sort_order: 1,
				validation_rules: {},
				options: [],
			},
		],
	},
]

const nowString = () => {
	const now = new Date()
	const pad = (value) => String(value).padStart(2, '0')

	return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`
}

const deepClone = (value) => JSON.parse(JSON.stringify(value))

export const useFormStore = defineStore('form', () => {
	const forms = ref([])

	const loadForms = () => {
		const stored = localStorage.getItem(STORAGE_KEY)

		if (stored) {
			try {
				forms.value = JSON.parse(stored)
				return
			} catch (error) {
				console.error('Failed to parse forms from localStorage:', error)
			}
		}

		forms.value = deepClone(defaultForms)
		saveForms()
	}

	const saveForms = () => {
		localStorage.setItem(STORAGE_KEY, JSON.stringify(forms.value))
	}

	const allForms = computed(() => forms.value)

	const getFormById = (id) => {
		return forms.value.find((form) => Number(form.id) === Number(id)) || null
	}

	const createEmptyForm = () => ({
		id: null,
		title: '',
		description: '',
		is_active: true,
		is_public: true,
		fields: [],
	})

	const addForm = (payload) => {
		const form = deepClone(payload)
		const timestamp = nowString()
		const newId = Date.now()

		const newForm = {
			id: newId,
			title: form.title?.trim() || 'Untitled Form',
			description: form.description?.trim() || '',
			is_active: Boolean(form.is_active),
			is_public: Boolean(form.is_public),
			fields_count: Array.isArray(form.fields) ? form.fields.length : 0,
			responses_count: 0,
			created_by: {
				id: 1,
				name: 'Kevin Holgado',
			},
			created_at: timestamp,
			updated_at: timestamp,
			fields: Array.isArray(form.fields) ? form.fields : [],
		}

		forms.value.unshift(newForm)
		saveForms()

		return newForm
	}

	const updateForm = (id, payload) => {
		const index = forms.value.findIndex((form) => Number(form.id) === Number(id))

		if (index === -1) return null

		const form = deepClone(payload)

		forms.value[index] = {
			...forms.value[index],
			title: form.title?.trim() || 'Untitled Form',
			description: form.description?.trim() || '',
			is_active: Boolean(form.is_active),
			is_public: Boolean(form.is_public),
			fields: Array.isArray(form.fields) ? form.fields : [],
			fields_count: Array.isArray(form.fields) ? form.fields.length : 0,
			updated_at: nowString(),
		}

		saveForms()

		return forms.value[index]
	}

	const deleteForm = (id) => {
		forms.value = forms.value.filter((form) => Number(form.id) !== Number(id))
		saveForms()
	}

	const duplicateForm = (id) => {
		const target = getFormById(id)
		if (!target) return null

		const timestamp = nowString()
		const duplicated = deepClone(target)

		const newForm = {
			...duplicated,
			id: Date.now(),
			title: `${duplicated.title} (Copy)`,
			responses_count: 0,
			created_at: timestamp,
			updated_at: timestamp,
		}

		forms.value.unshift(newForm)
		saveForms()

		return newForm
	}

	return {
		forms,
		allForms,
		loadForms,
		saveForms,
		getFormById,
		createEmptyForm,
		addForm,
		updateForm,
		deleteForm,
		duplicateForm,
	}
})
