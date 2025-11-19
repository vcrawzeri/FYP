<template>
    <guest-layout>
        <form class="space-y-6" @submit.prevent="requestReset">
            <!-- Email Field -->
            <div>
                <label for="email" class="text-sm font-medium text-white block">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    class="mt-1 rounded-md border-gray-600 text-white placeholder-gray-400 px-3 py-2 focus:ring-indigo-500 block w-full border bg-[#1e293b] focus:ring-2 focus:outline-none"
                />
            </div>

            <!-- Link to login -->
            <div class="text-sm">
                <router-link :to="{ name: 'login' }" class="font-medium text-indigo-400 hover:text-indigo-300"> Remember your password? </router-link>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="rounded-md bg-indigo-600 hover:bg-indigo-700 px-3 py-2 text-sm font-semibold text-white focus:ring-indigo-500 flex w-full justify-center transition focus:ring-2 focus:outline-none"
                >
                    Submit
                </button>
            </div>
        </form>
    </guest-layout>
</template>

<script setup>
import GuestLayout from '../components/GuestLayout.vue';

function requestReset() {
    console.log('Password reset requested');
}
</script>
