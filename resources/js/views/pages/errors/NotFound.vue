<script setup>
	import {useRouter} from 'vue-router'

	const router = useRouter()

	const goDashboard = () => {
		router.push({name: 'dashboard'})
	}

	const goBack = () => {
		router.back()
	}
</script>

<template>
	<div class="notfound-container">
		<div class="notfound-card">
			<div class="icon-wrapper">
				<i class="pi pi-exclamation-circle"></i>
			</div>

			<h1 class="title">404</h1>

			<h2 class="subtitle">Page Not Found</h2>

			<p class="description">The page you are looking for does not exist or has been moved.</p>

			<div class="actions">
				<Button label="Back to Dashboard" icon="pi pi-home" @click="goDashboard" />

				<Button label="Go Back" icon="pi pi-arrow-left" severity="secondary" outlined @click="goBack" />
			</div>
		</div>
	</div>
</template>

<style scoped>
	.notfound-container {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 100vh;
		padding: 2rem;
		background: var(--surface-ground);
	}

	.notfound-card {
		background: var(--surface-card);
		border-radius: 20px;
		padding: 3rem;
		text-align: center;
		max-width: 500px;
		width: 100%;
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
		border: 1px solid var(--surface-border);
	}

	.icon-wrapper {
		width: 80px;
		height: 80px;
		border-radius: 20px;
		background: var(--primary-color);
		color: white;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 2rem;
		margin: 0 auto 1.5rem auto;
	}

	.title {
		font-size: 4rem;
		font-weight: 800;
		margin: 0;
		color: var(--primary-color);
	}

	.subtitle {
		font-size: 1.5rem;
		font-weight: 600;
		margin-top: 0.5rem;
	}

	.description {
		color: var(--text-color-secondary);
		margin-top: 0.75rem;
		line-height: 1.6;
	}

	.actions {
		display: flex;
		justify-content: center;
		gap: 1rem;
		margin-top: 2rem;
		flex-wrap: wrap;
	}
</style>
