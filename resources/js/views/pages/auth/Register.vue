<template>
	<div class="flex flex-col justify-center grow">
		<div class="max-w-md mx-auto w-full">
			<h2 class="text-2xl font-semibold text-center lg:text-left">Register</h2>

			<p class="mt-3 text-gray-500 text-center lg:text-left">Enter your details to create a new account.</p>

			<!-- Form -->
			<form @submit.prevent="attemptRegister">
				<!-- Name -->
				<InputText v-model="form.name" placeholder="Full Name" class="w-full" :class="{'p-invalid': hasNameError}" />
				<Message v-if="hasNameError" severity="error" size="small" variant="simple" class="w-full mt-2">
					{{ formError.name[0] }}
				</Message>

				<!-- Email -->
				<InputText v-model="form.email" placeholder="Email" class="w-full mt-6" :class="{'p-invalid': hasEmailError}" />
				<Message v-if="hasEmailError" severity="error" size="small" variant="simple" class="w-full mt-2">
					{{ formError.email[0] }}
				</Message>

				<!-- Password -->
				<Password v-model="form.password" placeholder="Password" class="w-full mt-6" fluid toggleMask :feedback="true" :class="{'p-invalid': hasPasswordError}">
					<template #header>
						<div class="font-semibold text-sm mb-4">Create Password</div>
					</template>

					<template #footer>
						<Divider />
						<ul class="pl-2 my-0 text-sm space-y-2">
							<li v-for="(rule, i) in passwordRules" :key="i" class="flex items-center gap-2 transition-all duration-200">
								<!-- Check Icon -->
								<i :class="rule.valid ? 'pi pi-check-circle text-green-500' : 'pi pi-circle text-gray-400'"></i>

								<span :class="rule.valid ? 'text-green-600' : 'text-gray-500'">
									{{ rule.label }}
								</span>
							</li>
						</ul>
					</template>
				</Password>

				<Message v-if="hasPasswordError" severity="error" size="small" variant="simple" class="w-full mt-2">
					{{ formError.password[0] }}
				</Message>

				<!-- Confirm Password -->
				<Password v-model="form.password_confirmation" placeholder="Confirm Password" class="w-full mt-6" fluid toggleMask :feedback="false" :class="{'p-invalid': hasConfirmPasswordError}" />

				<Message v-if="hasConfirmPasswordError" severity="error" size="small" variant="simple" class="w-full mt-2">
					{{ formError.password_confirmation[0] }}
				</Message>

				<!-- Submit -->
				<Button type="submit" label="Register" class="w-full mt-8" severity="info" size="large" :loading="loading" />
			</form>

			<div class="mt-8 text-center lg:text-left text-sm">
				Already have an account?
				<RouterLink to="/auth/login" class="text-primary hover:underline"> Login </RouterLink>
			</div>
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
	const formError = ref({})

	const form = ref({
		name: '',
		email: '',
		password: '',
		password_confirmation: '',
	})

	/*
    |--------------------------------------------------------------------------
    | Validation states
    |--------------------------------------------------------------------------
    */
	const hasNameError = computed(() => !!formError.value?.name)
	const hasEmailError = computed(() => !!formError.value?.email)
	const hasPasswordError = computed(() => !!formError.value?.password)
	const hasConfirmPasswordError = computed(() => !!formError.value?.password_confirmation)

	/*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */
	const attemptRegister = async () => {
		// Set loading state and clear previous errors
		loading.value = true
		formError.value = {}

		await axios.get('/sanctum/csrf-cookie').then(() => {
			authStore
				.ATTEMPT_REGISTER(form.value)
				.then((response) => {
					// Optional: auto login after register
					if (response?.access_token) {
						authStore.AUTHENTICATE_TOKEN(response.access_token)
					}

					router.push('/')
				})
				.catch((error) => {
					formError.value = error.errors || {}
				})
				.finally(() => {
					loading.value = false
				})
		})
	}

	const passwordRules = computed(() => {
		const password = form.value.password || ''

		return [
			{label: 'At least one lowercase', valid: /[a-z]/.test(password)},
			{label: 'At least one uppercase', valid: /[A-Z]/.test(password)},
			{label: 'At least one numeric', valid: /[0-9]/.test(password)},
			{label: 'Minimum 8 characters', valid: password.length >= 8},
			{label: 'At least one special character (!@#$%^&*)', valid: /[!@#$%^&*]/.test(password)},
			{label: 'Only allowed characters (A-Z, a-z, 0-9, !@#$%^&*)', valid: /^[A-Za-z0-9!@#$%^&*]*$/.test(password)},
		]
	})
</script>
