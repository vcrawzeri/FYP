<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 flex flex-col items-center p-8">
    <!-- Header -->
    <h1
      class="text-4xl font-extrabold text-gray-800 mb-10 tracking-tight drop-shadow-sm"
    >
      Reports Center
    </h1>

    <!-- Navigation Tabs -->
    <div
      class="flex flex-wrap justify-center gap-6 mb-12 bg-white rounded-2xl shadow-lg p-4 border border-gray-100"
    >
      <router-link
        :to="{ name: 'reports.orders' }"
        class="px-8 py-3 rounded-xl text-lg font-semibold bg-white text-gray-800 border border-gray-200 hover:bg-blue-600 hover:text-white shadow-sm transition-all duration-300 ease-out transform hover:-translate-y-1 hover:shadow-md"
        active-class="bg-blue-600 text-white border-blue-600 shadow-md scale-105"
      >
        📦 Orders Report
      </router-link>

      <router-link
        :to="{ name: 'reports.customers' }"
        class="px-8 py-3 rounded-xl text-lg font-semibold bg-white text-gray-800 border border-gray-200 hover:bg-green-600 hover:text-white shadow-sm transition-all duration-300 ease-out transform hover:-translate-y-1 hover:shadow-md"
        active-class="bg-green-600 text-white border-green-600 shadow-md scale-105"
      >
        👥 Customers Report
      </router-link>
    </div>

    <!-- Report Display Area -->
    <div
      class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl border border-gray-100 p-10 transition-all duration-500 hover:shadow-blue-100"
    >
      <router-view />
    </div>
  </div>
</template>

<script setup>
// no logic changed
</script>

<style>
/* Smooth transition for router-view */
.router-view-enter-active,
.router-view-leave-active {
  transition: all 0.4s ease;
}
.router-view-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.router-view-enter-to {
  opacity: 1;
  transform: translateY(0);
}
</style>
