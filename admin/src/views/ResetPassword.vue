<template>
    <guest-layout title="Set new password">
        <form class="space-y-6" @submit.prevent="reset">
            <div>
                <label for="new-password" class="text-sm/6 font-medium text-white block">New password</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    class="mt-1 rounded-md border-gray-600 text-white placeholder-gray-400 px-3 py-2 focus:ring-indigo-500 block w-full border bg-[#1e293b] focus:ring-2 focus:outline-none"
                />
            </div>

            <div>
                <label for="password-repeat" class="text-sm/6 font-medium text-white block">Repeat password</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    class="mt-1 rounded-md border-gray-600 text-white placeholder-gray-400 px-3 py-2 focus:ring-indigo-500 block w-full border bg-[#1e293b] focus:ring-2 focus:outline-none"
                />
            </div>

            <div class="flex items-center justify-between">
                <router-link :to="{ name: 'login' }" class="text-indigo-600 hover:text-indigo-500 text-sm font-semibold"> Back to login </router-link>
            </div>

            <div>
                <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded-md font-semibold hover:bg-indigo-500 w-full">Submit</button>
            </div>
        </form>
    </guest-layout>
</template>

<script setup>
import GuestLayout from '../components/GuestLayout.vue';
function reset() {
    console.log('Password reset submitted');
}


</script>
