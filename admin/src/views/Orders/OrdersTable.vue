<template>
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <!-- Top Controls -->
        <div class="mb-4 flex flex-col gap-4 border-b pb-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-3">
                <span class="font-medium text-gray-600">Per Page</span>
                <select
                    @change="getOrders(null)"
                    v-model="perPage"
                    class="block w-28 rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-gray-800 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div class="relative w-full md:w-1/3">
                <input
                    v-model="search"
                    @change="getOrders(null)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 bg-gray-50 py-2 pl-10 pr-3 text-gray-800 placeholder-gray-400 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    placeholder="🔍 Search orders..."
                />
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-700">
                    <tr>
                        <TableHeading @click="sortOrder" class="p-3 text-left" field="id" :sort-field="sortField" :sort-direction="sortDirection"
                            >ID</TableHeading
                        >

                        <TableHeading class="p-3 text-left">Customer</TableHeading>

                        <TableHeading
                            @click="sortOrders('status')"
                            class="p-3 text-left"
                            field="status"
                            :sort-field="sortField"
                            :sort-direction="sortDirection"
                            >Status</TableHeading
                        >

                        <TableHeading
                            @click="sortOrders('total_price')"
                            class="p-3 text-left"
                            field="total_price"
                            :sort-field="sortField"
                            :sort-direction="sortDirection"
                            >Price</TableHeading
                        >

                        <TableHeading
                            @click="sortOrders('created_at')"
                            class="p-3 text-left"
                            field="created_at"
                            :sort-field="sortField"
                            :sort-direction="sortDirection"
                            >Date</TableHeading
                        >

                        <TableHeading field="actions" class="p-3 text-center">Actions</TableHeading>
                    </tr>
                </thead>

                <!-- Loading State -->
                <tbody v-if="orders.loading">
                    <tr>
                        <td colspan="6" class="py-8 text-center">
                            <Spinner />
                        </td>
                    </tr>
                </tbody>

                <!-- Data Rows -->
                <tbody v-else class="divide-y divide-gray-100">
                    <tr v-for="order in orders.data" :key="order.id" class="transition hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-800">{{ order.id }}</td>
                        <td class="p-3 text-gray-700">{{ order.customer?.first_name }} {{ order.customer?.last_name }}</td>
                        <td class="p-3">
                            <OrderStatus :order="order"/>
                        </td>
                        <td class="p-3 font-semibold text-gray-800">${{ order.total_price }}</td>
                        <td class="p-3 text-gray-600">{{ order.created_at }}</td>

                        <td class="p-3 text-center">
                            <router-link
                                :to="{ name: 'app.orders.view', params: { id: order.id } }"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-400 transition hover:bg-indigo-600 hover:text-white"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-5 w-5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                                    />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="!orders.loading" class="mt-6 flex flex-col items-center justify-between gap-3 text-sm text-gray-600 md:flex-row">
            <span
                >Showing <strong>{{ orders.from }}</strong> to <strong>{{ orders.to }}</strong></span
            >

            <nav v-if="orders.total > orders.limit" class="flex flex-wrap items-center justify-center gap-1" aria-label="Pagination">
                <a
                    v-for="(link, i) in orders.links"
                    :key="i"
                    href="#"
                    @click.prevent="getForPage($event, link)"
                    v-html="link.label"
                    class="rounded-md border px-3 py-1 text-sm font-medium transition"
                    :class="[
                        link.active ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-100',
                        !link.url ? 'cursor-not-allowed bg-gray-100 text-gray-400' : '',
                    ]"
                ></a>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import Spinner from '../../components/core/Spinner.vue';
import TableHeading from '../../components/core/Table/TableHeading.vue';
import { PRODUCTS_PER_PAGE } from '../../constants';
import store from '../../store';
import OrderStatus from './OrderStatus.vue';

const emit = defineEmits(['clickEdit']);
const perPage = ref(PRODUCTS_PER_PAGE);
const search = ref('');
const orders = computed(() => store.state.orders);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

onMounted(() => {
    getOrders();
});

function getOrders(url = null) {
    store.dispatch('getOrders', {
        url,
        sort_field: sortField.value,
        sort_direction: sortDirection.value,
        search: search.value,
        perPage: perPage.value,
    });
}

function getForPage(ev, link) {
    if (!link.url || link.active) {
        return;
    }
    getOrders(link.url);
}

function sortOrder(field) {
    if (sortField.value === field) {
        if (sortDirection.value === 'asc') {
            sortDirection.value = 'desc';
        } else {
            sortDirection.value = 'asc';
        }
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    getOrders();
}

function showOrder(order) {
    emit('clickShow', order);
}

function delelteOrder(order) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }
    store.dispatch('deleteOrder', order.id).then((res) => {
        //Show notification
        store.dispatch('getOrders');
    });
}
</script>

<style></style>
