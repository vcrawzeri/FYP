<template>
    <guest-layout>
        <div class="text-center">
            <!-- Logo icon -->
            <svg class="h-10 text-indigo-500 mx-auto w-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>

            <!-- Title -->
            <h2 class="mt-6 text-2xl font-bold text-white text-center">Sign in to your account</h2>
        </div>

        <!-- Form -->
        <form class="mt-8 space-y-6" @submit.prevent="login">
            <div v-if="errorMsg" class="py-3 px-5 bg-red-500 text-white rounded flex items-center justify-center">
                {{ errorMsg }}
                <span
                    @click="errorMsg = ''"
                    class="w-8 h-8 hover:bg-black/20 cursor-pointer items-center justify-between rounded-full transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
            </div>
            <div>
                <label for="email" class="text-sm font-medium text-white block">Email address</label>
                <input
                    id="email"
                    type="email"
                    autocomplete="email"
                    required
                    v-model="user.email"
                    class="mt-1 rounded-md border-gray-600 text-white placeholder-gray-400 px-3 py-2 focus:ring-indigo-500 block w-full border bg-[#1e293b] focus:ring-2 focus:outline-none"
                />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="text-sm font-medium text-white block">Password</label>
                    <router-link :to="{ name: 'requestPassword' }" class="text-sm font-medium text-indigo-400 hover:text-indigo-300">
                        Forgot password?
                    </router-link>
                </div>
                <input
                    id="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    v-model="user.password"
                    class="mt-1 rounded-md border-gray-600 text-white placeholder-gray-400 px-3 py-2 focus:ring-indigo-500 block w-full border bg-[#1e293b] focus:ring-2 focus:outline-none"
                />
            </div>

            <div>
                <button
                    :disabled="loading"
                    type="submit"
                    class="rounded-md bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 text-sm gap-2 flex w-full items-center justify-center transition"
                    :class="{
                        'bg-indigo-200 hover:bg-indigo-600': !loading,
                        'bg-indigo-300 cursor-not-allowed': loading,
                    }"
                >
                    <svg
                        v-if="loading"
                        class="mr-3 -ml-1 size-5 animate-spin text-white"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Sign in
                </button>
            </div>
        </form>

        <!-- Bottom link -->
    </guest-layout>
</template>

<script setup>
import { ref } from 'vue';
import GuestLayout from '../components/GuestLayout.vue';
import router from '../router';
import store from '../store';

let loading = ref(false);
let errorMsg = ref('');

const user = {
    email: '',
    password: '',
    remember: false,
};

function login() {
    loading.value = true;
    store
        .dispatch('login', user)
        .then(() => {
            loading.value = false;
            router.push({ name: 'app.dashboard' });
        })
        .catch(({ response }) => {
            loading.value = false;
            errorMsg.value = response.data.message;
        });
}
</script>
