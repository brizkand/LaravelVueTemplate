<script setup>
	import {computed} from 'vue'
	import RatingInput from './RatingInput.vue'

	const props = defineProps({
		field: {
			type: Object,
			required: true,
		},
		modelValue: {
			type: [String, Number, Array, Object, Date, null],
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
		if (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue)) {
			return props.modelValue.selected ?? null
		}

		return props.modelValue
	})

	const radioOtherText = computed(() => {
		if (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue)) {
			return props.modelValue.other_text ?? ''
		}

		return ''
	})

	const checkboxValue = computed(() => {
		if (Array.isArray(props.modelValue)) return props.modelValue

		if (props.modelValue && typeof props.modelValue === 'object' && Array.isArray(props.modelValue.selected)) {
			return props.modelValue.selected
		}

		return []
	})

	const checkboxOtherText = computed(() => {
		if (props.modelValue && typeof props.modelValue === 'object' && !Array.isArray(props.modelValue)) {
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
		const currentModel = props.modelValue && typeof props.modelValue === 'object' ? {...props.modelValue} : {}

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
</script>

<template>
	<div v-if="safeField" class="dynamic-field" :class="{'has-error': error}">
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
			<!-- Short Text -->
			<InputText
				v-if="safeField.type === 'short_text'"
				:id="inputId"
				:modelValue="modelValue"
				:placeholder="safeField.placeholder || 'Your answer'"
				class="w-full"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<!-- Long Text -->
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

			<!-- Number -->
			<InputNumber
				v-else-if="safeField.type === 'number'"
				:inputId="inputId"
				:modelValue="modelValue"
				class="w-full"
				:placeholder="safeField.placeholder || 'Enter a number'"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<!-- Email -->
			<InputText
				v-else-if="safeField.type === 'email'"
				:id="inputId"
				:modelValue="modelValue"
				type="email"
				class="w-full"
				:placeholder="safeField.placeholder || 'Enter your email'"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<!-- Dropdown -->
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

			<!-- Multiple Choice -->
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

			<!-- Checkbox -->
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

			<!-- Date -->
			<DatePicker
				v-else-if="safeField.type === 'date'"
				:modelValue="modelValue"
				class="w-full"
				showIcon
				dateFormat="yy-mm-dd"
				:disabled="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<!-- Time -->
			<InputText v-else-if="safeField.type === 'time'" :id="inputId" :modelValue="modelValue" type="time" class="w-full" :disabled="readonly" @update:modelValue="emit('update:modelValue', $event)" />

			<!-- Rating -->
			<RatingInput
				v-else-if="safeField.type === 'rating'"
				:modelValue="Number(modelValue || 0)"
				:max="safeField.validation_rules?.max || 5"
				:readonly="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<!-- Linear Scale -->
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

			<!-- Multiple Choice Grid -->
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

			<!-- Checkbox Grid -->
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

			<!-- File Upload -->
			<div v-else-if="safeField.type === 'file'" class="file-placeholder">
				<i class="pi pi-upload text-xl" />
				<span>File upload input can be implemented next.</span>
			</div>

			<!-- Unsupported -->
			<div v-else class="unsupported-field">Unsupported field type: {{ safeField.type }}</div>
		</div>

		<Message v-if="error" severity="error" size="small" variant="simple">
			{{ error }}
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

	.file-placeholder,
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
</style>
