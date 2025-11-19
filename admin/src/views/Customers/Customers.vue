<template>
    <div class="mb-3 flex items-center justify-between">
        <h1 class="text-3xl font-semibold">Customers</h1>
    </div>
    <CustomerModal v-model="showModel" :customer="customerModal" @close="onModalClose"></CustomerModal>
    <CustomersTable @clickEdit="editCustomer"></CustomersTable>
</template>

<script setup>
import { ref } from 'vue';
import CustomerModal from './CustomerModal.vue';
import CustomersTable from './CustomersTable.vue';
import store from '../../store';

const DEFAULT_EMPTY_OBJECT = {
}

const showModel = ref(false);
const customerModal = ref({ ...DEFAULT_EMPTY_OBJECT });

function showCustomerModal() {
    showModel.value = true;
}

function editCustomer(c) {
    store.dispatch('getCustomer', c.id)
        .then(response => {
            // API returns the customer resource in response.data
            customerModal.value = response.data;
            showCustomerModal();
        })
        .catch(err => {
            console.error('Failed to fetch customer', err);
            // fallback to the provided object if you want:
            customerModal.value = c;
            showCustomerModal();
        });
}

function onModalClose() {
    customerModal.value = { ...DEFAULT_EMPTY_OBJECT };
}
</script>

<style></style>
