<script setup>
	import {computed, ref, watch} from 'vue'
	import draggable from 'vuedraggable'

	const props = defineProps({
		visible: {
			type: Boolean,
			default: false,
		},
		field: {
			type: Object,
			default: null,
		},
	})

	const emit = defineEmits(['update:visible', 'save'])

	const dialogVisible = computed({
		get: () => props.visible,
		set: (value) => emit('update:visible', value),
	})

	const fieldTypes = [
		{label: 'Short Text', value: 'short_text', icon: 'pi pi-pencil'},
		{label: 'Long Text', value: 'long_text', icon: 'pi pi-align-left'},
		{label: 'Number', value: 'number', icon: 'pi pi-hashtag'},
		{label: 'Email', value: 'email', icon: 'pi pi-envelope'},
		{label: 'Dropdown', value: 'dropdown', icon: 'pi pi-chevron-down'},
		{label: 'Multiple Choice', value: 'radio', icon: 'pi pi-circle'},
		{label: 'Checkbox', value: 'checkbox', icon: 'pi pi-check-square'},
		{label: 'Date', value: 'date', icon: 'pi pi-calendar'},
		{label: 'Rating (1–5)', value: 'rating', icon: 'pi pi-star'},
		{label: 'File Upload', value: 'file', icon: 'pi pi-upload'},
	]

	const createDefaultForm = () => ({
		id: null,
		type: 'short_text',
		label: '',
		description: '',
		is_required: false,
		placeholder: '',
		is_active: true,
		sort_order: 1,
		validation_rules: {
			min: null,
			max: null,
			max_size_kb: null,
			allowed_types: [],
		},
		options: [],
		allow_other_option: false,
		other_option_label: 'Other',
	})

	const localForm = ref(createDefaultForm())
	const formError = ref('')
	const allowedTypesText = ref('')

	const resetForm = () => {
		localForm.value = createDefaultForm()
		allowedTypesText.value = ''
		formError.value = ''
	}

	const fillForm = (value) => {
		if (value) {
			localForm.value = {
				id: value.id ?? null,
				type: value.type ?? 'short_text',
				label: value.label ?? '',
				description: value.description ?? '',
				is_required: Boolean(value.is_required),
				placeholder: value.placeholder ?? '',
				is_active: value.is_active !== false,
				sort_order: value.sort_order ?? 1,
				validation_rules: {
					min: value.validation_rules?.min ?? null,
					max: value.validation_rules?.max ?? null,
					max_size_kb: value.validation_rules?.max_size_kb ?? null,
					allowed_types: Array.isArray(value.validation_rules?.allowed_types) ? value.validation_rules.allowed_types : [],
				},
				options: Array.isArray(value.options)
					? value.options.map((option, index) => ({
							id: option.id ?? null,
							label: option.label ?? '',
							value: option.value ?? '',
							sort_order: option.sort_order ?? index + 1,
						}))
					: [],
				allow_other_option: Boolean(value.allow_other_option),
				other_option_label: value.other_option_label ?? 'Other',
			}
		} else {
			resetForm()
		}

		allowedTypesText.value = (localForm.value.validation_rules.allowed_types || []).join(', ')
		formError.value = ''
	}

	watch(
		() => props.field,
		(value) => {
			fillForm(value)
		},
		{immediate: true, deep: true},
	)

	watch(
		() => props.visible,
		(value) => {
			if (value && !props.field) {
				resetForm()
			}
		},
	)

	const dialogTitle = computed(() => (localForm.value.id ? 'Edit Question' : 'Add Question'))

	const requiresOptions = computed(() => ['dropdown', 'radio', 'checkbox'].includes(localForm.value.type))

	const supportsPlaceholder = computed(() => ['short_text', 'long_text', 'number', 'email'].includes(localForm.value.type))

	const supportsMinMax = computed(() => ['number', 'rating'].includes(localForm.value.type))

	const supportsFileRules = computed(() => localForm.value.type === 'file')

	const supportsOtherOption = computed(() => ['radio', 'checkbox'].includes(localForm.value.type))

	const addOption = () => {
		localForm.value.options.push({
			id: null,
			label: '',
			value: '',
			sort_order: localForm.value.options.length + 1,
		})
	}

	const removeOption = (index) => {
		localForm.value.options.splice(index, 1)
		reindexOptions()
	}

	const reindexOptions = () => {
		localForm.value.options = localForm.value.options.map((option, index) => ({
			...option,
			sort_order: index + 1,
		}))
	}

	const onOptionDragEnd = () => {
		reindexOptions()
	}

	const autoFillOptionValue = (index) => {
		const option = localForm.value.options[index]

		if (!option) return
		if (option.value?.trim()) return

		option.value = option.label
			.trim()
			.toLowerCase()
			.replace(/[^\w\s-]/g, '')
			.replace(/\s+/g, '_')
	}

	const normalizeValidationRules = () => {
		const allowedTypes = allowedTypesText.value
			.split(',')
			.map((item) => item.trim().toLowerCase())
			.filter(Boolean)

		const rules = {
			min: null,
			max: null,
			max_size_kb: null,
			allowed_types: [],
		}

		if (supportsMinMax.value) {
			rules.min =
				localForm.value.validation_rules.min !== null && localForm.value.validation_rules.min !== undefined && localForm.value.validation_rules.min !== ''
					? Number(localForm.value.validation_rules.min)
					: null

			rules.max =
				localForm.value.validation_rules.max !== null && localForm.value.validation_rules.max !== undefined && localForm.value.validation_rules.max !== ''
					? Number(localForm.value.validation_rules.max)
					: null
		}

		if (supportsFileRules.value) {
			rules.max_size_kb =
				localForm.value.validation_rules.max_size_kb !== null && localForm.value.validation_rules.max_size_kb !== undefined && localForm.value.validation_rules.max_size_kb !== ''
					? Number(localForm.value.validation_rules.max_size_kb)
					: null

			rules.allowed_types = allowedTypes
		}

		return rules
	}

	const validateForm = () => {
		formError.value = ''

		if (!localForm.value.label.trim()) {
			formError.value = 'Question label is required.'
			return false
		}

		if (requiresOptions.value) {
			if (!localForm.value.options.length) {
				formError.value = 'Please add at least one option.'
				return false
			}

			const hasInvalidOption = localForm.value.options.some((option) => !option.label.trim() || !option.value.trim())

			if (hasInvalidOption) {
				formError.value = 'All options must have both label and value.'
				return false
			}
		}

		if (supportsMinMax.value) {
			const min = localForm.value.validation_rules.min
			const max = localForm.value.validation_rules.max

			if (min !== null && min !== '' && max !== null && max !== '' && Number(min) > Number(max)) {
				formError.value = 'Minimum value cannot be greater than maximum value.'
				return false
			}
		}

		if (supportsOtherOption.value && localForm.value.allow_other_option) {
			if (!localForm.value.other_option_label.trim()) {
				formError.value = 'Other option label is required.'
				return false
			}
		}

		return true
	}

	const buildPayload = () => {
		reindexOptions()

		return {
			id: localForm.value.id,
			type: localForm.value.type,
			label: localForm.value.label.trim(),
			description: localForm.value.description?.trim() || '',
			is_required: localForm.value.is_required,
			placeholder: supportsPlaceholder.value ? localForm.value.placeholder?.trim() || '' : '',
			is_active: localForm.value.is_active,
			sort_order: localForm.value.sort_order ?? 1,
			validation_rules: normalizeValidationRules(),
			options: requiresOptions.value
				? localForm.value.options.map((option, index) => ({
						id: option.id ?? null,
						label: option.label.trim(),
						value: option.value.trim(),
						sort_order: index + 1,
					}))
				: [],
			allow_other_option: supportsOtherOption.value ? localForm.value.allow_other_option : false,
			other_option_label: supportsOtherOption.value ? localForm.value.other_option_label.trim() || 'Other' : null,
		}
	}

	const saveField = () => {
		if (!validateForm()) return

		const isEditMode = Boolean(localForm.value.id)

		emit('save', buildPayload())

		if (isEditMode) {
			dialogVisible.value = false
			return
		}

		resetForm()
		dialogVisible.value = false
	}

	const closeDialog = () => {
		dialogVisible.value = false
	}
</script>

<template>
	<Dialog v-model:visible="dialogVisible" modal closable :dismissableMask="true" :style="{width: '950px', maxWidth: '95vw'}" :header="dialogTitle">
		<div class="editor-dialog-body">
			<div class="editor-grid">
				<div class="field-block">
					<label class="editor-label">Question Type</label>
					<Select v-model="localForm.type" :options="fieldTypes" optionLabel="label" optionValue="value" class="w-full" placeholder="Select type">
						<template #option="{option}">
							<div class="type-option">
								<i :class="option.icon" />
								<span>{{ option.label }}</span>
							</div>
						</template>

						<template #value="{value, placeholder}">
							<div v-if="value" class="type-option">
								<i :class="fieldTypes.find((item) => item.value === value)?.icon || 'pi pi-question-circle'" />
								<span>
									{{ fieldTypes.find((item) => item.value === value)?.label || value }}
								</span>
							</div>
							<span v-else>{{ placeholder }}</span>
						</template>
					</Select>
				</div>

				<div class="field-block">
					<label class="editor-label">Order</label>
					<InputNumber v-model="localForm.sort_order" :min="1" inputClass="w-full" class="w-full" placeholder="1" />
				</div>

				<div class="field-block field-block-full">
					<label class="editor-label">Question Label</label>
					<InputText v-model="localForm.label" class="w-full" placeholder="Enter your question" />
				</div>

				<div class="field-block field-block-full">
					<label class="editor-label">Description</label>
					<Textarea v-model="localForm.description" rows="3" autoResize class="w-full" placeholder="Optional helper text" />
				</div>

				<div v-if="supportsPlaceholder" class="field-block field-block-full">
					<label class="editor-label">Placeholder</label>
					<InputText v-model="localForm.placeholder" class="w-full" placeholder="Optional placeholder" />
				</div>
			</div>

			<Divider />

			<div v-if="requiresOptions" class="section-block">
				<div class="section-header">
					<div>
						<h3>Options</h3>
						<p>Drag to reorder your options.</p>
					</div>

					<Button type="button" label="Add Option" icon="pi pi-plus" severity="secondary" outlined @click="addOption" />
				</div>

				<div v-if="!localForm.options.length" class="options-empty">No options yet.</div>

				<draggable v-else v-model="localForm.options" item-key="sort_order" handle=".option-drag-handle" animation="200" class="options-list" @end="onOptionDragEnd">
					<template #item="{element, index}">
						<div class="option-row">
							<div class="option-drag-handle" title="Drag to reorder">
								<i class="pi pi-bars" />
							</div>

							<div class="option-index">
								{{ index + 1 }}
							</div>

							<InputText v-model="element.label" class="w-full" placeholder="Option label" @blur="autoFillOptionValue(index)" />

							<InputText v-model="element.value" class="w-full" placeholder="Option value" />

							<Button type="button" icon="pi pi-trash" text rounded severity="danger" @click="removeOption(index)" />
						</div>
					</template>
				</draggable>

				<div v-if="supportsOtherOption" class="setting-item">
					<div>
						<div class="setting-title">Allow “Other” option</div>
						<div class="setting-text">Respondents can type their own answer.</div>
					</div>
					<ToggleSwitch v-model="localForm.allow_other_option" />
				</div>

				<div v-if="supportsOtherOption && localForm.allow_other_option" class="field-block">
					<label class="editor-label">Other Option Label</label>
					<InputText v-model="localForm.other_option_label" class="w-full" placeholder="Other" />
				</div>
			</div>

			<div v-if="supportsMinMax || supportsFileRules" class="section-block">
				<Divider />

				<div class="section-header">
					<div>
						<h3>Validation Rules</h3>
						<p>Optional validation settings.</p>
					</div>
				</div>

				<div class="editor-grid">
					<template v-if="supportsMinMax">
						<div class="field-block">
							<label class="editor-label">Minimum</label>
							<InputNumber v-model="localForm.validation_rules.min" class="w-full" inputClass="w-full" placeholder="Optional" />
						</div>

						<div class="field-block">
							<label class="editor-label">Maximum</label>
							<InputNumber v-model="localForm.validation_rules.max" class="w-full" inputClass="w-full" placeholder="Optional" />
						</div>
					</template>

					<template v-if="supportsFileRules">
						<div class="field-block">
							<label class="editor-label">Max File Size (KB)</label>
							<InputNumber v-model="localForm.validation_rules.max_size_kb" class="w-full" inputClass="w-full" placeholder="e.g. 2048" />
						</div>

						<div class="field-block field-block-full">
							<label class="editor-label">Allowed File Types</label>
							<InputText v-model="allowedTypesText" class="w-full" placeholder="Example: pdf, docx, jpg, png" />
							<small class="helper-text">Separate file types with commas.</small>
						</div>
					</template>
				</div>
			</div>

			<Divider />

			<div class="settings-row">
				<div class="setting-item">
					<div>
						<div class="setting-title">Required Question</div>
						<div class="setting-text">Respondents must answer this question.</div>
					</div>
					<ToggleSwitch v-model="localForm.is_required" />
				</div>

				<div class="setting-item">
					<div>
						<div class="setting-title">Active Question</div>
						<div class="setting-text">Inactive questions will not appear in the form.</div>
					</div>
					<ToggleSwitch v-model="localForm.is_active" />
				</div>
			</div>

			<Message v-if="formError" severity="error">
				{{ formError }}
			</Message>
		</div>

		<template #footer>
			<div class="dialog-footer">
				<Button type="button" label="Cancel" severity="secondary" text @click="closeDialog" />
				<Button type="button" label="Save Question" icon="pi pi-check" @click="saveField" />
			</div>
		</template>
	</Dialog>
</template>

<style scoped>
	.editor-dialog-body {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.editor-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 1rem;
	}

	.field-block {
		display: flex;
		flex-direction: column;
		gap: 0.5rem;
	}

	.field-block-full {
		grid-column: 1 / -1;
	}

	.editor-label {
		font-size: 0.875rem;
		font-weight: 600;
		color: var(--text-color);
	}

	.type-option {
		display: flex;
		align-items: center;
		gap: 0.75rem;
	}

	.section-block {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.section-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		flex-wrap: wrap;
	}

	.section-header h3 {
		margin: 0;
		font-size: 1rem;
		font-weight: 700;
	}

	.section-header p {
		margin: 0.25rem 0 0;
		font-size: 0.875rem;
		color: var(--text-color-secondary);
	}

	.options-empty {
		padding: 1rem;
		border: 1px dashed var(--surface-border);
		border-radius: 12px;
		background: var(--surface-50);
		color: var(--text-color-secondary);
	}

	.options-list {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}

	.option-row {
		display: grid;
		grid-template-columns: 44px 52px 1fr 1fr auto;
		gap: 0.75rem;
		align-items: center;
	}

	.option-drag-handle {
		width: 44px;
		height: 44px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 12px;
		background: var(--surface-100);
		color: var(--text-color-secondary);
		cursor: grab;
	}

	.option-index {
		width: 44px;
		height: 44px;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 12px;
		background: var(--surface-100);
		color: var(--text-color-secondary);
		font-weight: 700;
	}

	.settings-row {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.setting-item {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		padding: 1rem;
		border: 1px solid var(--surface-border);
		border-radius: 14px;
		background: var(--surface-50);
	}

	.setting-title {
		font-weight: 600;
		color: var(--text-color);
	}

	.setting-text {
		margin-top: 0.25rem;
		font-size: 0.875rem;
		color: var(--text-color-secondary);
	}

	.helper-text {
		color: var(--text-color-secondary);
		font-size: 0.8rem;
	}

	.dialog-footer {
		display: flex;
		align-items: center;
		justify-content: flex-end;
		gap: 0.75rem;
		width: 100%;
	}

	@media (max-width: 768px) {
		.editor-grid {
			grid-template-columns: 1fr;
		}

		.option-row {
			grid-template-columns: 1fr;
		}

		.option-index,
		.option-drag-handle {
			width: 100%;
		}
	}
</style>
