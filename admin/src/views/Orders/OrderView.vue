<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <!-- Order Details Card -->
    <div class="bg-white rounded-2xl shadow-md p-6 mb-6" v-if="order">
      <h2 class="text-2xl font-semibold mb-4 text-gray-800">
        Order Summary
        <OrderStatus :order="order"/>
    </h2>
      <table class="w-full text-sm text-gray-700">
        <tbody>
          <tr class="border-b">
            <td class="py-2 font-semibold w-40">Order #</td>
            <td>{{ order.id ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Order Date</td>
            <td>{{ order.created_at ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Status</td>
            <td>
                <!-- I want to check this error later -->
                <select v-model="order.status" @change="onStatusChange">
                    <option v-for="status of orderStatuses" :value="status">{{ status }}</option>
                </select>
            </td>
          </tr>
          <tr>
            <td class="py-2 font-semibold">Subtotal</td>
            <td class="text-emerald-600 font-bold">${{ order.total_price ?? 0 }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Customer Details Card -->
    <div class="bg-white rounded-2xl shadow-md p-6 mb-6" v-if="order.customer">
      <h2 class="text-2xl font-semibold mb-4 text-gray-800">Customer Details</h2>
      <table class="w-full text-sm text-gray-700">
        <tbody>
          <tr class="border-b">
            <td class="py-2 font-semibold w-40">Full Name</td>
            <td>{{ order.customer.first_name ?? '-' }} {{ order.customer.last_name ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Email</td>
            <td>{{ order.customer.email ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Phone</td>
            <td>{{ order.customer.phone ?? '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Billing Address Card -->
    <div class="bg-white rounded-2xl shadow-md p-6 mb-6" v-if="order.customer?.billingAddress">
      <h2 class="text-2xl font-semibold mb-4 text-gray-800">Billing Address</h2>
      <table class="w-full text-sm text-gray-700">
        <tbody>
          <tr class="border-b">
            <td class="py-2 font-semibold w-40">Address 1</td>
            <td>{{ order.customer.billingAddress.address1 ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Address 2</td>
            <td>{{ order.customer.billingAddress.address2 ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">City</td>
            <td>{{ order.customer.billingAddress.city ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">State</td>
            <td>{{ order.customer.billingAddress.state ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">ZIP</td>
            <td>{{ order.customer.billingAddress.zipcode ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Country</td>
            <td>{{ order.customer.billingAddress.country?.name ?? '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>



    <!-- Shipping Address Card -->
    <div class="bg-white rounded-2xl shadow-md p-6 mb-6" v-if="order.customer?.shippingAddress">
      <h2 class="text-2xl font-semibold mb-4 text-gray-800">Shipping Address</h2>
      <table class="w-full text-sm text-gray-700">
        <tbody>
          <tr class="border-b">
            <td class="py-2 font-semibold w-40">Address 1</td>
            <td>{{ order.customer.shippingAddress.address1 ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Address 2</td>
            <td>{{ order.customer.shippingAddress.address2 ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">City</td>
            <td>{{ order.customer.shippingAddress.city ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">State</td>
            <td>{{ order.customer.shippingAddress.state ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">ZIP</td>
            <td>{{ order.customer.shippingAddress.zipcode ?? '-' }}</td>
          </tr>
          <tr class="border-b">
            <td class="py-2 font-semibold">Country</td>
            <td>{{ order.customer.shippingAddress.country?.name ?? '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Order Items List -->
    <div class="bg-white rounded-2xl shadow-md p-6">
      <h2 class="text-2xl font-semibold mb-4 text-gray-800">Order Items</h2>

      <div
        v-for="item in order.items ?? []"
        :key="item.id"
        class="flex items-center justify-between border-b last:border-none py-4"
      >
        <!-- Left Section: Image + Info -->
        <div class="flex items-center gap-4">
          <!-- Product Image -->
          <div class="h-24 w-24 flex items-center justify-center overflow-hidden rounded-lg bg-gray-100">
            <img :src="item.product?.image ?? ''" alt="" class="object-cover w-full h-full" />
          </div>

          <!-- Product Title + Quantity -->
          <div class="flex flex-col">
            <h3 class="text-lg font-medium text-gray-800">{{ item.product?.title ?? '-' }}</h3>
            <p class="text-sm text-gray-600 mt-1">
              Qty: <span class="font-semibold">{{ item.quantity ?? 0 }}</span>
            </p>
          </div>
        </div>

        <!-- Right Section: Price -->
        <div class="text-right">
          <p class="text-lg font-semibold text-emerald-600">${{ item.unit_price ?? 0 }}</p>
        </div>
      </div>

      <!-- No items fallback -->
      <p v-if="!(order.items?.length)" class="text-gray-500 text-center py-4">
        No items found in this order.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import store from '../../store'
import axiosClient from '../../axios'
import OrderStatus from './OrderStatus.vue'


const route = useRoute()
const order = ref({})
const orderStatuses = ref([]);

onMounted(() => {
  store.dispatch('getOrder', route.params.id)
    .then(({ data }) => {
      order.value = data ?? {}
    })
    .catch(err => {
      console.error('Failed to load order:', err)
      order.value = {}
    })

  axiosClient.get(`/orders/statuses`)
    .then(({ data }) => {
      orderStatuses.value = data
    })
    .catch(err => console.error(err))
})
function onStatusChange()
{
    axiosClient.post(`/orders/change-status/${order.value.id}/${order.value.status}`)
        .then(({data})=>{
            store.commit('showToast',`Status was successfully changed into "${order.value.status}"`)
        })
}
</script>
