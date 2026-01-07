<template>
    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <!-- Top Controls -->
        <div class="mb-4 flex flex-col gap-4 border-b pb-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-3">
                <span class="font-medium text-gray-600">Per Page</span>
                <select
                    v-model="perPage"
                    @change="getOrders(null)"
                    class="block w-28 rounded-md border border-gray-300 bg-gray-50 px-3 py-2"
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
                    @input="getOrders(null)"
                    type="text"
                    class="w-full rounded-md border border-gray-300 bg-gray-50 py-2 pl-10 pr-3"
                    placeholder="🔍 Search orders..."
                />
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-xs font-semibold uppercase text-gray-700">
                    <tr>
                        <TableHeading
                            class="p-3 text-left"
                            field="id"
                            :sort-field="sortField"
                            :sort-direction="sortDirection"
                            @click="sortOrder('id')"
                        >
                            ID
                        </TableHeading>

                        <TableHeading class="p-3 text-left">Customer</TableHeading>

                        <TableHeading
                            class="p-3 text-left"
                            field="status"
                            :sort-field="sortField"
                            :sort-direction="sortDirection"
                            @click="sortOrder('status')"
                        >
                            Status
                        </TableHeading>

                        <TableHeading
                            class="p-3 text-left"
                            field="total_price"
                            :sort-field="sortField"
                            :sort-direction="sortDirection"
                            @click="sortOrder('total_price')"
                        >
                            Price
                        </TableHeading>

                        <TableHeading
                            class="p-3 text-left"
                            field="created_at"
                            :sort-field="sortField"
                            :sort-direction="sortDirection"
                            @click="sortOrder('created_at')"
                        >
                            Date
                        </TableHeading>

                        <TableHeading field="actions" class="p-3 text-center">
                            Actions
                        </TableHeading>
                    </tr>
                </thead>

                <tbody v-if="orders.loading">
                    <tr>
                        <td colspan="6" class="py-8 text-center">
                            <Spinner />
                        </td>
                    </tr>
                </tbody>

                <tbody v-else class="divide-y divide-gray-100">
                    <tr v-for="order in orders.data" :key="order.id">
                        <td class="p-3 font-medium">{{ order.id }}</td>
                        <td class="p-3">
                            {{ order.customer?.first_name }} {{ order.customer?.last_name }}
                        </td>
                        <td class="p-3">
                            <OrderStatus :order="order" />
                        </td>
                        <td class="p-3 font-semibold">${{ order.total_price }}</td>
                        <td class="p-3">{{ order.created_at }}</td>
                        <td class="p-3 text-center">
                            <router-link
                                :to="{ name: 'app.orders.view', params: { id: order.id } }"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border"
                            >
                                👁
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="!orders.loading" class="mt-6 flex justify-between text-sm">
            <span>
                Showing <strong>{{ orders.from }}</strong> to
                <strong>{{ orders.to }}</strong>
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Spinner from '../../components/core/Spinner.vue';
import TableHeading from '../../components/core/Table/TableHeading.vue';
import OrderStatus from './OrderStatus.vue';
import store from '../../store';

const perPage = ref(10);
const search = ref('');
const orders = computed(() => store.state.orders);

const sortField = ref('created_at');
const sortDirection = ref('desc');

onMounted(() => {
    getOrders();
});

function getOrders(url = null) {
    store.dispatch('getOrders', {
        url,
        perPage: perPage.value,
        search: search.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value,
    });
}

function sortOrder(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }

    getOrders();
}
</script>


<style></style>
