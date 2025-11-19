<template>
  <div
    v-show="toast.show"
    class="fixed left-1/2 -translate-x-1/2 top-16 w-[380px] rounded-2xl shadow-lg bg-white border border-gray-200 text-gray-800 overflow-hidden"
  >
    <!-- Header -->
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
      <div class="font-semibold text-base text-gray-900">
        {{ toast.message }}
      </div>
      <button
        @click="close"
        class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 transition-colors"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Progress Bar -->
    <div class="h-1 bg-gray-200 relative">
      <div
        class="absolute left-0 top-0 h-full bg-emerald-500 transition-all duration-100 ease-linear"
        :style="{ width: `${percent}%` }"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import store from '../../store/index.js'

let interval = null
let timeout = null

const percent = ref(0)
const toast = computed(() => store.state.toast)

watch(store.state.toast, (newToast) => {
  if (newToast.show) {
    if (interval) {
      clearInterval(interval)
      interval = null
    }
    if (timeout) {
      clearTimeout(timeout)
      timeout = null
    }

    timeout = setTimeout(() => {
      close()
      timeout = null
    }, toast.value.delay)
    const startDate = Date.now()
    const futureDate = Date.now() + toast.value.delay
    interval = setInterval(() => {
      const date = Date.now()
      percent.value = ((date - startDate) * 100) / (futureDate - startDate)
      if (percent.value >= 100) {
        clearInterval(interval)
        interval = null
      }
    }, 30)
  }
})

function close() {
  store.commit('hideToast')
}
</script>

<style scoped>
/* No animation, just clean styling */
</style>
