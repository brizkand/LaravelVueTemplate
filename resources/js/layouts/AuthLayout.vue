<template>
	<section class="min-h-screen flex items-center lg:items-start lg:py-20 justify-center max-w-[100rem] mx-auto">
		<div class="flex w-full h-full justify-center gap-12">
			<!-- LEFT SIDE -->
			<div class="flex flex-col py-20 lg:min-w-[30rem]">
				<!-- Logo -->
				<a href="/" class="flex items-center justify-center lg:justify-start mb-4">
					<img :src="logoUrl" alt="CRMIS Logo" width="120" height="120" />
				</a>

				<router-view />
			</div>

			<!-- RIGHT SIDE IMAGE -->
			<div class="hidden lg:flex h-full py-20">
				<div class="h-full w-full lg:max-w-[32.5rem] xl:max-w-[52rem] mx-auto flex items-center justify-center rounded-3xl overflow-hidden shadow-lg">
					<img :src="rightSideImageUrl" alt="Auth Image" class="w-auto h-full object-contain object-left" />
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
	import {computed} from 'vue'

	const logoUrl = computed(() => {
		return `/images/crmis_logo_nb.png`
	})

	const rightSideImageUrl = computed(() => {
		return `/images/crmis_auth_image.jpg`
	})
</script>
