<template>
	<button type="button" class="layout-topbar-action" @click="toggle">
		<Avatar :label="userInitials" shape="circle" size="normal" />
		<span class="ml-2">Profile</span>
	</button>
	<Menu ref="menu" :model="items" popup />
</template>

<script setup>
	import {ref, computed} from 'vue'
	import {useAuthStore} from '@/stores/auth'
	import {useConfirm} from 'primevue/useconfirm'
	import {useRouter} from 'vue-router'

	// Access the router instance
	const router = useRouter()
	// Access the confirmation service
	const confirm = useConfirm()
	// Access the auth store
	const authStore = useAuthStore()

	const menu = ref()

	// ✅ Get initials from store
	const userInitials = computed(() => {
		return authStore.USER?.data?.avatar_name_initials || '?'
	})

	const items = [
		{label: 'Profile'},
		{label: 'Settings'},
		{separator: true},
		{
			label: 'Logout',
			command: () => {
				// Handle logout logic here
				confirmLogout()
			},
		},
	]

	const toggle = (event) => menu.value.toggle(event)

	// Logout function
	const logout = () => authStore.REVOKE_TOKEN()

	const confirmLogout = () => {
		// Show confirmation dialog before logging out
		confirm.require({
			message: 'Are you sure you want to logout?',
			header: 'Logout Confirmation',
			icon: 'pi pi-question-circle',
			rejectProps: {
				label: 'Cancel',
				severity: 'secondary',
				outlined: true,
			},
			acceptProps: {
				label: 'Logout',
			},
			accept: () => {
				logout()
			},
			reject: () => {},
		})
	}
</script>
