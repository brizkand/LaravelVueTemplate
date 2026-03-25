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

			if (field.type === 'checkbox') {
				answers[field.id] = []
			} else if (field.type === 'rating') {
				answers[field.id] = 0
			} else {
				answers[field.id] = null
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
			} else if (field.type === 'rating') {
				if (!value || Number(value) < 1) {
					return 'Please provide a rating.'
				}
			} else if (value === null || value === undefined || value === '') {
				return 'This question is required.'
			}
		}

		if (value && field.type === 'email') {
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
			if (!emailRegex.test(value)) {
				return 'Please enter a valid email address.'
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
