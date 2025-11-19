<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/75" />
            </TransitionChild>
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel
                            class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                        >
                            <Spinner v-if="loading" class="absolute bottom-0 left-0 right-0 top-0 flex items-center justify-center bg-white" />
                            <header class="flex items-center justify-between px-4 py-3">
                                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                    {{ customer.id ? 'Update customer: ' + customer.first_name : 'Create new Customer' }}
                                </DialogTitle>
                                <button
                                    @click="closeModal()"
                                    class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full transition-colors hover:bg-[rgba(0,0,0,0.2)]"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </header>
                            <form @submit.prevent="onSubmit" class="space-y-6">
                                <!-- Customer Info Section -->
                                <div class="rounded-xl border border-gray-100 bg-gray-50 p-6 shadow-sm">
                                    <h2 class="mb-4 flex items-center gap-2 text-xl font-semibold text-gray-800">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-indigo-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5.121 17.804A3 3 0 017 17h10a3 3 0 011.879.804M12 14a4 4 0 100-8 4 4 0 000 8z"
                                            />
                                        </svg>
                                        Customer Information
                                    </h2>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <CustomInput v-model="customer.first_name" label="First Name" />
                                        <CustomInput v-model="customer.last_name" label="Last Name" />
                                        <CustomInput v-model="customer.email" label="Email" />
                                        <CustomInput v-model="customer.phone" label="Phone" />
                                        <!-- Status Dropdown -->
                                        <CustomInput type="checkbox" class="mb-2" v-model="customer.status" label="Active"/>
                                    </div>
                                </div>

                                <!-- Addresses Section -->
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- Billing -->
                                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-6 shadow-sm">
                                        <h2 class="mb-4 flex items-center gap-2 text-xl font-semibold text-gray-800">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-blue-600"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 14l2-2 4 4m0 0l2-2m-6 2a9 9 0 11-9-9 9 9 0 019 9z"
                                                />
                                            </svg>
                                            Billing Address
                                        </h2>
                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <CustomInput v-model="customer.billingAddress.address1" label="Address 1" />
                                            <CustomInput v-model="customer.billingAddress.address2" label="Address 2" />
                                            <CustomInput v-model="customer.billingAddress.city" label="City" />
                                            <CustomInput v-model="customer.billingAddress.zipcode" label="Zip Code" />
                                            <CustomInput
                                                type="select"
                                                :select-options="countries"
                                                v-model="customer.billingAddress.country_code"
                                                label="Country"
                                            />
                                            <CustomInput
                                                v-if="!billingCountry || !billingCountry.states"
                                                v-model="customer.billingAddress.state"
                                                label="State"
                                            />
                                            <CustomInput
                                                v-else
                                                type="select"
                                                :select-options="billingStateOptions"
                                                v-model="customer.billingAddress.state"
                                                label="State"
                                            />
                                        </div>
                                    </div>

                                    <!-- Shipping -->
                                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-6 shadow-sm">
                                        <h2 class="mb-4 flex items-center gap-2 text-xl font-semibold text-gray-800">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-green-600"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 7l8.485 8.485a3 3 0 004.243 0L21 10"
                                                />
                                            </svg>
                                            Shipping Address
                                        </h2>
                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <CustomInput v-model="customer.shippingAddress.address1" label="Address 1" />
                                            <CustomInput v-model="customer.shippingAddress.address2" label="Address 2" />
                                            <CustomInput v-model="customer.shippingAddress.city" label="City" />
                                            <CustomInput v-model="customer.shippingAddress.zipcode" label="Zip Code" />
                                            <CustomInput
                                                type="select"
                                                :select-options="countries"
                                                v-model="customer.shippingAddress.country_code"
                                                label="Country"
                                            />
                                            <CustomInput
                                                v-if="!shippingCountry || !shippingCountry.states"
                                                v-model="customer.shippingAddress.state"
                                                label="State"
                                            />
                                            <CustomInput
                                                v-else
                                                type="select"
                                                :select-options="shippingStateOptions"
                                                v-model="customer.shippingAddress.state"
                                                label="State"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <footer class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                                    <button
                                        type="submit"
                                        class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all duration-200 hover:bg-indigo-500"
                                    >
                                        {{ customer.id ? 'Update Customer' : 'Create Customer' }}
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-100"
                                        @click="closeModal"
                                        ref="cancelModelRef"
                                    >
                                        Cancel
                                    </button>
                                </footer>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { computed, onUpdated, ref } from 'vue';
import CustomInput from '../../components/core/CustomInput.vue';
import Spinner from '../../components/core/Spinner.vue';
import store from '../../store';

const loading = ref(false);

const props = defineProps({
    modelValue: Boolean,
    customer: {
        required: true,
        type: Object,
    },
});

const customer = ref({
    billingAddress: {},
    shippingAddress: {},
});

const emit = defineEmits(['update:modelValue', 'close']);

const show = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    },
});

const countries = computed(() => store.state.countries.map((c) => ({ key: c.code, text: c.name })));

const billingCountry = computed(() => store.state.countries.find((c) => c.code == customer.value.billingAddress.country_code));
const shippingCountry = computed(() => store.state.countries.find((c) => c.code == customer.value.shippingAddress.country_code));

const billingStateOptions = computed(() => {
    if (!billingCountry.value || !billingCountry.value.states) return [];

    let states = {};
    try {
        states = JSON.parse(billingCountry.value.states);
    } catch (e) {
        console.error('Failed to parse states JSON', e);
    }

    return Object.entries(states).map(([key, name]) => ({ key, text: name }));
});

const shippingStateOptions = computed(() => {
    if (!shippingCountry.value || !shippingCountry.value.states) return [];

    let states = {};
    try {
        states = JSON.parse(shippingCountry.value.states);
    } catch (e) {
        console.error('Failed to parse states JSON', e);
    }

    return Object.entries(states).map(([key, name]) => ({ key, text: name }));
});

onUpdated(() => {
    customer.value = {
        id: props.customer.id,
        first_name: props.customer.first_name,
        last_name: props.customer.last_name,
        email: props.customer.email,
        phone: props.customer.phone,
        status: props.customer.status,
        billingAddress: {
            ...props.customer.billingAddress,
        },
        shippingAddress: {
            ...props.customer.shippingAddress,
        },
    };
});

function closeModal() {
    show.value = false;
    emit('close');
}

function onSubmit() {
    loading.value = true;

    if (customer.value.id) {
        customer.value.status = !!customer.value.status;
        store
            .dispatch('updateCustomer', customer.value)
            .then(() => {
                loading.value = false;
                store.dispatch('getCustomers');
                closeModal();
            })
            .catch((error) => {
                loading.value = false;
                console.error(error.response?.data || error.message);
            });
    } else {
        store
            .dispatch('createCustomer', customer.value)
            .then(() => {
                loading.value = false;
                store.dispatch('getCustomers');
                closeModal();
            })
            .catch((error) => {
                loading.value = false;
                console.error(error.response?.data || error.message);
            });
    }
}
</script>

<style></style>
