<template>
	<section class="min-h-screen flex items-center lg:items-start lg:py-20 justify-center max-w-[100rem] mx-auto">
		<div class="flex w-full h-full justify-center gap-12">
			<!-- LEFT SIDE -->
			<div class="flex flex-col py-20 lg:min-w-[30rem]">
				<!-- Logo -->
				<a href="/" class="flex items-center justify-center lg:justify-start mb-8">
					<img :src="logoUrl" alt="CRMIS Logo" />
					<h2 class="text-2xl font-bold">CRMIS</h2>
				</a>

				<div class="flex flex-col justify-center grow">
					<div class="max-w-md mx-auto w-full">
						<h5 class="text-2xl font-semibold text-center lg:text-left">Login</h5>

						<p class="mt-3 text-gray-500 text-center lg:text-left">Please enter your details</p>

						<!-- Form -->
						<form @submit.prevent="login">
							<InputText v-model="form.email" placeholder="Email" class="w-full" />

							<Password v-model="form.password" placeholder="Password" toggleMask :feedback="false" class="w-full mt-4" />

							<div class="my-8 flex items-center justify-between">
								<div class="flex items-center gap-2">
									<Checkbox v-model="form.remember" binary inputId="remember" />
									<label for="remember" class="text-sm"> Remember me </label>
								</div>

								<RouterLink to="/auth/forgot-password" class="text-primary hover:underline text-sm"> Forgot password? </RouterLink>
							</div>

							<Button type="submit" label="Login" class="w-full" />
						</form>

						<div class="mt-8 text-center lg:text-left text-sm">
							Not registered?
							<RouterLink to="/auth/register" class="text-primary hover:underline"> Create an Account </RouterLink>
						</div>
					</div>
				</div>

				<div class="mt-8 text-center lg:text-start text-sm text-gray-400">©2026 Your Company</div>
			</div>

			<!-- RIGHT SIDE IMAGE -->
			<div class="hidden lg:flex h-full py-20">
				<div class="h-full w-full lg:max-w-[32.5rem] xl:max-w-[60.5rem] mx-auto flex items-center justify-center rounded-3xl border overflow-hidden">
					<img src="https://diamond.primevue.org/demo/images/landing/auth-image.svg" alt="Auth Image" class="w-auto h-full object-contain object-left" />
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
	import {reactive, computed} from 'vue'
	import {useRouter} from 'vue-router'

	import InputText from 'primevue/inputtext'
	import Password from 'primevue/password'
	import Button from 'primevue/button'
	import Checkbox from 'primevue/checkbox'

	const router = useRouter()

	const form = reactive({
		email: '',
		password: '',
		remember: false,
	})

	const login = () => {
		console.log('Login data:', form)

		// later connect to Laravel Sanctum API
    }

    const logoUrl = computed(() => {
		return `images/crmis-logo.png`
	})
</script>
