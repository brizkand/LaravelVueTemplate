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

	const checkboxValue = computed(() => {
		if (Array.isArray(props.modelValue)) return props.modelValue
		if (props.modelValue && Array.isArray(props.modelValue.selected)) return props.modelValue.selected
		return []
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
				other_text: props.modelValue?.other_text || '',
			})
		} else {
			emit('update:modelValue', current)
		}
	}
	const inputId = computed(() => {
		if (!safeField.value?.id) return `field-temp-${Math.random().toString(36).slice(2, 9)}`
		return `field-${safeField.value.id}`
	})
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
					<RadioButton :inputId="`${inputId}-radio-${option.id}`" :modelValue="modelValue" :value="option.value" :disabled="readonly" @update:modelValue="emit('update:modelValue', $event)" />
					<label :for="`${inputId}-radio-${option.id}`">
						{{ option.label }}
					</label>
				</div>

				<div v-if="safeField.allow_other_option" class="choice-item other-option-block">
					<RadioButton
						:inputId="`${inputId}-radio-other`"
						:modelValue="typeof modelValue === 'object' ? modelValue?.selected : modelValue"
						value="__other__"
						:disabled="readonly"
						@update:modelValue="
							emit('update:modelValue', {
								selected: '__other__',
								other_text: typeof modelValue === 'object' ? modelValue?.other_text || '' : '',
							})
						" />
					<label :for="`${inputId}-radio-other`">
						{{ safeField.other_option_label || 'Other' }}
					</label>
					<InputText
						v-if="(typeof modelValue === 'object' ? modelValue?.selected : modelValue) === '__other__'"
						:modelValue="typeof modelValue === 'object' ? modelValue?.other_text || '' : ''"
						placeholder="Please specify"
						class="other-input"
						:disabled="readonly"
						@update:modelValue="
							emit('update:modelValue', {
								selected: '__other__',
								other_text: $event,
							})
						" />
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
						:modelValue="typeof modelValue === 'object' ? modelValue?.other_text || '' : ''"
						placeholder="Please specify"
						class="other-input"
						:disabled="readonly"
						@update:modelValue="
							emit('update:modelValue', {
								selected: Array.isArray(modelValue?.selected) ? modelValue.selected : checkboxValue,
								other_text: $event,
							})
						" />
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

			<RatingInput
				v-else-if="safeField.type === 'rating'"
				:modelValue="Number(modelValue || 0)"
				:max="safeField.validation_rules?.max || 5"
				:readonly="readonly"
				@update:modelValue="emit('update:modelValue', $event)" />

			<div v-else-if="safeField.type === 'file'" class="file-placeholder">
				<i class="pi pi-upload text-xl" />
				<span>File upload preview placeholder.</span>
			</div>

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

	.other-option-block {
		flex-wrap: wrap;
	}

	.other-input {
		margin-left: 2rem;
		min-width: 260px;
		flex: 1;
	}
</style>
