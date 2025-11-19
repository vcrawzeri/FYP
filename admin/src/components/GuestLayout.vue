<script setup>
const { title } = defineProps({
  title: String,
})
</script>

<template>
  <div class="min-h-screen bg-[#0f172a] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <slot />
    </div>
  </div>
</template>

