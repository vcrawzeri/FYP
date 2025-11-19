<template>
    <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium capitalize"
                                :class="{
                                    'bg-green-100 text-green-700': order.status === 'paid' || order.status === 'complete',
                                    'bg-yellow-100 text-yellow-700': order.status === 'unpaid',
                                    'bg-blue-100 text-blue-700': order.status === 'shipped',
                                    'bg-red-100 text-red-700': order.status === 'cancelled',
                                    'bg-gray-100 text-gray-600': !['paid', 'unpaid', 'cancelled', 'shipped', 'complete'].includes(order.status),
                                }"
                            >
                                {{ order.status }}
                            </span>
</template>


<script setup>
 const {order} = defineProps({
    order: Object
 })
</script>


<style scoped>

</style>
