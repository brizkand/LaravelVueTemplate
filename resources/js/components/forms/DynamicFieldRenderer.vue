<script setup>
	import {computed, ref} from 'vue'
	import RatingInput from './RatingInput.vue'

	const props = defineProps({
		field: {
			type: Object,
			required: true,
		},
		modelValue: {
			type: [String, Number, Array, Object, Date, File, null],
			default: null,
		},
		error: {
			type: String,
			default: '',
		},
		readonly: {
			type: Boolean,
			default: false,
		},
	})

	const emit = defineEmits(['update:modelValue'])

	const safeField = computed(() => props.field || null)

	const normalizedOptions = computed(() => {
		if (!safeField.value) return []

		return (safeField.value.options || []).map((option, index) => ({
			id: option.id ?? index + 1,
			label: option.label,
			value: option.value,
		}))
	})

	const linearScaleOptions = computed(() => {
		const start = Number(safeField.value?.field_settings?.scale_start ?? 1)
		const end = Number(safeField.value?.field_settings?.scale_end ?? 5)

		if (Number.isNaN(start) || Number.isNaN(end) || end < start) return []

		return Array.from({length: end - start + 1}, (_, index) => start + index)
	})

	const gridRows = computed(() => safeField.value?.field_settings?.rows ?? [])
	const gridColumns = computed(() => safeField.value?.field_settings?.columns ?? [])

	const radioSelectedValue = computed(() => {
		if (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue) && !(props.modelValue instanceof File)) {
			return props.modelValue.selected ?? null
		}

		return props.modelValue
	})

	const radioOtherText = computed(() => {
		if (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue) && !(props.modelValue instanceof File)) {
			return props.modelValue.other_text ?? ''
		}

		return ''
	})

	const checkboxValue = computed(() => {
		if (Array.isArray(props.modelValue)) return props.modelValue

		if (props.modelValue && typeof props.modelValue === 'object' && !(props.modelValue instanceof File) && Array.isArray(props.modelValue.selected)) {
			return props.modelValue.selected
		}

		return []
	})

	const checkboxOtherText = computed(() => {
		if (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue) && !(props.modelValue instanceof File)) {
			return props.modelValue.other_text ?? ''
		}

		return ''
	})

	const inputId = computed(() => {
		if (!safeField.value?.id) {
			return `field-temp-${Math.random().toString(36).slice(2, 9)}`
		}

		return `field-${safeField.value.id}`
	})

	const fileInputRef = ref(null)
	const isDraggingFile = ref(false)
	const fileError = ref('')

	const effectiveError = computed(() => fileError.value || props.error)

	const maxFileSizeKb = 5096
	const maxFileSizeBytes = maxFileSizeKb * 1024

	const updateCheckbox = (optionValue, checked) => {
		const current = [...checkboxValue.value]

		if (checked) {
			if (!current.includes(optionValue)) current.push(optionValue)
		} else {
			const index = current.indexOf(optionValue)
			if (index > -1) current.splice(index, 1)
		}

		if (current.includes('__other__')) {
			emit('update:modelValue', {
				selected: current,
				other_text: checkboxOtherText.value,
			})
		} else {
			emit('update:modelValue', current)
		}
	}

	const updateCheckboxOtherText = (value) => {
		emit('update:modelValue', {
			selected: checkboxValue.value,
			other_text: value,
		})
	}

	const updateRadioOther = (value) => {
		emit('update:modelValue', {
			selected: '__other__',
			other_text: value,
		})
	}

	const updateMultipleChoiceGrid = (rowValue, selectedColumnValue) => {
		emit('update:modelValue', {
			...(props.modelValue || {}),
			[rowValue]: selectedColumnValue,
		})
	}

	const updateCheckboxGrid = (rowValue, columnValue, checked) => {
		const currentModel = props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue) && !(props.modelValue instanceof File) ? {...props.modelValue} : {}

		const currentRow = Array.isArray(currentModel[rowValue]) ? [...currentModel[rowValue]] : []

		if (checked) {
			if (!currentRow.includes(columnValue)) currentRow.push(columnValue)
		} else {
			const index = currentRow.indexOf(columnValue)
			if (index > -1) currentRow.splice(index, 1)
		}

		currentModel[rowValue] = currentRow

		emit('update:modelValue', currentModel)
	}

	const validateSelectedFile = (file) => {
		if (!file) {
			return {valid: false, message: 'No file selected.'}
		}

		const isPdf = file.type === 'application/pdf' || file.name?.toLowerCase().endsWith('.pdf')

		if (!isPdf) {
			return {valid: false, message: 'Only PDF files are allowed.'}
		}

		if (file.size > maxFileSizeBytes) {
			return {valid: false, message: `File size must not exceed ${maxFileSizeKb} KB.`}
		}

		return {valid: true, message: ''}
	}

	const applySelectedFile = (file) => {
		if (!file) {
			fileError.value = ''
			emit('update:modelValue', null)
			return
		}

		const result = validateSelectedFile(file)

		if (!result.valid) {
			fileError.value = result.message
			emit('update:modelValue', null)

			if (fileInputRef.value) {
				fileInputRef.value.value = ''
			}

			return
		}

		fileError.value = ''
		emit('update:modelValue', file)
	}

	const openFilePicker = () => {
		if (props.readonly) return
		fileInputRef.value?.click()
	}

	const handleFileSelect = (event) => {
		const file = event.target.files?.[0] || null
		applySelectedFile(file)
	}

	const handleFileDragOver = (event) => {
		if (props.readonly) return

		event.preventDefault()
		isDraggingFile.value = true
	}

	const handleFileDragLeave = (event) => {
		if (props.readonly) return

		event.preventDefault()
		isDraggingFile.value = false
	}

	const handleFileDrop = (event) => {
		if (props.readonly) return

		event.preventDefault()
		isDraggingFile.value = false

		const file = event.dataTransfer?.files?.[0] || null
		applySelectedFile(file)
	}

	const clearSelectedFile = () => {
		if (props.readonly) return

		fileError.value = ''
		emit('update:modelValue', null)

		if (fileInputRef.value) {
			fileInputRef.value.value = ''
		}
	}
</script>

<template>
	<div v-if="safeField" class="dynamic-field" :class="{'has-error': effectiveError}">
		<div class="field-header">
			<label class="field-label" :for="inputId">
				{{ safeField.label }}
				<span v-if="safeField.is_required" class="required-mark">*</span>
			</label>

			<p v-if="safeField.description" class="field-description">
				{{ safeField.description }}
			</p>
		</div>

		<div class="field-control">
			<InputText
				v-if="safeField.type === 'short_text'"
				:id="inputId"
				:modelValue="modelValue"
				:placeholder="safeField.placeholder || 'Your answer'"
				class="w-full"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<Textarea
				v-else-if="safeField.type === 'long_text'"
				:id="inputId"
				:modelValue="modelValue"
				:placeholder="safeField.placeholder || 'Your answer'"
				class="w-full"
				rows="4"
				autoResize
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<InputNumber
				v-else-if="safeField.type === 'number'"
				:inputId="inputId"
				:modelValue="modelValue"
				class="w-full"
				:placeholder="safeField.placeholder || 'Enter a number'"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<InputText
				v-else-if="safeField.type === 'email'"
				:id="inputId"
				:modelValue="modelValue"
				type="email"
				class="w-full"
				:placeholder="safeField.placeholder || 'Enter your email'"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<Select
				v-else-if="safeField.type === 'dropdown'"
				:modelValue="modelValue"
				:options="normalizedOptions"
				optionLabel="label"
				optionValue="value"
				placeholder="Choose an option"
				class="w-full"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<div v-else-if="safeField.type === 'radio'" class="choice-group">
				<div v-for="option in normalizedOptions" :key="option.id" class="choice-item">
					<RadioButton :inputId="`${inputId}-radio-${option.id}`" :modelValue="radioSelectedValue" :value="option.value" :disabled="readonly" @update:modelValue="emit('update:modelValue', $event)" />
					<label :for="`${inputId}-radio-${option.id}`">
						{{ option.label }}
					</label>
				</div>

				<div v-if="safeField.allow_other_option" class="choice-item other-option-block">
					<RadioButton
						:inputId="`${inputId}-radio-other`"
						:modelValue="radioSelectedValue"
						value="__other__"
						:disabled="readonly"
						@update:modelValue="
							emit('update:modelValue', {
								selected: '__other__',
								other_text: radioOtherText,
							})
						" />
					<label :for="`${inputId}-radio-other`">
						{{ safeField.other_option_label || 'Other' }}
					</label>

					<InputText
						v-if="radioSelectedValue === '__other__'"
						:modelValue="radioOtherText"
						placeholder="Please specify"
						class="other-input"
						:disabled="readonly"
						@update:modelValue="updateRadioOther" />
				</div>
			</div>

			<div v-else-if="safeField.type === 'checkbox'" class="choice-group">
				<div v-for="option in normalizedOptions" :key="option.id" class="choice-item">
					<Checkbox
						:inputId="`${inputId}-checkbox-${option.id}`"
						:binary="true"
						:modelValue="checkboxValue.includes(option.value)"
						:disabled="readonly"
						@update:modelValue="updateCheckbox(option.value, $event)" />
					<label :for="`${inputId}-checkbox-${option.id}`">
						{{ option.label }}
					</label>
				</div>

				<div v-if="safeField.allow_other_option" class="choice-item other-option-block">
					<Checkbox
						:inputId="`${inputId}-checkbox-other`"
						:binary="true"
						:modelValue="checkboxValue.includes('__other__')"
						:disabled="readonly"
						@update:modelValue="updateCheckbox('__other__', $event)" />
					<label :for="`${inputId}-checkbox-other`">
						{{ safeField.other_option_label || 'Other' }}
					</label>

					<InputText
						v-if="checkboxValue.includes('__other__')"
						:modelValue="checkboxOtherText"
						placeholder="Please specify"
						class="other-input"
						:disabled="readonly"
						@update:modelValue="updateCheckboxOtherText" />
				</div>
			</div>

			<DatePicker
				v-else-if="safeField.type === 'date'"
				:modelValue="modelValue"
				class="w-full"
				showIcon
				dateFormat="yy-mm-dd"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<InputText v-else-if="safeField.type === 'time'" :id="inputId" :modelValue="modelValue" type="time" class="w-full" :disabled="readonly" @update:modelValue="emit('update:modelValue', $event)" />

			<RatingInput
				v-else-if="safeField.type === 'rating'"
				:modelValue="Number(modelValue || 0)"
				:max="safeField.validation_rules?.max || 5"
				:readonly="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<div v-else-if="safeField.type === 'linear_scale'" class="linear-scale-wrapper">
				<div class="linear-scale-labels">
					<span>{{ safeField.field_settings?.start_label || '' }}</span>
					<span>{{ safeField.field_settings?.end_label || '' }}</span>
				</div>

				<div class="linear-scale-options">
					<div v-for="value in linearScaleOptions" :key="value" class="linear-scale-item">
						<label :for="`${inputId}-scale-${value}`">{{ value }}</label>
						<RadioButton :inputId="`${inputId}-scale-${value}`" :modelValue="modelValue" :value="value" :disabled="readonly" @update:modelValue="emit('update:modelValue', $event)" />
					</div>
				</div>
			</div>

			<div v-else-if="safeField.type === 'multiple_choice_grid'" class="grid-wrapper">
				<table class="grid-table">
					<thead>
						<tr>
							<th></th>
							<th v-for="column in gridColumns" :key="column.value">
								{{ column.label }}
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="row in gridRows" :key="row.value">
							<td class="grid-row-label">{{ row.label }}</td>
							<td v-for="column in gridColumns" :key="`${row.value}-${column.value}`">
								<RadioButton
									:inputId="`${inputId}-${row.value}-${column.value}`"
									:modelValue="modelValue?.[row.value] ?? null"
									:value="column.value"
									:disabled="readonly"
									@update:modelValue="updateMultipleChoiceGrid(row.value, $event)" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div v-else-if="safeField.type === 'checkbox_grid'" class="grid-wrapper">
				<table class="grid-table">
					<thead>
						<tr>
							<th></th>
							<th v-for="column in gridColumns" :key="column.value">
								{{ column.label }}
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="row in gridRows" :key="row.value">
							<td class="grid-row-label">{{ row.label }}</td>
							<td v-for="column in gridColumns" :key="`${row.value}-${column.value}`">
								<Checkbox
									:inputId="`${inputId}-${row.value}-${column.value}`"
									:binary="true"
									:modelValue="Array.isArray(modelValue?.[row.value]) ? modelValue[row.value].includes(column.value) : false"
									:disabled="readonly"
									@update:modelValue="(checked) => updateCheckboxGrid(row.value, column.value, checked)" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div v-else-if="safeField.type === 'file'" class="file-upload-wrapper">
				<div
					class="upload-card"
					:class="{
						'upload-card-disabled': readonly,
						'upload-card-dragging': isDraggingFile,
						'upload-card-has-file': !!modelValue?.name,
					}"
					@dragover="handleFileDragOver"
					@dragleave="handleFileDragLeave"
					@drop="handleFileDrop">
					<input :id="inputId" ref="fileInputRef" type="file" accept=".pdf,application/pdf" :disabled="readonly" class="hidden-file-input" @change="handleFileSelect" />

					<div class="upload-card-icon">
						<i :class="modelValue?.name ? 'pi pi-file-pdf' : 'pi pi-cloud-upload'" />
					</div>

					<div class="upload-card-content">
						<div class="upload-card-title">
							{{ modelValue?.name ? 'PDF file selected' : 'Upload PDF file' }}
						</div>

						<div class="upload-card-subtitle">
							{{ modelValue?.name ? 'You can replace this file by choosing another PDF or dragging one here.' : 'Drag and drop your PDF here or choose a file from your device.' }}
						</div>

						<div class="upload-rule-badges">
							<Tag value="PDF only" severity="contrast" rounded />
							<Tag :value="`Max ${maxFileSizeKb} KB`" severity="secondary" rounded />
						</div>
					</div>

					<div class="upload-card-action">
						<Button type="button" :label="modelValue?.name ? 'Replace File' : 'Choose File'" icon="pi pi-upload" severity="secondary" outlined :disabled="readonly" @click="openFilePicker" />
					</div>
				</div>

				<div v-if="modelValue?.name" class="selected-file-card">
					<div class="selected-file-left">
						<div class="selected-file-icon">
							<i class="pi pi-file-pdf" />
						</div>

						<div class="selected-file-meta">
							<div class="selected-file-name">{{ modelValue.name }}</div>
							<div class="selected-file-size">{{ (modelValue.size / 1024).toFixed(2) }} KB</div>
						</div>
					</div>

					<div class="selected-file-actions">
						<Button type="button" icon="pi pi-times" text rounded severity="danger" :disabled="readonly" @click="clearSelectedFile" />
					</div>
				</div>

				<div class="file-upload-note">Please upload a PDF document only. Maximum allowed size is {{ maxFileSizeKb }} KB.</div>
			</div>

			<div v-else class="unsupported-field">Unsupported field type: {{ safeField.type }}</div>
		</div>

		<Message v-if="effectiveError" severity="error" size="small" variant="simple">
			{{ effectiveError }}
		</Message>
	</div>
</template>

<style scoped>
	.dynamic-field {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
		padding: 0.25rem 0;
	}

	.field-header {
		display: flex;
		flex-direction: column;
		gap: 0.25rem;
	}

	.field-label {
		font-size: 1rem;
		font-weight: 600;
		color: var(--text-color);
	}

	.required-mark {
		color: #dc2626;
		margin-left: 0.25rem;
	}

	.field-description {
		margin: 0;
		font-size: 0.875rem;
		color: var(--text-color-secondary);
	}

	.choice-group {
		display: flex;
		flex-direction: column;
		gap: 0.875rem;
		padding-top: 0.25rem;
	}

	.choice-item {
		display: flex;
		align-items: center;
		gap: 0.75rem;
	}

	.other-option-block {
		flex-wrap: wrap;
	}

	.other-input {
		margin-left: 2rem;
		min-width: 260px;
		flex: 1;
	}

	.linear-scale-wrapper {
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}

	.linear-scale-labels {
		display: flex;
		justify-content: space-between;
		font-size: 0.875rem;
		color: var(--text-color-secondary);
	}

	.linear-scale-options {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(48px, 1fr));
		gap: 0.75rem;
	}

	.linear-scale-item {
		display: flex;
		flex-direction: column;
		align-items: center;
		gap: 0.5rem;
	}

	.grid-wrapper {
		overflow-x: auto;
	}

	.grid-table {
		width: 100%;
		border-collapse: collapse;
	}

	.grid-table th,
	.grid-table td {
		padding: 0.75rem;
		border: 1px solid var(--surface-border);
		text-align: center;
		min-width: 90px;
	}

	.grid-row-label {
		text-align: left;
		font-weight: 600;
		min-width: 180px;
	}

	.file-upload-wrapper {
		display: flex;
		flex-direction: column;
		gap: 0.85rem;
	}

	.hidden-file-input {
		display: none;
	}

	.upload-card {
		display: grid;
		grid-template-columns: auto 1fr auto;
		align-items: center;
		gap: 1rem;
		padding: 1rem 1.1rem;
		border: 1px dashed var(--surface-border);
		border-radius: 18px;
		background: linear-gradient(180deg, var(--surface-card), var(--surface-50));
		transition: all 0.2s ease;
	}

	.upload-card:hover {
		border-color: var(--primary-color);
		box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
		transform: translateY(-1px);
	}

	.upload-card-dragging {
		border-color: var(--primary-color);
		background: linear-gradient(180deg, var(--surface-50), var(--surface-100));
		box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color) 18%, transparent);
	}

	.upload-card-has-file {
		border-style: solid;
	}

	.upload-card-disabled {
		opacity: 0.7;
		cursor: not-allowed;
	}

	.upload-card-icon {
		width: 52px;
		height: 52px;
		border-radius: 16px;
		display: flex;
		align-items: center;
		justify-content: center;
		background: var(--surface-100);
		color: var(--primary-color);
		font-size: 1.4rem;
	}

	.upload-card-content {
		display: flex;
		flex-direction: column;
		gap: 0.45rem;
		min-width: 0;
	}

	.upload-card-title {
		font-weight: 700;
		color: var(--text-color);
	}

	.upload-card-subtitle {
		font-size: 0.875rem;
		color: var(--text-color-secondary);
		line-height: 1.4;
	}

	.upload-rule-badges {
		display: flex;
		flex-wrap: wrap;
		gap: 0.5rem;
		margin-top: 0.1rem;
	}

	.upload-card-action {
		display: flex;
		align-items: center;
		justify-content: flex-end;
	}

	.selected-file-card {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		padding: 0.9rem 1rem;
		border: 1px solid var(--surface-border);
		border-radius: 16px;
		background: var(--surface-50);
	}

	.selected-file-left {
		display: flex;
		align-items: center;
		gap: 0.85rem;
		min-width: 0;
	}

	.selected-file-icon {
		width: 44px;
		height: 44px;
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		background: #fee2e2;
		color: #dc2626;
		font-size: 1.1rem;
		flex-shrink: 0;
	}

	.selected-file-meta {
		min-width: 0;
	}

	.selected-file-name {
		font-weight: 600;
		color: var(--text-color);
		word-break: break-word;
	}

	.selected-file-size {
		font-size: 0.825rem;
		color: var(--text-color-secondary);
		margin-top: 0.2rem;
	}

	.selected-file-actions {
		flex-shrink: 0;
	}

	.file-upload-note {
		font-size: 0.875rem;
		color: var(--text-color-secondary);
		padding-left: 0.15rem;
	}

	.unsupported-field {
		min-height: 3rem;
		display: flex;
		align-items: center;
		gap: 0.75rem;
		padding: 1rem;
		border: 1px dashed var(--surface-border);
		border-radius: 12px;
		color: var(--text-color-secondary);
		background: var(--surface-50);
	}

	@media (max-width: 640px) {
		.upload-card {
			grid-template-columns: 1fr;
			text-align: center;
		}

		.upload-card-icon {
			margin: 0 auto;
		}

		.upload-card-action {
			justify-content: center;
		}

		.selected-file-card {
			flex-direction: column;
			align-items: stretch;
		}

		.selected-file-actions {
			display: flex;
			justify-content: flex-end;
		}
	}
</style>
