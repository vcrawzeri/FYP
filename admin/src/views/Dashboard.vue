<template>
    <div class="flex min-h-screen flex-col gap-6 bg-gray-50 p-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-gray-800">Dashboard</h1>
            <CustomInput type="select" v-model="choosenDate" @change="onDatePickerChange" :select-options="dateOptions" />
        </div>

        <!-- Cards -->
        <div class="flex flex-wrap gap-6">
            <!-- Customers -->
            <div class="flex w-64 flex-col items-center rounded-2xl bg-white p-6 shadow-lg">
                <template v-if="!loading.customersCount">
                    <div class="mb-4 text-6xl text-indigo-600">👥</div>
                    <h2 class="mb-2 text-xl font-semibold text-gray-700">Active Customers</h2>
                    <p class="text-4xl font-bold text-gray-900">{{ customersCount }}</p>
                </template>
                <Spinner v-else />
            </div>

            <!-- Products -->
            <div class="flex w-64 flex-col items-center rounded-2xl bg-white p-6 shadow-lg">
                <template v-if="!loading.productsCount">
                    <div class="mb-4 text-6xl text-green-600">📦</div>
                    <h2 class="mb-2 text-xl font-semibold text-gray-700">Active Products</h2>
                    <p class="text-4xl font-bold text-gray-900">{{ productsCount }}</p>
                </template>
                <Spinner v-else />
            </div>

            <!-- Paid Orders -->
            <div class="flex w-64 flex-col items-center rounded-2xl bg-white p-6 shadow-lg">
                <template v-if="!loading.paidOrders">
                    <div class="mb-4 text-6xl text-yellow-500">🛒</div>
                    <h2 class="mb-2 text-xl font-semibold text-gray-700">Paid Orders</h2>
                    <p class="text-4xl font-bold text-gray-900">{{ paidOrders }}</p>
                </template>
                <Spinner v-else />
            </div>

            <!-- Total Income -->
            <div class="flex w-64 flex-col items-center rounded-2xl bg-white p-6 shadow-lg">
                <template v-if="!loading.totalIncome">
                    <div class="mb-4 text-6xl text-emerald-600">💰</div>
                    <h2 class="mb-2 text-xl font-semibold text-gray-700">Total Income</h2>
                    <p class="text-4xl font-bold text-gray-900">{{ totalIncome }}</p>
                </template>
                <Spinner v-else />
            </div>
        </div>

        <!-- Doughnut Chart aligned left -->
        <div class="grid-row-2 grid grid-flow-col grid-cols-1 gap-3 md:grid-cols-3">
            <!-- Latest Orders -->
            <div class="col-span-2 row-span-2 flex flex-col rounded-lg bg-white px-5 py-6 shadow">
                <h2 class="mb-4 text-xl font-semibold text-gray-700">Latest Orders</h2>

                <div v-if="loading.latestOrders" class="flex justify-center py-4">
                    <Spinner />
                </div>

                <div v-else class="space-y-4">
                    <RouterLink
                        v-for="(o, index) in latestOrders"
                        :key="index"
                        :to="`/app/orders/${o.id}`"
                        class="flex cursor-pointer items-center justify-between rounded-xl border border-gray-100 bg-gray-50 p-3 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-300 hover:bg-white"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-lg font-semibold text-amber-600">
                                {{ o.first_name.charAt(0) }}{{ o.last_name.charAt(0) }}
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-sm font-medium text-gray-800">{{ o.first_name }} {{ o.last_name }}</h3>
                                <p class="text-xs text-gray-500">Order #{{ o.id }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col text-right">
                            <p class="text-sm font-semibold text-gray-800">${{ o.total_price }}</p>
                            <p class="text-xs text-gray-500">{{ getDaysAgo(o.created_at) }}</p>
                        </div>
                    </RouterLink>
                </div>
            </div>

            <!-- Orders by Country -->
            <div class="flex flex-col items-center justify-center rounded-lg bg-white px-5 py-6 shadow">
                <Doughnut v-if="!loading.chartData" :data="ordersByCountry" :width="100" :height="200" />
                <Spinner v-else />
            </div>

            <!-- Latest Customers -->
            <div class="rounded-lg bg-white px-6 py-6 shadow">
                <h2 class="mb-4 text-xl font-semibold text-gray-700">Latest Active Customers</h2>

                <div v-if="loading.latestCustomers" class="flex justify-center py-4">
                    <Spinner />
                </div>

                <div v-else class="space-y-4">
                    <RouterLink
                        v-for="(c, index) in latestCustomers"
                        :key="index"
                        :to="`/app/customers/${c.id}`"
                        class="flex cursor-pointer items-center gap-4 rounded-xl border border-gray-100 bg-gray-50 p-3 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-300 hover:bg-white"
                    >
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-lg font-semibold text-indigo-600">
                            {{ c.first_name.charAt(0) }}{{ c.last_name.charAt(0) }}
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-sm font-medium text-gray-800">{{ c.first_name }} {{ c.last_name }}</h3>
                            <p class="text-xs text-gray-500">{{ c.email }}</p>
                        </div>
                    </RouterLink>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import axiosClient from '../axios';
import Doughnut from '../components/core/Charts/Doughnut.vue';
import CustomInput from '../components/core/CustomInput.vue';
import Spinner from '../components/core/Spinner.vue';

const dateOptions = ref([
    { key: '1d', text: 'Last Day' },
    { key: '1w', text: 'Last Week' },
    { key: '2w', text: 'Last 2 Weeks' },
    { key: '1m', text: 'Last Month' },
    { key: '3m', text: 'Last 3 Months' },
    { key: '6m', text: 'Last 6 Months' },
    { key: 'all', text: 'All' },
]);

const choosenDate = ref('all');

const loading = ref({
    customersCount: true,
    productsCount: true,
    paidOrders: true,
    totalIncome: true,
    chartData: true,
    latestCustomers: true,
    latestOrders: true,
});

const customersCount = ref(0);
const productsCount = ref(0);
const paidOrders = ref(0);
const totalIncome = ref(0);
const latestOrders = ref([]);
const latestCustomers = ref([]);

const ordersByCountry = ref({
    labels: [],
    datasets: [
        {
            backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16', '#facc15', '#10b981'],
            data: [],
        },
    ],
});

// ✅ Helper to calculate time ago
function getDaysAgo(dateString) {
    const createdDate = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - createdDate);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return '1 day ago';
    if (diffDays < 7) return `${diffDays} days ago`;
    const weeks = Math.floor(diffDays / 7);
    if (weeks < 4) return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
    const months = Math.floor(diffDays / 30);
    return `${months} month${months > 1 ? 's' : ''} ago`;
}

function updateDashboard() {
    const d = choosenDate.value
    loading.value = {
        customersCount: true,
        productsCount: true,
        paidOrders: true,
        totalIncome: true,
        chartData: true,
        latestCustomers: true,
        latestOrders: true,
    };
    // API calls
    axiosClient.get(`/dashboard/customers-count`,{params:{d}}).then(({ data }) => {
        customersCount.value = data;
        loading.value.customersCount = false;
    });

    axiosClient.get(`/dashboard/products-count`,{params:{d}}).then(({ data }) => {
        productsCount.value = data;
        loading.value.productsCount = false;
    });

    axiosClient.get(`/dashboard/orders-count`,{params:{d}}).then(({ data }) => {
        paidOrders.value = data;
        loading.value.paidOrders = false;
    });

    axiosClient.get(`/dashboard/income-amount`,{params:{d}}).then(({ data }) => {
        totalIncome.value = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(data);
        loading.value.totalIncome = false;
    });

    axiosClient.get(`/dashboard/orders-by-country`,{params:{d}}).then(({ data }) => {
        ordersByCountry.value.labels = data.map((item) => item.name);
        ordersByCountry.value.datasets[0].data = data.map((item) => item.count);
        loading.value.chartData = false;
    });

    axiosClient.get(`/dashboard/latest-customers`,{params:{d}}).then(({ data: customers }) => {
        latestCustomers.value = customers;
        loading.value.latestCustomers = false;
    });

    axiosClient.get(`/dashboard/latest-orders`,{params:{d}}).then(({ data: orders }) => {
        latestOrders.value = orders;
        loading.value.latestOrders = false;
    });
}

function onDatePickerChange() {
    updateDashboard()
}


onMounted(()=> updateDashboard())
</script>

<style scoped>
div.bg-white:hover {
    transform: translateY(-4px);
    transition: all 0.3s ease;
}
</style>
