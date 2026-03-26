<script setup>
	import {computed, onMounted, onUnmounted} from 'vue'
	import {useRoute, useRouter} from 'vue-router'
	import {useToast} from 'primevue/usetoast'
	import {useFormResponseStore} from '@/stores/formResponse'
	import DynamicFormRenderer from '@/components/forms/DynamicFormRenderer.vue'
	import {forceAuthRedirect} from '@/utils/authRedirect'

	const route = useRoute()
	const router = useRouter()
	const toast = useToast()
	const formResponseStore = useFormResponseStore()

	const formId = computed(() => route.params.id)
	const form = computed(() => formResponseStore.form)
	const loading = computed(() => formResponseStore.loading)
	const submitting = computed(() => formResponseStore.submitting)

	const goLoginWithRedirect = () => {
		const targetPath = `/forms/${formId.value}/respond`

		forceAuthRedirect(targetPath)

		router.replace({
			name: 'auth.login',
		})
	}

	onMounted(async () => {
		try {
			await formResponseStore.fetchForm(formId.value)
		} catch (error) {
			const status = error?.status
			const detail = error?.message || 'Unable to load this form.'

			toast.add({
				severity: 'error',
				summary: 'Load Failed',
				detail,
				life: 4000,
			})

			if (status === 401) {
				goLoginWithRedirect()
				return
			}

			if (status === 403 || status === 404) {
				router.push({name: 'not-found'})
				return
			}

			router.push({name: 'dashboard'})
		}
	})

	onUnmounted(() => {
		formResponseStore.clearForm()
	})

	const submitForm = async (payload) => {
		try {
			const response = await formResponseStore.submit(formId.value, payload)

			toast.add({
				severity: 'success',
				summary: 'Submitted',
				detail: response?.message || 'Form submitted successfully.',
				life: 4000,
			})

			router.push({
				name: 'forms.thank-you',
				params: {id: formId.value},
				query: {
					reference_code: response?.data?.reference_code,
					submitted_at: response?.data?.submitted_at,
				},
			})
		} catch (error) {
			const status = error?.status
			let detail = error?.message || 'Failed to submit form.'

			if (error?.errors) {
				const firstError = Object.values(error.errors)[0]
				if (Array.isArray(firstError) && firstError.length) {
					detail = firstError[0]
				}
			}

			toast.add({
				severity: 'error',
				summary: 'Submission Failed',
				detail,
				life: 5000,
			})

			if (status === 401) {
				goLoginWithRedirect()
			}
		}
	}
</script>

<template>
	<div class="public-form-page">
		<Toast />

		<div v-if="loading" class="loading-state">
			<ProgressSpinner />
			<p>Loading form...</p>
		</div>

		<div v-else-if="form" class="form-wrapper">
			<DynamicFormRenderer :form="form" :previewMode="false" :submitting="submitting" @submit="submitForm" />
		</div>

		<div v-else class="empty-state">
			<i class="pi pi-inbox text-4xl" />
			<h2>Form not available</h2>
			<p>This form could not be loaded.</p>

			<div class="empty-actions">
				<Button label="Go to Login" icon="pi pi-sign-in" severity="secondary" outlined @click="goLoginWithRedirect" />
			</div>
		</div>
	</div>
</template>

<style scoped>
	.public-form-page {
		padding: 1rem;
		min-height: 100vh;
		background: var(--surface-ground);
	}

	.loading-state,
	.empty-state {
		min-height: 60vh;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		gap: 1rem;
		color: var(--text-color-secondary);
		text-align: center;
	}

	.form-wrapper {
		padding: 1rem 0 2rem;
	}

	.empty-actions {
		margin-top: 0.5rem;
	}
</style>
