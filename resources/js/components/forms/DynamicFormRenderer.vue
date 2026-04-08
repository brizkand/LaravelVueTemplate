<script setup>
	import {computed, reactive, watch} from 'vue'
	import DynamicFieldRenderer from './DynamicFieldRenderer.vue'

	const props = defineProps({
		form: {
			type: Object,
			required: true,
		},
		previewMode: {
			type: Boolean,
			default: false,
		},
		submitting: {
			type: Boolean,
			default: false,
		},
	})

	const emit = defineEmits(['submit'])

	const answers = reactive({})
	const errors = reactive({})
	const submittingState = reactive({
		loading: false,
	})

	const activeFields = computed(() => {
		return [...(props.form?.fields || [])].filter((field) => field && field.is_active !== false).sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
	})

	const initializeAnswers = () => {
		activeFields.value.forEach((field) => {
			if (!field) return
			if (answers[field.id] !== undefined) return

			switch (field.type) {
				case 'checkbox':
					answers[field.id] = []
					break

				case 'rating':
					answers[field.id] = 0
					break

				case 'multiple_choice_grid':
					answers[field.id] = {}
					break

				case 'checkbox_grid':
					answers[field.id] = {}
					break

				case 'linear_scale':
					answers[field.id] = null
					break

				default:
					answers[field.id] = null
					break
			}
		})
	}

	watch(activeFields, () => initializeAnswers(), {immediate: true, deep: true})

	const validateField = (field, value) => {
		if (!field) return ''

		if (field.is_required) {
			if (field.type === 'checkbox') {
				if (Array.isArray(value)) {
					if (value.length === 0) return 'This question is required.'
				} else if (value?.selected && Array.isArray(value.selected)) {
					if (value.selected.length === 0) return 'This question is required.'
				} else {
					return 'This question is required.'
				}
			}

			if (field.type === 'rating') {
				if (!value || Number(value) < 1) {
					return 'Please provide a rating.'
				}
			}

			if (field.type === 'linear_scale') {
				if (value === null || value === undefined || value === '') {
					return 'This question is required.'
				}
			}

			if (field.type === 'time') {
				if (!value) {
					return 'This question is required.'
				}
			}

			if (field.type === 'multiple_choice_grid') {
				const rows = field.field_settings?.rows || []
				const hasMissingRow = rows.some((row) => !value?.[row.value])

				if (hasMissingRow) {
					return 'Please answer all rows in this grid.'
				}
			}

			if (field.type === 'checkbox_grid') {
				const rows = field.field_settings?.rows || []
				const hasMissingRow = rows.some((row) => !Array.isArray(value?.[row.value]) || value[row.value].length === 0)

				if (hasMissingRow) {
					return 'Please answer all rows in this grid.'
				}
			}

			if (field.type !== 'checkbox' && field.type !== 'rating' && field.type !== 'linear_scale' && field.type !== 'time' && field.type !== 'multiple_choice_grid' && field.type !== 'checkbox_grid') {
				if (value === null || value === undefined || value === '') {
					return 'This question is required.'
				}
			}
		}

		if (value && field.type === 'email') {
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
			if (!emailRegex.test(value)) {
				return 'Please enter a valid email address.'
			}
		}

		if (field.type === 'number' && value !== null && value !== '' && value !== undefined) {
			const min = field.validation_rules?.min
			const max = field.validation_rules?.max

			if (min !== null && min !== undefined && Number(value) < Number(min)) {
				return `Value must be at least ${min}.`
			}

			if (max !== null && max !== undefined && Number(value) > Number(max)) {
				return `Value must not exceed ${max}.`
			}
		}

		if (field.type === 'rating' && value !== null && value !== '' && value !== undefined) {
			const min = field.validation_rules?.min
			const max = field.validation_rules?.max

			if (min !== null && min !== undefined && Number(value) < Number(min)) {
				return `Rating must be at least ${min}.`
			}

			if (max !== null && max !== undefined && Number(value) > Number(max)) {
				return `Rating must not exceed ${max}.`
			}
		}

		if (field.type === 'file') {
			if (!(value instanceof File)) {
				return 'Please upload a PDF file.'
			}

			if (value.type !== 'application/pdf') {
				return 'Only PDF files are allowed.'
			}

			const maxBytes = 2048 * 1024
			if (value.size > maxBytes) {
				return 'File size must not exceed 2048 KB.'
			}
		}

		return ''
	}

	const validateForm = () => {
		let isValid = true

		activeFields.value.forEach((field) => {
			const error = validateField(field, answers[field.id])
			errors[field.id] = error

			if (error) {
				isValid = false
			}
		})

		return isValid
	}

	const submitForm = async () => {
		if (props.previewMode) return

		const valid = validateForm()
		if (!valid) return

		submittingState.loading = true

		try {
			const hasFileField = activeFields.value.some((field) => field.type === 'file')

			if (hasFileField) {
				const formData = new FormData()

				formData.append('respondent_name', '')
				formData.append('respondent_email', '')

				const serializedAnswers = activeFields.value.map((field) => {
					const value = answers[field.id]

					if (field.type === 'file') {
						if (value instanceof File) {
							formData.append(`files[${field.id}]`, value)
						}

						return {
							field_id: field.id,
							value: null,
						}
					}

					return {
						field_id: field.id,
						value,
					}
				})

				formData.append('answers', JSON.stringify(serializedAnswers))

				emit('submit', formData)
				return
			}

			const payload = {
				respondent_name: null,
				respondent_email: null,
				answers: activeFields.value.map((field) => ({
					field_id: field.id,
					value: answers[field.id],
				})),
			}

			emit('submit', payload)
		} finally {
			submittingState.loading = false
		}
	}
</script>

<template>
	<div class="google-form-shell">
		<div class="form-banner" />

		<div class="form-card form-header-card">
			<h1 class="form-title">{{ form.title || 'Untitled Form' }}</h1>
			<p v-if="form.description" class="form-description">
				{{ form.description }}
			</p>

			<Divider />

			<div class="form-meta">
				<span class="required-text"> <span class="required-star">*</span> Required </span>
				<span v-if="previewMode" class="preview-badge">Preview Mode</span>
			</div>
		</div>

		<div v-if="!activeFields.length" class="form-card empty-card">
			<i class="pi pi-inbox text-3xl" />
			<h3>No questions yet</h3>
			<p>No active questions available in this form.</p>
		</div>

		<div v-for="field in activeFields" :key="field.id" class="form-card question-card">
			<DynamicFieldRenderer v-model="answers[field.id]" :field="field" :error="errors[field.id]" :readonly="previewMode" />
		</div>

		<div v-if="activeFields.length" class="form-actions">
			<Button label="Submit" icon="pi pi-send" :loading="submitting || submittingState.loading" :disabled="previewMode" class="submit-btn" @click="submitForm" />
		</div>

		<Message v-if="previewMode" severity="info" class="mt-3"> Preview mode is enabled. Submission is disabled. </Message>
	</div>
</template>

<style scoped>
	.google-form-shell {
		max-width: 820px;
		margin: 0 auto;
		padding: 1.5rem 1rem 3rem;
	}

	.form-banner {
		height: 14px;
		border-radius: 16px 16px 0 0;
		background: linear-gradient(90deg, #673ab7, #7e57c2);
		margin-bottom: 0;
	}

	.form-card {
		background: var(--surface-card);
		border: 1px solid var(--surface-border);
		border-radius: 16px;
		padding: 1.5rem;
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
		margin-bottom: 1rem;
	}

	.form-header-card {
		border-top-left-radius: 0;
		border-top-right-radius: 0;
	}

	.form-title {
		margin: 0;
		font-size: 2rem;
		font-weight: 700;
		color: var(--text-color);
	}

	.form-description {
		margin-top: 0.75rem;
		margin-bottom: 0;
		color: var(--text-color-secondary);
		line-height: 1.6;
	}

	.form-meta {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		flex-wrap: wrap;
	}

	.required-text {
		font-size: 0.875rem;
		color: var(--text-color-secondary);
	}

	.required-star {
		color: #dc2626;
		font-weight: 700;
	}

	.preview-badge {
		background: #ede9fe;
		color: #6d28d9;
		padding: 0.4rem 0.75rem;
		border-radius: 999px;
		font-size: 0.8rem;
		font-weight: 600;
	}

	.question-card {
		border-left: 6px solid transparent;
		transition:
			border-color 0.2s ease,
			box-shadow 0.2s ease;
	}

	.question-card:hover {
		border-left-color: #7e57c2;
		box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
	}

	.empty-card {
		text-align: center;
		color: var(--text-color-secondary);
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 0.75rem;
		padding: 2rem;
	}

	.empty-card h3,
	.empty-card p {
		margin: 0;
	}

	.form-actions {
		display: flex;
		justify-content: flex-start;
		padding-top: 0.5rem;
	}

	.submit-btn {
		min-width: 140px;
	}
</style>
