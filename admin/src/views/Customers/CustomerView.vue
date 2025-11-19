<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold mb-6">
      {{ customer.id ? 'Edit Customer' : 'Add Customer' }}
    </h1>

    <form @submit.prevent="onSubmit" class="space-y-6">
      <!-- Basic Info -->
      <div class="grid grid-cols-2 gap-6">
        <CustomInput
          v-model="customer.first_name"
          label="First Name"
          placeholder="Enter first name"
        />
        <CustomInput
          v-model="customer.last_name"
          label="Last Name"
          placeholder="Enter last name"
        />
        <CustomInput
          v-model="customer.email"
          label="Email"
          type="email"
          placeholder="Enter email"
        />
        <CustomInput
          v-model="customer.phone"
          label="Phone"
          placeholder="Enter phone number"
        />
      </div>

      <!-- Shipping Address -->
      <div class="border-t pt-6">
        <h2 class="text-lg font-semibold mb-4">Shipping Address</h2>
        <div class="grid grid-cols-2 gap-6">
          <CustomInput
            v-model="customer.shippingAddress.address1"
            label="Address Line 1"
          />
          <CustomInput
            v-model="customer.shippingAddress.address2"
            label="Address Line 2"
          />
          <CustomInput v-model="customer.shippingAddress.city" label="City" />
          <CustomInput
            v-model="customer.shippingAddress.zip"
            label="Postal Code"
          />

          <CustomInput
            v-model="customer.shippingAddress.country_code"
            label="Country"
            :options="countries"
            type="select"
          />
          <CustomInput
            v-model="customer.shippingAddress.state"
            label="State"
            :options="shippingStateOptions"
            type="select"
          />
        </div>
      </div>

      <!-- Billing Address -->
      <div class="border-t pt-6">
        <h2 class="text-lg font-semibold mb-4">Billing Address</h2>
        <div class="grid grid-cols-2 gap-6">
          <CustomInput
            v-model="customer.billingAddress.address1"
            label="Address Line 1"
          />
          <CustomInput
            v-model="customer.billingAddress.address2"
            label="Address Line 2"
          />
          <CustomInput v-model="customer.billingAddress.city" label="City" />
          <CustomInput
            v-model="customer.billingAddress.zip"
            label="Postal Code"
          />

          <CustomInput
            v-model="customer.billingAddress.country_code"
            label="Country"
            :options="countries"
            type="select"
          />
          <CustomInput
            v-model="customer.billingAddress.state"
            label="State"
            :options="billingStateOptions"
            type="select"
          />
        </div>
      </div>

      <!-- Submit Buttons -->
      <div class="flex justify-end mt-8 gap-4">
        <button
          type="button"
          @click="closeModal"
          class="px-6 py-2 bg-gray-300 hover:bg-gray-400 rounded-lg text-gray-700"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg"
        >
          {{ loading ? 'Saving...' : customer.id ? 'Update' : 'Save' }}
        </button>
      </div>
    </form>
  </div>
</template>


<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import CustomInput from '../../components/core/CustomInput.vue'
import store from '../../store'

const router = useRouter()
const route = useRoute() // ✅ You were missing this
const loading = ref(false)

const customer = ref({
  billingAddress: {},
  shippingAddress: {},
})

const countries = computed(() =>
  store.state.countries.map((c) => ({ key: c.code, text: c.name }))
)

const billingCountry = computed(() =>
  store.state.countries.find(
    (c) => c.code == customer.value.billingAddress.country_code
  )
)
const shippingCountry = computed(() =>
  store.state.countries.find(
    (c) => c.code == customer.value.shippingAddress.country_code
  )
)

const billingStateOptions = computed(() => {
  if (!billingCountry.value || !billingCountry.value.states) return []
  let states = {}
  try {
    states = JSON.parse(billingCountry.value.states)
  } catch (e) {
    console.error('Failed to parse states JSON', e)
  }
  return Object.entries(states).map(([key, name]) => ({ key, text: name }))
})

const shippingStateOptions = computed(() => {
  if (!shippingCountry.value || !shippingCountry.value.states) return []
  let states = {}
  try {
    states = JSON.parse(shippingCountry.value.states)
  } catch (e) {
    console.error('Failed to parse states JSON', e)
  }
  return Object.entries(states).map(([key, name]) => ({ key, text: name }))
})

onMounted(() => {
  // ✅ Check if an ID is in the route (edit mode)
  if (route.params.id) {
    store
      .dispatch('getCustomer', route.params.id)
      .then(({ data }) => {
        customer.value = data
      })
      .catch((err) => console.error('Failed to load customer:', err))
  }
})

function onSubmit() {
  loading.value = true

  if (customer.value.id) {
    // ✅ Update existing customer
    customer.value.status = !!customer.value.status
    store
      .dispatch('updateCustomer', customer.value)
      .then(() => {
        loading.value = false
        store.dispatch('getCustomers')
        router.push({ name: 'app.customers' }) // ✅ FIXED: router not route
      })
      .catch((error) => {
        loading.value = false
        console.error(error.response?.data || error.message)
      })
  } else {
    // ✅ Create new customer
    store
      .dispatch('createCustomer', customer.value)
      .then(() => {
        loading.value = false
        store.dispatch('getCustomers')
        router.push({ name: 'app.customers' }) // ✅ FIXED: router not route
      })
      .catch((error) => {
        loading.value = false
        console.error(error.response?.data || error.message)
      })
  }
}

function closeModal() {
  router.push({ name: 'app.customers' })
}
</script>
