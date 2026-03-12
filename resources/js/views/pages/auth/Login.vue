<template>
	<div class="flex flex-col justify-center grow">
		<div class="max-w-md mx-auto w-full">
			<h2 class="text-2xl font-semibold text-center lg:text-left">Welcome back!</h2>

			<p class="mt-3 text-gray-500 text-center lg:text-left">Log into your account</p>

			<!-- Form -->
			<form @submit.prevent="attemptLogin">
				<InputText v-model="form.username" placeholder="Username" class="w-full" :class="{'p-invalid': hasUsernameError}" />
				<Message v-if="hasUsernameError" severity="error" size="small" variant="simple" class="w-full mt-2" :class="{'p-error': hasUsernameError}">
					{{ formError.username[0] }}
				</Message>

				<Password v-model="form.password" placeholder="Password" class="w-full mt-6" fluid :feedback="false" toggleMask :class="{'p-invalid': hasPasswordError}"> </Password>
				<Message v-if="hasPasswordError" severity="error" size="small" variant="simple" class="w-full mt-2" :class="{'p-error': hasPasswordError}">
					{{ formError.password[0] }}
				</Message>

				<Button type="submit" label="Login" class="w-full mt-6" severity="info" size="large" :loading="loading" />
			</form>
		</div>
	</div>
</template>

<script setup>
	import {ref, computed} from 'vue'
	import {useRouter} from 'vue-router'
	import {useAuthStore} from '@/stores/auth'

	const router = useRouter()

	const authStore = useAuthStore()

	const loading = ref(false)
	const formError = ref(null)

	const form = ref({
		username: '',
		password: '',
	})

	const hasUsernameError = computed(() => !!formError.value?.username)
	const hasPasswordError = computed(() => !!formError.value?.password)

	const attemptLogin = async () => {
		loading.value = true

		try {
			await axios.get('/sanctum/csrf-cookie')

			const response = await authStore.ATTEMPT_LOGIN(form.value)

			authStore.AUTHENTICATE_TOKEN(response?.access_token)

			// Fetch user data after successful login
			await authStore.GET_USER_DATA()

			router.push('/')
		} catch (error) {
			formError.value = error.errors
		} finally {
			loading.value = false
		}
	}
</script>
