<template>
    <div class="mb-3 flex items-center justify-between">
        <h1 class="text-3xl font-semibold">Products</h1>
        <button
            @click="showProductModal"
            type="submit"
            class="py-2 px-4 text-sm font-medium rounded-md text-white bg-indigo-600 flex justify-center border border-transparent"
        >
            Add new product
        </button>
    </div>
    <ProductModal v-model="showModel" :product="productModal" @close="onModalClose"></ProductModal>
    <ProductsTable @clickEdit="editProduct"></ProductsTable>
</template>

<script setup>
import { ref } from 'vue';
import ProductModal from './ProductModal.vue';
import ProductsTable from './ProductsTable.vue';
import store from '../../store';


const DEFAULT_EMPTY_OBJECT = {
    id: '',
    title: '',
    image: '',
    description: '',
    price: '',
}


const showModel = ref(false);
const productModal = ref({...DEFAULT_EMPTY_OBJECT});

function showProductModal() {
    showModel.value = true;
}

function editProduct(product){
    store.dispatch('getProduct',product.id)
    .then(({data})=>{
        productModal.value = data
        showProductModal()
    })
}

function onModalClose(){
    productModal.value = {...DEFAULT_EMPTY_OBJECT}
}

</script>

<style></style>
