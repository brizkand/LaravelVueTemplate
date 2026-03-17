<script setup>
	import {computed} from 'vue'

	const props = defineProps({
		field: {
			type: Object,
			required: true,
		},
		index: {
			type: Number,
			default: 0,
		},
	})

	const emit = defineEmits(['edit', 'duplicate', 'delete'])

	const typeLabelMap = {
		short_text: 'Short Text',
		long_text: 'Long Text',
		number: 'Number',
		email: 'Email',
		dropdown: 'Dropdown',
		radio: 'Multiple Choice',
		checkbox: 'Checkboxes',
		date: 'Date',
		rating: 'Rating',
		file: 'File Upload',
	}

	const typeLabel = computed(() => typeLabelMap[props.field.type] || props.field.type)

	const previewValue = computed(() => {
		switch (props.field.type) {
			case 'short_text':
				return props.field.placeholder || 'Short answer text'
			case 'long_text':
				return props.field.placeholder || 'Long answer text'
			case 'number':
				return 'Number input'
			case 'email':
				return 'name@example.com'
			case 'dropdown':
				return `Dropdown (${props.field.options?.length || 0} options)`
			case 'radio':
				return `Multiple choice (${props.field.options?.length || 0} options)`
			case 'checkbox':
				return `Checkboxes (${props.field.options?.length || 0} options)`
			case 'date':
				return 'Date picker'
			case 'rating':
				return '1 to 5 rating'
			case 'file':
				return 'File upload'
			default:
				return 'Question preview'
		}
	})
</script>

<template>
	<div class="field-card">
		<div class="field-card-left-accent" />

		<div class="field-card-main">
			<div class="field-card-top">
				<div class="field-card-title-wrap">
					<div class="drag-handle" title="Drag to reorder">
						<i class="pi pi-bars" />
					</div>

					<div class="field-card-content">
						<div class="field-card-heading-row">
							<h3 class="field-label">{{ field.label || 'Untitled Question' }}</h3>

							<Tag :value="typeLabel" severity="secondary" rounded />

							<Tag v-if="field.is_required" value="Required" severity="danger" rounded />

							<Tag v-if="field.is_active === false" value="Inactive" severity="contrast" rounded />
						</div>

						<p v-if="field.description" class="field-description">
							{{ field.description }}
						</p>
					</div>
				</div>

				<div class="field-actions">
					<Button type="button" icon="pi pi-pencil" text rounded severity="secondary" v-tooltip.top="'Edit'" @click="emit('edit', field)" />
					<Button type="button" icon="pi pi-copy" text rounded severity="secondary" v-tooltip.top="'Duplicate'" @click="emit('duplicate', field)" />
					<Button type="button" icon="pi pi-trash" text rounded severity="danger" v-tooltip.top="'Delete'" @click="emit('delete', field)" />
				</div>
			</div>

			<Divider />

			<div class="field-preview">
				<div class="field-preview-box">
					<span class="field-preview-text">{{ previewValue }}</span>
				</div>

				<div v-if="['radio', 'checkbox', 'dropdown'].includes(field.type) && field.options?.length" class="options-preview">
					<div v-for="(option, optionIndex) in field.options" :key="option.id || optionIndex" class="option-chip">
						{{ option.label }}
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.field-card {
		position: relative;
		display: flex;
		background: var(--surface-card);
		border: 1px solid var(--surface-border);
		border-radius: 18px;
		overflow: hidden;
		box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
		transition:
			box-shadow 0.2s ease,
			transform 0.2s ease;
	}

	.field-card:hover {
		transform: translateY(-1px);
		box-shadow: 0 8px 24px rgba(0, 0, 0, 0.07);
	}

	.field-card-left-accent {
		width: 6px;
		background: linear-gradient(180deg, #7e57c2, #673ab7);
	}

	.field-card-main {
		flex: 1;
		padding: 1rem 1.25rem;
	}

	.field-card-top {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 1rem;
	}

	.field-card-title-wrap {
		display: flex;
		align-items: flex-start;
		gap: 1rem;
		flex: 1;
		min-width: 0;
	}

	.drag-handle {
		width: 2.25rem;
		height: 2.25rem;
		border-radius: 10px;
		display: flex;
		align-items: center;
		justify-content: center;
		background: var(--surface-100);
		color: var(--text-color-secondary);
		cursor: grab;
		flex-shrink: 0;
	}

	.field-card-content {
		flex: 1;
		min-width: 0;
	}

	.field-card-heading-row {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		flex-wrap: wrap;
	}

	.field-label {
		margin: 0;
		font-size: 1rem;
		font-weight: 700;
		color: var(--text-color);
	}

	.field-description {
		margin-top: 0.5rem;
		margin-bottom: 0;
		color: var(--text-color-secondary);
		line-height: 1.5;
	}

	.field-actions {
		display: flex;
		align-items: center;
		flex-shrink: 0;
	}

	.field-preview {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}

	.field-preview-box {
		min-height: 3rem;
		display: flex;
		align-items: center;
		padding: 0.875rem 1rem;
		background: var(--surface-50);
		border: 1px dashed var(--surface-border);
		border-radius: 12px;
	}

	.field-preview-text {
		color: var(--text-color-secondary);
	}

	.options-preview {
		display: flex;
		flex-wrap: wrap;
		gap: 0.5rem;
	}

	.option-chip {
		background: var(--surface-100);
		color: var(--text-color);
		border-radius: 999px;
		padding: 0.4rem 0.8rem;
		font-size: 0.875rem;
	}

	@media (max-width: 768px) {
		.field-card-top {
			flex-direction: column;
		}

		.field-actions {
			align-self: flex-end;
		}
	}
</style>
