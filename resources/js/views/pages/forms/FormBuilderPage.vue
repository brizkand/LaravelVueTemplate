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

	const form = ref(formStore.createEmptyForm())

	const formId = computed(() => route.params.id || null)
	const isEditMode = computed(() => Boolean(formId.value))

	onMounted(() => {
		formStore.loadForms()

		if (isEditMode.value) {
			const existingForm = formStore.getFormById(formId.value)

			if (!existingForm) {
				toast.add({
					severity: 'error',
					summary: 'Form Not Found',
					detail: 'The requested form could not be found.',
					life: 3000,
				})

				router.push({name: 'forms.list'})
				return
			}

			form.value = JSON.parse(JSON.stringify(existingForm))
		} else {
			form.value = formStore.createEmptyForm()
		}
	})

	const saveForm = (payload) => {
		if (isEditMode.value) {
			formStore.updateForm(formId.value, payload)

			toast.add({
				severity: 'success',
				summary: 'Form Updated',
				detail: 'The form has been updated successfully.',
				life: 3000,
			})
		} else {
			formStore.addForm(payload)

			toast.add({
				severity: 'success',
				summary: 'Form Created',
				detail: 'The form has been created successfully.',
				life: 3000,
			})
		}

		router.push({name: 'forms.list'})
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
		<!-- <h1>Form</h1>
		<pre
			>{{ form }}
        </pre
		>
		<Divider />
		<h1>Form Id</h1>
		<pre
			>{{ formId }}
        </pre
		>
		<Divider />
		<h1>Is Edit Mode</h1>
		<pre
			>{{ isEditMode }}
        </pre
		> -->

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
