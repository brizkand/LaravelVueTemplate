<script setup>
	import {computed, ref, watch} from 'vue'
	import draggable from 'vuedraggable'
	import {useConfirm} from 'primevue/useconfirm'
	import {useToast} from 'primevue/usetoast'

	import FormFieldCard from './FormFieldCard.vue'
	import FieldEditorDialog from './FieldEditorDialog.vue'
	import DynamicFormRenderer from './DynamicFormRenderer.vue'

	const props = defineProps({
		modelValue: {
			type: Object,
			required: true,
		},
	})

	const emit = defineEmits(['update:modelValue', 'save', 'preview-submit'])

	const confirm = useConfirm()
	const toast = useToast()

	const cloneDeep = (value) => JSON.parse(JSON.stringify(value))

	const buildLocalState = (value = {}) => ({
		id: value?.id ?? null,
		title: value?.title ?? '',
		description: value?.description ?? '',
		is_active: value?.is_active ?? true,
		is_public: value?.is_public ?? true,
		fields: [...(value?.fields || [])].sort((a, b) => a.sort_order - b.sort_order),
	})

	const formState = ref(buildLocalState(props.modelValue))
	const editorVisible = ref(false)
	const editingField = ref(null)
	const previewMode = ref(false)

	watch(
		() => props.modelValue,
		(value) => {
			formState.value = buildLocalState(value)
		},
		{immediate: true},
	)

	const syncToParent = () => {
		emit('update:modelValue', cloneDeep(formState.value))
	}

	const totalQuestions = computed(() => formState.value.fields.length)

	const activeQuestions = computed(() => formState.value.fields.filter((field) => field.is_active !== false).length)

	const addQuestion = () => {
		editingField.value = null
		editorVisible.value = true
	}

	const editQuestion = (field) => {
		editingField.value = cloneDeep(field)
		editorVisible.value = true
	}

	const saveQuestion = (payload) => {
		if (payload.id) {
			const index = formState.value.fields.findIndex((field) => field.id === payload.id)

			if (index > -1) {
				formState.value.fields[index] = {
					...formState.value.fields[index],
					...cloneDeep(payload),
				}
			}
		} else {
			formState.value.fields.push({
				...cloneDeep(payload),
				id: Date.now(),
				sort_order: formState.value.fields.length + 1,
			})
		}

		reindexFields()
		syncToParent()
	}

	const duplicateQuestion = (field) => {
		const cloned = cloneDeep(field)

		formState.value.fields.push({
			...cloned,
			id: Date.now() + Math.floor(Math.random() * 1000),
			label: `${cloned.label} (Copy)`,
			sort_order: formState.value.fields.length + 1,
			options: (cloned.options || []).map((option, index) => ({
				...option,
				id: null,
				sort_order: index + 1,
			})),
		})

		reindexFields()
		syncToParent()
	}

	const deleteQuestion = (field) => {
		confirm.require({
			message: `Are you sure you want to delete "${field.label}"?`,
			header: 'Delete Question',
			icon: 'pi pi-exclamation-triangle',
			rejectProps: {
				label: 'Cancel',
				severity: 'secondary',
				outlined: true,
			},
			acceptProps: {
				label: 'Delete',
				severity: 'danger',
			},
			accept: () => {
				formState.value.fields = formState.value.fields.filter((item) => item.id !== field.id)
				reindexFields()
				syncToParent()
			},
		})
	}

	const reindexFields = () => {
		formState.value.fields = formState.value.fields
			.map((field, index) => ({
				...field,
				sort_order: index + 1,
			}))
			.sort((a, b) => a.sort_order - b.sort_order)
	}

	const onDragEnd = () => {
		reindexFields()
		syncToParent()
	}

	const validateForm = () => {
		if (!formState.value.title.trim()) {
			toast.add({
				severity: 'warn',
				summary: 'Validation Error',
				detail: 'Form title is required.',
				life: 3000,
			})
			return false
		}

		if (!formState.value.fields.length) {
			toast.add({
				severity: 'warn',
				summary: 'Validation Error',
				detail: 'Please add at least one question before saving.',
				life: 3000,
			})
			return false
		}

		return true
	}

	const saveForm = () => {
		if (!validateForm()) return

		const payload = cloneDeep({
			...formState.value,
			title: formState.value.title.trim(),
			description: formState.value.description?.trim() || '',
			fields: formState.value.fields.map((field, index) => ({
				...field,
				sort_order: index + 1,
			})),
		})

		emit('save', payload)
	}

	const handlePreviewSubmit = (payload) => {
		emit('preview-submit', payload)
	}
</script>

<template>
	<div class="builder-layout">
		<!-- <ConfirmDialog /> -->

		<div class="builder-sidebar">
			<Card class="sidebar-card">
				<template #title>
					<div class="sidebar-title-row">
						<span>Form Settings</span>
						<Tag :value="formState.is_active ? 'Active' : 'Inactive'" rounded />
					</div>
				</template>

				<template #content>
					<div class="sidebar-section">
						<label class="sidebar-label">Form Title</label>
						<InputText
							:modelValue="formState.title"
							class="w-full"
							placeholder="Untitled Form"
							@update:modelValue="
								(value) => {
									formState.title = value
									syncToParent()
								}
							" />
					</div>

					<div class="sidebar-section">
						<label class="sidebar-label">Description</label>
						<Textarea
							:modelValue="formState.description"
							rows="4"
							autoResize
							class="w-full"
							placeholder="Form description"
							@update:modelValue="
								(value) => {
									formState.description = value
									syncToParent()
								}
							" />
					</div>

					<Divider />

					<div class="sidebar-stats">
						<div class="stat-card">
							<div class="stat-value">{{ totalQuestions }}</div>
							<div class="stat-label">Total Questions</div>
						</div>

						<div class="stat-card">
							<div class="stat-value">{{ activeQuestions }}</div>
							<div class="stat-label">Active Questions</div>
						</div>
					</div>

					<Divider />

					<div class="sidebar-toggle-row">
						<div>
							<div class="toggle-title">Form Active</div>
							<div class="toggle-subtitle">Allow form to be used</div>
						</div>
						<ToggleSwitch
							:modelValue="formState.is_active"
							@update:modelValue="
								(value) => {
									formState.is_active = value
									syncToParent()
								}
							" />
					</div>

					<div class="sidebar-toggle-row">
						<div>
							<div class="toggle-title">Public Form</div>
							<div class="toggle-subtitle">Accessible without login</div>
						</div>
						<ToggleSwitch
							:modelValue="formState.is_public"
							@update:modelValue="
								(value) => {
									formState.is_public = value
									syncToParent()
								}
							" />
					</div>

					<Divider />

					<div class="sidebar-actions">
						<Button label="Add Question" icon="pi pi-plus" class="w-full" @click="addQuestion" />

						<Button
							:label="previewMode ? 'Back to Builder' : 'Preview Form'"
							:icon="previewMode ? 'pi pi-pencil' : 'pi pi-eye'"
							severity="secondary"
							outlined
							class="w-full"
							@click="previewMode = !previewMode" />

						<Button label="Save Form" icon="pi pi-save" severity="contrast" class="w-full" @click="saveForm" />
					</div>
				</template>
			</Card>
		</div>

		<div class="builder-main">
			<div v-if="!previewMode" class="builder-canvas">
				<div class="builder-header-card">
					<div class="builder-header-banner" />
					<div class="builder-header-content">
						<h1>{{ formState.title || 'Untitled Form' }}</h1>
						<p>{{ formState.description || 'Form description will appear here.' }}</p>
					</div>
				</div>

				<div class="builder-toolbar">
					<div class="builder-toolbar-left">
						<h2>Questions</h2>
						<span>{{ totalQuestions }} item(s)</span>
					</div>

					<Button label="Add Question" icon="pi pi-plus" @click="addQuestion" />
				</div>

				<div v-if="!formState.fields.length" class="empty-builder-state">
					<i class="pi pi-file-edit text-4xl" />
					<h3>No questions yet</h3>
					<p>Start building your form by adding the first question.</p>
					<Button label="Add First Question" icon="pi pi-plus" @click="addQuestion" />
				</div>

				<draggable v-else v-model="formState.fields" item-key="id" handle=".drag-handle" animation="200" class="fields-list" @end="onDragEnd">
					<template #item="{element, index}">
						<FormFieldCard :field="element" :index="index" @edit="editQuestion" @duplicate="duplicateQuestion" @delete="deleteQuestion" />
					</template>
				</draggable>
			</div>

			<div v-else class="preview-wrapper">
				<DynamicFormRenderer :form="formState" :previewMode="true" @submit="handlePreviewSubmit" />
			</div>
		</div>

		<FieldEditorDialog v-model:visible="editorVisible" :field="editingField" @save="saveQuestion" />
	</div>
</template>

<style scoped>
	.builder-layout {
		display: grid;
		grid-template-columns: 320px minmax(0, 1fr);
		gap: 1.5rem;
		align-items: start;
	}

	.builder-sidebar {
		position: sticky;
		top: 1rem;
	}

	.sidebar-card {
		border-radius: 20px;
		overflow: hidden;
	}

	.sidebar-title-row {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 0.75rem;
	}

	.sidebar-section {
		display: flex;
		flex-direction: column;
		gap: 0.5rem;
		margin-bottom: 1rem;
	}

	.sidebar-label {
		font-size: 0.875rem;
		font-weight: 600;
		color: var(--text-color);
	}

	.sidebar-stats {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 0.75rem;
	}

	.stat-card {
		padding: 1rem;
		border-radius: 16px;
		background: var(--surface-50);
		border: 1px solid var(--surface-border);
		text-align: center;
	}

	.stat-value {
		font-size: 1.5rem;
		font-weight: 700;
		color: var(--text-color);
	}

	.stat-label {
		font-size: 0.875rem;
		color: var(--text-color-secondary);
		margin-top: 0.25rem;
	}

	.sidebar-toggle-row {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		padding: 0.75rem 0;
	}

	.toggle-title {
		font-weight: 600;
	}

	.toggle-subtitle {
		font-size: 0.875rem;
		color: var(--text-color-secondary);
		margin-top: 0.25rem;
	}

	.sidebar-actions {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}

	.builder-main {
		min-width: 0;
	}

	.builder-header-card {
		background: var(--surface-card);
		border-radius: 20px;
		overflow: hidden;
		border: 1px solid var(--surface-border);
		box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
		margin-bottom: 1rem;
	}

	.builder-header-banner {
		height: 16px;
		background: linear-gradient(90deg, #673ab7, #7e57c2);
	}

	.builder-header-content {
		padding: 1.5rem;
	}

	.builder-header-content h1 {
		margin: 0;
		font-size: 2rem;
		font-weight: 700;
	}

	.builder-header-content p {
		margin: 0.75rem 0 0;
		color: var(--text-color-secondary);
	}

	.builder-toolbar {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		margin-bottom: 1rem;
		flex-wrap: wrap;
	}

	.builder-toolbar-left {
		display: flex;
		align-items: baseline;
		gap: 0.75rem;
		flex-wrap: wrap;
	}

	.builder-toolbar-left h2 {
		margin: 0;
		font-size: 1.25rem;
		font-weight: 700;
	}

	.builder-toolbar-left span {
		color: var(--text-color-secondary);
		font-size: 0.95rem;
	}

	.empty-builder-state {
		background: var(--surface-card);
		border: 1px dashed var(--surface-border);
		border-radius: 20px;
		padding: 3rem 1.5rem;
		text-align: center;
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 0.75rem;
		color: var(--text-color-secondary);
	}

	.empty-builder-state h3,
	.empty-builder-state p {
		margin: 0;
	}

	.fields-list {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.preview-wrapper {
		background: linear-gradient(180deg, var(--surface-ground), var(--surface-50));
		border-radius: 20px;
		padding: 1rem;
		border: 1px solid var(--surface-border);
	}

	@media (max-width: 1100px) {
		.builder-layout {
			grid-template-columns: 1fr;
		}

		.builder-sidebar {
			position: static;
		}
	}
</style>
