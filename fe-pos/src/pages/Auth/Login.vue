<script setup lang="ts">
import { ref } from 'vue';
import InputText from 'primevue/inputtext';
import router from '@/router';
import { useAuthStore } from '@/stores/auth.store';

const auth = useAuthStore()
const error = ref < string | null > (null)
const showPassword = ref(false)
const form = ref({
    'email': '',
    'password': '',
})

async function login() {
    error.value = null;
    if (!form.value.email || !form.value.password) {
        error.value = 'Email and Password are required'
        return
    }

    try {
        await auth.login(form.value.email, form.value.password)
        router.push({ name: 'dashboard' })
    } catch (e) {
        error.value = 'Invalid email or password'
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-surface-50 px-4 py-8">
        <div class="w-full max-w-[380px] rounded-2xl border border-surface-200  bg-white px-7 py-8 shadow-sm">
            <!-- Logo -->
            <div class="mb-7 text-center">
                <div
                    class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary-500 text-white shadow-md">
                    <i class="pi pi-bolt text-xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-surface-900">
                    Welcome Back
                </h1>

                <p class="mt-1.5 text-sm text-surface-500">
                    Sign in to continue to your account
                </p>
            </div>
            <form @submit.prevent="login" class="flex flex-col gap-5">
                <!-- Error Message -->
                <div v-if="error" class="mb-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    <i class="pi pi-exclamation-circle text-red-500"></i>
                    <span>
                        {{ error }}
                    </span>
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-surface-700">
                        Email Address <span class="text-red-600">*</span>
                    </label>

                    <div class="relative">
                        <!-- Email Icon -->
                        <i class="pi pi-envelope absolute left-3.5 top-1/2
                                   z-10 -translate-y-1/2 text-surface-400"></i>

                        <!-- Email Input -->
                        <InputText id="email" v-model="form.email" type="email" placeholder="name@gmail.com" fluid
                            class="!h-11 !bg-surface-50 !pl-10
                                   !text-sm focus:!bg-white" />
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-surface-700">
                        Password <span class="text-red-600">*</span>
                    </label>

                    <div class="relative">

                        <!-- Lock Icon -->
                        <i class="pi pi-lock absolute left-3.5 top-1/2
                                   z-10 -translate-y-1/2 text-surface-400"></i>

                        <!-- Password Input -->
                        <InputText id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                            placeholder="********" fluid class="!h-11 !bg-surface-50 !pl-10 !pr-12
                                   !text-sm focus:!bg-white" />

                        <!-- Show / Hide Password -->
                        <button type="button" class="absolute right-3 top-1/2 z-20
                                   flex h-8 w-8 -translate-y-1/2
                                   items-center justify-center
                                   rounded-lg text-surface-400
                                   transition-colors
                                   hover:bg-surface-100
                                   hover:text-surface-700" @click="showPassword = !showPassword">
                            <i :class="showPassword
                                ? 'pi pi-eye-slash'
                                : 'pi pi-eye'
                                " class="text-sm"></i>
                        </button>

                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">

                    <label class="flex cursor-pointer items-center gap-2
                               text-sm text-surface-600">
                        <input type="checkbox" class="peer sr-only" />

                        <span class="relative flex h-4 w-4 items-center
                                   justify-center rounded border-2
                                   border-surface-400 bg-white
                                   transition-all duration-200
                                   peer-checked:border-primary-500
                                   peer-checked:bg-primary-500">
                            <span class="absolute h-2.5 w-1.5 rotate-45
                                       border-b-2 border-r-2 border-white
                                       opacity-0 transition-opacity
                                       peer-checked:opacity-100"></span>
                        </span>

                        <span>Remember Me</span>
                    </label>

                    <a href="#" class="text-sm font-medium text-primary-500
                               transition-colors hover:text-primary-600">
                        Forgot password?
                    </a>

                </div>

                <!-- Login Button -->
                <button type="submit" label="Login" fluid class="flex h-11 w-full items-center justify-center
                           gap-2 rounded-xl bg-primary-500 px-4
                           text-sm font-semibold text-white
                           transition-all duration-200
                           hover:bg-primary-600
                           active:scale-[0.98]">
                    <i class="pi pi-sign-in"></i>
                    <span>Sign In</span>
                </button>

            </form>
        </div>
    </div>
</template>
