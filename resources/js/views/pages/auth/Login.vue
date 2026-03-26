<template>
	<div class="flex flex-col justify-center grow">
		<div class="max-w-md mx-auto w-full">
			<h2 class="text-2xl font-semibold text-center lg:text-left">Welcome back!</h2>

			<p class="mt-3 text-gray-500 text-center lg:text-left">Log into your account</p>

			<form @submit.prevent="attemptLogin">
				<InputText v-model="form.username" placeholder="Username" class="w-full" :class="{'p-invalid': hasUsernameError}" />
				<Message v-if="hasUsernameError" severity="error" size="small" variant="simple" class="w-full mt-2">
					{{ formError.username[0] }}
				</Message>

				<Password v-model="form.password" placeholder="Password" class="w-full mt-6" fluid :feedback="false" toggleMask :class="{'p-invalid': hasPasswordError}" />
				<Message v-if="hasPasswordError" severity="error" size="small" variant="simple" class="w-full mt-2">
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
	import {getAuthRedirect, clearAuthRedirect} from '@/utils/authRedirect'

	const router = useRouter()
	const authStore = useAuthStore()

	const loading = ref(false)
	const formError = ref({})

	const form = ref({
		username: '',
		password: '',
	})

	const hasUsernameError = computed(() => !!formError.value?.username)
	const hasPasswordError = computed(() => !!formError.value?.password)

	const getRedirectPath = () => {
		return getAuthRedirect() || '/'
	}

	const attemptLogin = async () => {
		loading.value = true
		formError.value = {}

		try {
			await axios.get('/sanctum/csrf-cookie')

			const response = await authStore.ATTEMPT_LOGIN(form.value)

			authStore.AUTHENTICATE_TOKEN(response?.access_token)
			await authStore.GET_USER_DATA()

			const redirectTo = getRedirectPath()
			clearAuthRedirect()

			await router.replace(redirectTo)
		} catch (error) {
			formError.value = error?.errors || {}
		} finally {
			loading.value = false
		}
	}
</script>
