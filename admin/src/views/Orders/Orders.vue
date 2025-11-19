<template>
    <div class="mb-3 flex items-center justify-between">
        <h1 class="text-3xl font-semibold">Orders</h1>

    </div>
    <OrdersTable @clickView="viewOrder"></OrdersTable>
</template>

<script setup>
import { computed, ref } from 'vue';

import OrdersTable from './OrdersTable.vue';
import store from '../../store';


const orders = computed(()=>store.state.orders)



function showProductModal() {
    showModel.value = true;
}

function showOrder(product){

}



</script>

<style></style>
