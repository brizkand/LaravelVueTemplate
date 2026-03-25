<script setup>
	import {computed} from 'vue'
	import {useRoute, useRouter} from 'vue-router'

	const route = useRoute()
	const router = useRouter()

	const formId = computed(() => route.params.id)
	const referenceCode = computed(() => route.query.reference_code || null)
	const submittedAt = computed(() => route.query.submitted_at || null)

	const formattedSubmittedAt = computed(() => {
		if (!submittedAt.value) return null

		const date = new Date(submittedAt.value)
		if (Number.isNaN(date.getTime())) return submittedAt.value

		return new Intl.DateTimeFormat('en-PH', {
			year: 'numeric',
			month: 'long',
			day: 'numeric',
			hour: 'numeric',
			minute: '2-digit',
		}).format(date)
	})

	const submitAnotherResponse = () => {
		router.push({
			name: 'forms.respond',
			params: {id: formId.value},
		})
	}
</script>

<template>
	<div class="thank-you-page">
		<div class="thank-you-card">
			<div class="thank-you-icon">
				<i class="pi pi-check-circle" />
			</div>

			<h1>Thank You!</h1>
			<p class="thank-you-message">Your response has been submitted successfully.</p>

			<div v-if="referenceCode || formattedSubmittedAt" class="submission-details">
				<div v-if="referenceCode" class="detail-row">
					<span class="detail-label">Reference Code</span>
					<span class="detail-value">{{ referenceCode }}</span>
				</div>

				<div v-if="formattedSubmittedAt" class="detail-row">
					<span class="detail-label">Submitted At</span>
					<span class="detail-value">{{ formattedSubmittedAt }}</span>
				</div>
			</div>

			<div class="thank-you-actions">
				<Button label="Submit Another Response" icon="pi pi-refresh" @click="submitAnotherResponse" />
			</div>
		</div>
	</div>
</template>

<style scoped>
	.thank-you-page {
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 2rem 1rem;
		background: linear-gradient(180deg, var(--surface-ground), var(--surface-50));
	}

	.thank-you-card {
		width: 100%;
		max-width: 640px;
		background: var(--surface-card);
		border: 1px solid var(--surface-border);
		border-radius: 24px;
		padding: 2.5rem 2rem;
		text-align: center;
		box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
	}

	.thank-you-icon {
		width: 84px;
		height: 84px;
		border-radius: 999px;
		margin: 0 auto 1.5rem;
		display: flex;
		align-items: center;
		justify-content: center;
		background: #dcfce7;
		color: #16a34a;
		font-size: 2.5rem;
	}

	.thank-you-card h1 {
		margin: 0;
		font-size: 2rem;
		font-weight: 700;
		color: var(--text-color);
	}

	.thank-you-message {
		margin: 0.75rem auto 0;
		max-width: 480px;
		color: var(--text-color-secondary);
		line-height: 1.7;
	}

	.submission-details {
		margin-top: 2rem;
		padding: 1.25rem;
		border: 1px solid var(--surface-border);
		border-radius: 16px;
		background: var(--surface-50);
		display: flex;
		flex-direction: column;
		gap: 1rem;
		text-align: left;
	}

	.detail-row {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 1rem;
		flex-wrap: wrap;
	}

	.detail-label {
		font-size: 0.9rem;
		font-weight: 600;
		color: var(--text-color-secondary);
	}

	.detail-value {
		font-size: 0.95rem;
		font-weight: 600;
		color: var(--text-color);
		word-break: break-word;
	}

	.thank-you-actions {
		margin-top: 2rem;
		display: flex;
		justify-content: center;
	}

	@media (max-width: 640px) {
		.thank-you-card {
			padding: 2rem 1.25rem;
		}
	}
</style>
