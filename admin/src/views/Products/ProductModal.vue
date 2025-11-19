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
                                    {{ product.id ? 'Update product: ' + product.title : 'Create new Product' }}
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
                            <form @submit.prevent="onSubmit">
                                <div class="bg-white px-4 pb-4 pt-5">
                                    <CustomInput class="mb-2" v-model="product.title" label="Product Title" />
                                    <CustomInput type="file" class="mb-2" label="Product Image" @change="(file) => (product.image = file)" />
                                    <CustomInput type="textarea" class="mb-2" v-model="product.description" label="Description" />
                                    <CustomInput type="number" class="mb-2" v-model="product.price" label="Price" prepend="$" />
                                    <CustomInput type="checkbox" class="mb-2" v-model="product.published" label="Published" />
                                </div>
                                <footer class="flex flex-row-reverse gap-2 bg-gray-50 px-4 py-3 sm:px-6">
                                    <button
                                        type="submit"
                                        class="rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-400"
                                    >
                                        Submit
                                    </button>
                                    <button
                                        type="button"
                                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 shadow-sm"
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
    product: {
        required: true,
        type: Object,
    },
});

const product = ref({
    id: props.product.id,
    title: props.product.title,
    image: props.product.image,
    description: props.product.description,
    price: props.product.price,
    published: props.product.published,
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

onUpdated(() => {
    product.value = {
        id: props.product.id,
        title: props.product.title,
        image: props.product.image,
        description: props.product.description,
        price: props.product.price,
        published: props.product.published,
    };
});

function closeModal() {
    show.value = false;
    emit('close');
}

function onSubmit() {
    loading.value = true;
    if (product.value.id) {
        store.dispatch('updateProduct', product.value).then((response) => {
            loading.value = false;
            if (response.status === 200) {
                //show notification
                store.dispatch('getProducts');
                closeModal();
            }
        });
    } else {
        store.dispatch('createProduct', product.value).then((response) => {
            loading.value = false;
            if (response === 201) {
                //show notification
                store.dispatch('getProducts');
                closeModal();
            }
        });
    }
}
</script>

<style></style>
