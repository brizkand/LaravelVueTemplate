<script setup>
	import axios from 'axios'
	import {onMounted, ref, onBeforeUnmount} from 'vue'

	const apiMessage = ref('Loading...')

	onMounted(async () => {
		const res = await axios.get('/api/test')
		apiMessage.value = res.data.message
		console.log(apiMessage.value)
	})

	function toggleDarkMode() {
		document.documentElement.classList.toggle('my-app-dark')
	}
</script>
<template>
	<h1>Home Page</h1>
	<p>{{ apiMessage }}</p>

	<Button label="Primary" />
	<Button label="Secondary" severity="secondary" />
	<Button label="Success" severity="success" />
	<Button label="Info" severity="info" />
	<Button label="Warn" severity="warn" />
	<Button label="Help" severity="help" />
	<Button label="Danger" severity="danger" />
	<Button label="Contrast" severity="contrast" />

	<br />

	<i class="pi pi-check"></i>
	<i class="pi pi-times"></i>
	<span class="pi pi-search"></span>
	<span class="pi pi-user"></span>

	<Button label="Toggle Dark Mode" @click="toggleDarkMode()" />
</template>
