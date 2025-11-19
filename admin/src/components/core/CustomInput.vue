<template>
    <div>
        <label class="sr-only">{{ label }}</label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <span
                v-if="prepend"
                class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500"
            >
                {{ prepend }}
            </span>



            <template v-if="type === 'select'">
                <select
                    :name="name"
                    :required="required"
                    :value="modelValue"
                    @change="onChange($event.target.value)"
                    :class="inputClasses"
                >
                    <option v-for="option of selectOptions" :value="option.key">{{ option.text }}</option>
                </select>
            </template>

            <template v-else-if="type === 'textarea'">
                <textarea
                    :name="name"
                    :required="required"
                    :value="modelValue"
                    @input="emit('update:modelValue', $event.target.value)"
                    :class="inputClasses"
                    :placeholder="label"
                ></textarea>
            </template>

            <template v-else-if="type === 'file'">
                <input :type="type" :name="name" :required="required" @input="emit('change', $event.target.files[0])" :class="inputClasses" />
            </template>

            <template v-else-if="type === 'checkbox'">
                <input
                    :id="id"
                    :name="name"
                    :type="type"
                    :required="required"
                    :checked="props.modelValue"
                    @change="emit('update:modelValue', $event.target.checked)"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <label :for="id" class="ml-2 block text-sm text-gray-900">{{ label }}</label>
            </template>

            <template v-else>
                <input
                    :type="type"
                    :name="name"
                    :required="required"
                    :value="modelValue"
                    @input="emit('update:modelValue', $event.target.value)"
                    :class="inputClasses"
                    :placeholder="label"
                    :step="0.01"
                />
            </template>

            <span v-if="append" class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">
                {{ append }}
            </span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

// consuming the ascending order of the products
const props = defineProps({
    modelValue: [String, Number, File],
    label: String,
    type: {
        type: String,
        default: 'text',
    },
    name: String,
    required: Boolean,
    prepend: {
        type: String,
        default: '',
    },
    append: {
        type: String,
        default: '',
    },
    selectOptions: Array
});

const id = computed(() => {
    if (props.id) return props.id;

    return `id-${Math.floor(1000000 + Math.random() * 1000000)}`;
});

const emit = defineEmits(['update:modelValue', 'change']);

const inputClasses = computed(() => {
    const cls = [
        `block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm`,
    ];

    if (props.append && !props.prepend) {
        cls.push(`rounded-l-md`);
    } else if (props.prepend && !props.append) {
        cls.push(`rounded-r-md`);
    } else if (!props.prepend && !props.append) {
        cls.push(`rounded-md`);
    }
    return cls.join(' ');
});

function onChange(value){
    emit('update:modelValue',value)
    emit('change',value)
}
</script>
