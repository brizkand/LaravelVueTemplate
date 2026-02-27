<template>
	<h1>{{ message }}</h1>
</template>

<script setup>
	import {ref} from 'vue'

	console.log('component loaded')

	const message = ref('Vue Component Works!')
</script>
