<script setup>
import { computed } from 'vue'

const props = defineProps({
	modelValue: {
		type: Number,
		default: 0
	},
	max: {
		type: Number,
		default: 5
	},
	readonly: {
		type: Boolean,
		default: false
	},
	showValue: {
		type: Boolean,
		default: true
	}
})

const emit = defineEmits(['update:modelValue'])

const stars = computed(() => Array.from({ length: props.max }, (_, i) => i + 1))

const setRating = (value) => {
	if (props.readonly) return
	emit('update:modelValue', value)
}

const clearRating = () => {
	if (props.readonly) return
	emit('update:modelValue', 0)
}
</script>

<template>
	<div class="rating-input">
		<div class="rating-stars">
			<Button
				v-for="star in stars"
				:key="star"
				type="button"
				text
				rounded
				:disabled="readonly"
				class="rating-star-btn"
				@click="setRating(star)"
			>
				<i
					class="pi text-2xl"
					:class="star <= modelValue ? 'pi-star-fill active-star' : 'pi-star empty-star'"
				/>
			</Button>
		</div>

		<div v-if="showValue" class="rating-meta">
			<span class="rating-value">
				{{ modelValue ? `${modelValue} / ${max}` : `0 / ${max}` }}
			</span>

			<Button
				v-if="!readonly && modelValue"
				type="button"
				label="Clear"
				text
				size="small"
				class="p-0"
				@click="clearRating"
			/>
		</div>
	</div>
</template>

<style scoped>
.rating-input {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.rating-stars {
	display: flex;
	align-items: center;
	gap: 0.25rem;
	flex-wrap: wrap;
}

.rating-star-btn {
	width: 2.5rem;
	height: 2.5rem;
}

.active-star {
	color: #f59e0b;
}

.empty-star {
	color: #cbd5e1;
}

.rating-meta {
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.rating-value {
	font-size: 0.875rem;
	color: var(--text-color-secondary);
}
</style>
