<script setup>
	import {computed, onMounted, ref} from 'vue'
	import {useConfirm} from 'primevue/useconfirm'
	import {useToast} from 'primevue/usetoast'
	import {useRouter} from 'vue-router'
	import {useFormStore} from '@/stores/form'

	const router = useRouter()
	const confirm = useConfirm()
	const toast = useToast()
	const formStore = useFormStore()

	const loading = ref(false)
	const globalFilter = ref('')
	const statusFilter = ref('all')

	const statusOptions = [
		{label: 'All', value: 'all'},
		{label: 'Active', value: 'active'},
		{label: 'Inactive', value: 'inactive'},
	]

	onMounted(async () => {
		try {
			await formStore.fetchForms()
		} catch (error) {
			const message = error?.response?.data?.message || 'Failed to load forms.'

			toast.add({
				severity: 'error',
				summary: 'Load Failed',
				detail: message,
				life: 4000,
			})

			if (error?.response?.status === 401) {
				router.push({name: 'auth.login'})
			}
		}
	})

	const forms = computed(() => formStore.forms)

	const filteredForms = computed(() => {
		const keyword = globalFilter.value.trim().toLowerCase()

		return forms.value.filter((form) => {
			const matchesKeyword =
				!keyword || form.title.toLowerCase().includes(keyword) || (form.description || '').toLowerCase().includes(keyword) || (form.created_by?.name || '').toLowerCase().includes(keyword)

			const matchesStatus = statusFilter.value === 'all' || (statusFilter.value === 'active' && form.is_active) || (statusFilter.value === 'inactive' && !form.is_active)

			return matchesKeyword && matchesStatus
		})
	})

	const totalForms = computed(() => forms.value.length)
	const totalActive = computed(() => forms.value.filter((form) => form.is_active).length)
	const totalResponses = computed(() => forms.value.reduce((sum, form) => sum + (form.responses_count || 0), 0))

	const formatDate = (value) => {
		if (!value) return '-'

		const date = new Date(value.replace(' ', 'T'))
		if (Number.isNaN(date.getTime())) return value

		return new Intl.DateTimeFormat('en-PH', {
			year: 'numeric',
			month: 'short',
			day: 'numeric',
			hour: 'numeric',
			minute: '2-digit',
		}).format(date)
	}

	const getStatusSeverity = (isActive) => (isActive ? 'success' : 'contrast')
	const getVisibilitySeverity = (isPublic) => (isPublic ? 'info' : 'warn')

	const goToCreate = () => {
		router.push({name: 'forms.create'})
	}

	const goToBuilder = (form) => {
		router.push({
			name: 'forms.builder',
			params: {id: form.id},
		})
	}

	const goToAnalytics = (form) => {
		router.push({
			name: 'forms.analytics',
			params: {id: form.id},
		})
	}

	const goToResponses = (form) => {
		router.push({
			name: 'forms.responses',
			params: {id: form.id},
		})
	}

	const duplicateForm = (form) => {
		formStore.duplicateForm(form.id)

		toast.add({
			severity: 'success',
			summary: 'Form Duplicated',
			detail: `"${form.title}" was duplicated successfully.`,
			life: 3000,
		})
	}

	const toggleStatus = (form) => {
		formStore.updateForm(form.id, {
			...form,
			is_active: !form.is_active,
		})

		toast.add({
			severity: 'success',
			summary: 'Status Updated',
			detail: `"${form.title}" is now ${!form.is_active ? 'Active' : 'Inactive'}.`,
			life: 3000,
		})
	}

	// const deleteForm = (form) => {
	// 	confirm.require({
	// 		message: `Are you sure you want to delete "${form.title}"?`,
	// 		header: 'Delete Form',
	// 		icon: 'pi pi-exclamation-triangle',
	// 		rejectProps: {
	// 			label: 'Cancel',
	// 			severity: 'secondary',
	// 			outlined: true,
	// 		},
	// 		acceptProps: {
	// 			label: 'Delete',
	// 			severity: 'danger',
	// 		},
	// 		accept: () => {
	// 			formStore.deleteForm(form.id)

	// 			toast.add({
	// 				severity: 'success',
	// 				summary: 'Form Deleted',
	// 				detail: `"${form.title}" was deleted successfully.`,
	// 				life: 3000,
	// 			})
	// 		},
	// 	})
	// }

	const deleteForm = (form) => {
		confirm.require({
			message: `Are you sure you want to delete "${form.title}"?`,
			header: 'Delete Form',
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
			accept: async () => {
				try {
					await formStore.removeForm(form.id)

					toast.add({
						severity: 'success',
						summary: 'Form Deleted',
						detail: `"${form.title}" was deleted successfully.`,
						life: 3000,
					})
				} catch {
					toast.add({
						severity: 'error',
						summary: 'Delete Failed',
						detail: 'Unable to delete form.',
						life: 3000,
					})
				}
			},
		})
	}
</script>

<template>
	<div class="form-list-page">
		<Toast />
		<ConfirmDialog />

		<div class="page-header">
			<div>
				<h1>Forms</h1>
				<p>Manage dynamic feedback forms for CRMIS.</p>
			</div>

			<div class="page-header-actions">
				<Button label="Create Form" icon="pi pi-plus" @click="goToCreate" />
			</div>
		</div>

		<div class="stats-grid">
			<div class="stats-card">
				<div class="stats-icon purple">
					<i class="pi pi-file-edit" />
				</div>
				<div>
					<div class="stats-value">{{ totalForms }}</div>
					<div class="stats-label">Total Forms</div>
				</div>
			</div>

			<div class="stats-card">
				<div class="stats-icon green">
					<i class="pi pi-check-circle" />
				</div>
				<div>
					<div class="stats-value">{{ totalActive }}</div>
					<div class="stats-label">Active Forms</div>
				</div>
			</div>

			<div class="stats-card">
				<div class="stats-icon blue">
					<i class="pi pi-chart-bar" />
				</div>
				<div>
					<div class="stats-value">{{ totalResponses }}</div>
					<div class="stats-label">Total Responses</div>
				</div>
			</div>
		</div>

		<div class="filters-card">
			<div class="filters-row">
				<div class="filters-left">
					<IconField class="w-full search-field">
						<InputIcon class="pi pi-search" />
						<InputText v-model="globalFilter" placeholder="Search forms..." class="w-full" />
					</IconField>

					<Select v-model="statusFilter" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Filter by status" class="status-filter" />
				</div>

				<div class="filters-right">
					<Button label="Refresh" icon="pi pi-refresh" severity="secondary" outlined :loading="loading" />
				</div>
			</div>
		</div>

		<div class="table-card">
			<DataTable
				:value="filteredForms"
				dataKey="id"
				paginator
				:rows="10"
				:rowsPerPageOptions="[10, 20, 50]"
				responsiveLayout="scroll"
				stripedRows
				showGridlines
				class="forms-table"
				emptyMessage="No forms found.">
				<Column field="title" header="Form" style="min-width: 22rem">
					<template #body="{data}">
						<div class="form-title-cell">
							<div class="form-title-main">
								<h3>{{ data.title }}</h3>
								<p>{{ data.description || 'No description provided.' }}</p>
							</div>

							<div class="form-title-tags">
								<Tag :value="data.is_active ? 'Active' : 'Inactive'" :severity="getStatusSeverity(data.is_active)" rounded />
								<Tag :value="data.is_public ? 'Public' : 'Private'" :severity="getVisibilitySeverity(data.is_public)" rounded />
							</div>
						</div>
					</template>
				</Column>

				<Column header="Questions" style="width: 9rem">
					<template #body="{data}">
						<div class="metric-badge">
							<i class="pi pi-list" />
							<span>{{ data.fields_count || 0 }}</span>
						</div>
					</template>
				</Column>

				<Column header="Responses" style="width: 9rem">
					<template #body="{data}">
						<div class="metric-badge response">
							<i class="pi pi-chart-line" />
							<span>{{ data.responses_count || 0 }}</span>
						</div>
					</template>
				</Column>

				<Column header="Created By" style="min-width: 12rem">
					<template #body="{data}">
						<div class="created-by-cell">
							<Avatar :label="data.created_by?.name?.charAt(0) || 'U'" shape="circle" />
							<span>{{ data.created_by?.name || '-' }}</span>
						</div>
					</template>
				</Column>

				<Column header="Created Date" style="min-width: 14rem">
					<template #body="{data}">
						<span>{{ formatDate(data.created_at) }}</span>
					</template>
				</Column>

				<Column header="Actions" style="min-width: 20rem">
					<template #body="{data}">
						<div class="action-buttons">
							<Button icon="pi pi-pencil" text rounded severity="secondary" v-tooltip.top="'Open Builder'" @click="goToBuilder(data)" />
							<Button icon="pi pi-chart-pie" text rounded severity="info" v-tooltip.top="'Analytics'" @click="goToAnalytics(data)" />
							<Button icon="pi pi-table" text rounded severity="help" v-tooltip.top="'Responses'" @click="goToResponses(data)" />
							<Button icon="pi pi-copy" text rounded severity="secondary" v-tooltip.top="'Duplicate'" @click="duplicateForm(data)" />
							<Button :icon="data.is_active ? 'pi pi-eye-slash' : 'pi pi-eye'" text rounded severity="warn" v-tooltip.top="data.is_active ? 'Deactivate' : 'Activate'" @click="toggleStatus(data)" />
							<Button icon="pi pi-trash" text rounded severity="danger" v-tooltip.top="'Delete'" @click="deleteForm(data)" />
						</div>
					</template>
				</Column>
			</DataTable>
		</div>
	</div>
</template>

<style scoped>
	.form-list-page {
		display: flex;
		flex-direction: column;
		gap: 1.25rem;
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

	.stats-grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 1rem;
	}

	.stats-card {
		display: flex;
		align-items: center;
		gap: 1rem;
		padding: 1.25rem;
		border-radius: 18px;
		background: var(--surface-card);
		border: 1px solid var(--surface-border);
		box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
	}

	.stats-icon {
		width: 3rem;
		height: 3rem;
		border-radius: 14px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.25rem;
		color: white;
		flex-shrink: 0;
	}

	.stats-icon.purple {
		background: linear-gradient(135deg, #7e57c2, #673ab7);
	}

	.stats-icon.green {
		background: linear-gradient(135deg, #22c55e, #16a34a);
	}

	.stats-icon.blue {
		background: linear-gradient(135deg, #3b82f6, #2563eb);
	}

	.stats-value {
		font-size: 1.5rem;
		font-weight: 700;
		color: var(--text-color);
	}

	.stats-label {
		color: var(--text-color-secondary);
		font-size: 0.875rem;
		margin-top: 0.15rem;
	}

	.filters-card,
	.table-card {
		background: var(--surface-card);
		border: 1px solid var(--surface-border);
		border-radius: 18px;
		padding: 1rem;
		box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
	}

	.filters-row {
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 1rem;
		flex-wrap: wrap;
	}

	.filters-left {
		display: flex;
		align-items: center;
		gap: 0.75rem;
		flex: 1;
		flex-wrap: wrap;
	}

	.search-field {
		min-width: 280px;
		flex: 1;
	}

	.status-filter {
		width: 200px;
	}

	.form-title-cell {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}

	.form-title-main h3 {
		margin: 0;
		font-size: 1rem;
		font-weight: 700;
		color: var(--text-color);
	}

	.form-title-main p {
		margin: 0.35rem 0 0;
		color: var(--text-color-secondary);
		line-height: 1.5;
	}

	.form-title-tags {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		flex-wrap: wrap;
	}

	.metric-badge {
		display: inline-flex;
		align-items: center;
		gap: 0.5rem;
		padding: 0.55rem 0.85rem;
		border-radius: 999px;
		background: var(--surface-100);
		color: var(--text-color);
		font-weight: 600;
	}

	.metric-badge.response {
		background: #eff6ff;
		color: #1d4ed8;
	}

	.created-by-cell {
		display: flex;
		align-items: center;
		gap: 0.75rem;
	}

	.action-buttons {
		display: flex;
		align-items: center;
		flex-wrap: wrap;
		gap: 0.25rem;
	}

	:deep(.forms-table .p-datatable-thead > tr > th) {
		font-weight: 700;
	}

	@media (max-width: 992px) {
		.stats-grid {
			grid-template-columns: 1fr;
		}
	}

	@media (max-width: 768px) {
		.status-filter {
			width: 100%;
		}

		.search-field {
			min-width: 100%;
		}
	}
</style>
