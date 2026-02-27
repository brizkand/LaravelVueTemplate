<template>
	<div class="flex flex-col justify-center grow">
		<div class="max-w-md mx-auto w-full">
			<h2 class="text-2xl font-semibold text-center lg:text-left">Welcome back!</h2>

			<p class="mt-3 text-gray-500 text-center lg:text-left">Log into your account</p>

			<!-- Form -->
			<form @submit.prevent="attemptLogin">
				<InputText v-model="form.email" placeholder="Email" class="w-full" :class="{'p-invalid': hasEmailError}" />
				<Message v-if="hasEmailError" severity="error" size="small" variant="simple" class="w-full mt-2" :class="{'p-error': hasEmailError}">
					{{ formError.email[0] }}
				</Message>

				<Password v-model="form.password" placeholder="Password" class="w-full mt-6" fluid :feedback="false" toggleMask :class="{'p-invalid': hasPasswordError}"> </Password>
				<Message v-if="hasPasswordError" severity="error" size="small" variant="simple" class="w-full mt-2" :class="{'p-error': hasPasswordError}">
					{{ formError.password[0] }}
				</Message>

				<div class="my-8 flex items-center justify-between">
					<RouterLink to="/auth/forgot-password" class="text-primary hover:underline text-sm"> Forgot password? </RouterLink>
				</div>

				<Button type="submit" label="Login" class="w-full" severity="info" size="large" :loading="loading" />
			</form>

			<div class="mt-8 text-center lg:text-left text-sm">
				Not registered?
				<RouterLink to="/auth/register" class="text-primary hover:underline"> Create an Account </RouterLink>
			</div>
		</div>
	</div>
</template>

<script setup>
	import {ref, reactive, computed} from 'vue'
	import {useRouter} from 'vue-router'
	import {useAuthStore} from '@/stores/auth'

	const router = useRouter()

	const authStore = useAuthStore()

	const loading = ref(false)
	const formError = ref(null)

	const form = ref({
		email: '',
		password: '',
		remember: false,
	})

	const hasEmailError = computed(() => !!formError.value?.email)
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
