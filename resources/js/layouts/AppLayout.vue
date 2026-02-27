<script setup>
	import {useLayout} from '@/composables/layout'
	import {computed, onMounted} from 'vue'
	import {useAuthStore} from '@/stores/auth'
	import AppSidebar from '@/components/layout/AppSidebar.vue'
	import AppTopbar from '../components/layout/AppTopbar.vue'

	const authStore = useAuthStore()

	// ✅ Load user if token exists
	onMounted(async () => {
		const token = localStorage.getItem(authStore.authTokenName)

		if (token && !authStore.USER) {
			authStore.AUTHENTICATE_TOKEN(token)

			try {
				await authStore.GET_USER_DATA()
			} catch {
				authStore.REVOKE_TOKEN()
			}
		}
	})

	const {layoutConfig, layoutState, hideMobileMenu} = useLayout()

	const containerClass = computed(() => {
		return {
			'layout-overlay': layoutConfig.menuMode === 'overlay',
			'layout-static': layoutConfig.menuMode === 'static',
			'layout-overlay-active': layoutState.overlayMenuActive,
			'layout-mobile-active': layoutState.mobileMenuActive,
			'layout-static-inactive': layoutState.staticMenuInactive,
		}
	})
</script>

<template>
	<div class="layout-wrapper" :class="containerClass">
		<AppTopbar />
		<AppSidebar />
		<div class="layout-main-container">
			<div class="layout-main">
				<router-view />
			</div>
		</div>
		<div class="layout-mask animate-fadein" @click="hideMobileMenu" />
	</div>
	<Toast />
	<ConfirmDialog />
</template>
