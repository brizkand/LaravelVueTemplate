<script setup>
	import {computed, onMounted, ref} from 'vue'
	import {useRoute, useRouter} from 'vue-router'
	import {useToast} from 'primevue/usetoast'
	import FormBuilder from '@/components/forms/FormBuilder.vue'
	import {useFormStore} from '@/stores/form'

	const route = useRoute()
	const router = useRouter()
	const toast = useToast()
	const formStore = useFormStore()

	const form = ref({
		id: null,
		title: '',
		description: '',
		is_active: true,
		is_public: true,
		fields: [],
	})

	const formId = computed(() => route.params.id || null)
	const isEditMode = computed(() => Boolean(formId.value))

	onMounted(async () => {
		if (isEditMode.value) {
			try {
				const existingForm = await formStore.fetchForm(formId.value)
				form.value = JSON.parse(JSON.stringify(existingForm))
			} catch (error) {
				toast.add({
					severity: 'error',
					summary: 'Form Not Found',
					detail: 'Unable to load the selected form.',
					life: 3000,
				})
				router.push({name: 'forms.list'})
			}
		}
	})

	const saveForm = async (payload) => {
		try {
			await formStore.saveForm(payload, isEditMode.value ? formId.value : null)

			toast.add({
				severity: 'success',
				summary: isEditMode.value ? 'Form Updated' : 'Form Created',
				detail: isEditMode.value ? 'The form has been updated successfully.' : 'The form has been created successfully.',
				life: 3000,
			})

			router.push({name: 'forms.list'})
		} catch (error) {
			const response = error?.response?.data

			let detail = 'Failed to save form.'

			if (response?.message) {
				detail = response.message
			}

			if (response?.errors) {
				const firstError = Object.values(response.errors)[0]
				if (Array.isArray(firstError) && firstError.length) {
					detail = firstError[0]
				}
			}

			toast.add({
				severity: 'error',
				summary: 'Save Failed',
				detail,
				life: 4000,
			})
		}
	}

	const previewSubmit = () => {
		toast.add({
			severity: 'info',
			summary: 'Preview Mode',
			detail: 'Submission is disabled in preview mode.',
			life: 3000,
		})
	}

	const goBack = () => {
		router.push({name: 'forms.list'})
	}
</script>

<template>
	<div class="form-builder-page">
		<!-- <Toast /> -->

		<div class="page-header">
			<div>
				<h1>{{ isEditMode ? 'Edit Form' : 'Create Form' }}</h1>
				<p>
					{{ isEditMode ? 'Update your dynamic feedback form.' : 'Create a new dynamic feedback form for CRMIS.' }}
				</p>
			</div>

			<div class="page-header-actions">
				<Button label="Back to Forms" icon="pi pi-arrow-left" severity="secondary" outlined @click="goBack" />
			</div>
		</div>

		<FormBuilder v-model="form" @save="saveForm" @preview-submit="previewSubmit" />
	</div>
</template>

<style scoped>
	.form-builder-page {
		display: flex;
		flex-direction: column;
		gap: 1.5rem;
		padding: 1rem;
	}

	.page-header {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 1rem;
		flex-wrap: wrap;
	}

	.page-header h1 {
		margin: 0;
		font-size: 2rem;
		font-weight: 700;
	}

	.page-header p {
		margin: 0.5rem 0 0;
		color: var(--text-color-secondary);
	}

	.page-header-actions {
		display: flex;
		align-items: center;
		gap: 0.75rem;
	}
</style>
