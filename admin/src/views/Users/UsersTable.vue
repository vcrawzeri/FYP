<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <!-- Users Card -->
        <div class="rounded-2xl bg-white p-6 shadow-md">
            <!-- Top Controls: Per Page + Search -->
            <div class="mb-4 flex justify-between border-b-2 pb-3">
                <div class="flex items-center gap-3">
                    <span class="whitespace-nowrap font-medium">Per page</span>
                    <select
                        v-model="perPage"
                        @change="getUsers(null)"
                        class="w-24 rounded-md border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div>
                    <input
                        v-model="search"
                        @input="getUsers(null)"
                        class="w-64 rounded-md border-gray-300 px-3 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Type to search users"
                    />
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <TableHeading
                                @click="sortUser('id')"
                                field="id"
                                :sort-field="sortField"
                                :sort-direction="sortDirection"
                                class="p-2 text-left font-medium text-gray-700"
                            >
                                ID
                            </TableHeading>
                            <TableHeading
                                @click="sortUser('name')"
                                field="name"
                                :sort-field="sortField"
                                :sort-direction="sortDirection"
                                class="p-2 text-left font-medium text-gray-700"
                            >
                                Name
                            </TableHeading>
                            <TableHeading
                                @click="sortUser('email')"
                                field="email"
                                :sort-field="sortField"
                                :sort-direction="sortDirection"
                                class="p-2 text-left font-medium text-gray-700"
                            >
                                Email
                            </TableHeading>
                            <TableHeading
                                @click="sortUser('created_at')"
                                field="created_at"
                                :sort-field="sortField"
                                :sort-direction="sortDirection"
                                class="p-2 text-left font-medium text-gray-700"
                            >
                                Create Date
                            </TableHeading>
                            <TableHeading field="actions" class="p-2 font-medium text-gray-700"> Actions </TableHeading>
                        </tr>
                    </thead>

                    <!-- Loading State -->
                    <tbody v-if="users.loading">
                        <tr>
                            <td colspan="6" class="py-6 text-center">
                                <Spinner></Spinner>
                            </td>
                        </tr>
                    </tbody>

                    <!-- Users Rows -->
                    <tbody v-else>
                        <tr v-for="user in users.data" :key="user.id" class="border-b transition-colors hover:bg-gray-50">
                            <td class="p-2">{{ user.id }}</td>
                            <td class="p-2">
                                {{ user.name }}
                            </td>
                            <td class="max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap p-2">
                                {{ user.email }}
                            </td>
                            <td class="p-2">{{ user.created_at }}</td>
                            <td class="p-2">
                                <Menu as="div" class="relative inline-block text-left">
                                    <div>
                                        <MenuButton
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-full text-sm font-medium hover:bg-gray-200 focus:outline-none"
                                        >
                                            <DotsVerticalIcon class="h-5 w-5 text-black" aria-hidden="true" />
                                        </MenuButton>
                                    </div>
                                    <transition
                                        enter-active-class="transition duration-100 ease-out"
                                        enter-from-class="transform scale-95 opacity-0"
                                        enter-to-class="transform scale-100 opacity-100"
                                        leave-active-class="transition duration-75 ease-in"
                                        leave-from-class="transform scale-100 opacity-100"
                                        leave-to-class="transform scale-95 opacity-0"
                                    >
                                        <MenuItems
                                            class="absolute right-0 z-10 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                        >
                                            <div class="px-1 py-1">
                                                <MenuItem v-slot="{ active }">
                                                    <button
                                                        :class="[
                                                            active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                                                            'flex w-full items-center rounded-md px-2 py-2 text-sm',
                                                        ]"
                                                        @click="editUser(user)"
                                                    >
                                                        <PencilIcon :class="[active ? 'text-white' : 'text-black', 'mr-2 h-5 w-5']" />
                                                        Edit
                                                    </button>
                                                </MenuItem>

                                                <MenuItem v-slot="{ active }">
                                                    <button
                                                        :class="[
                                                            active ? 'bg-red-600 text-white' : 'text-gray-900',
                                                            'flex w-full items-center rounded-md px-2 py-2 text-sm',
                                                        ]"
                                                        @click="deleteUser(user)"
                                                    >
                                                        <TrashIcon :class="[active ? 'text-white' : 'text-black', 'mr-2 h-5 w-5']" />
                                                        Delete
                                                    </button>
                                                </MenuItem>
                                            </div>
                                        </MenuItems>
                                    </transition>
                                </Menu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="!users.loading" class="mt-5 flex items-center justify-between">
                <span> Showing from {{ users.from }} to {{ users.to }} </span>
                <nav
                    v-if="users.total > users.limit"
                    class="relative z-0 inline-flex justify-center -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <a
                        v-for="(link, i) in users.links"
                        :key="i"
                        href="#"
                        @click.prevent="getForPage($event, link)"
                        v-html="link.label"
                        class="relative inline-flex items-center whitespace-nowrap border px-4 py-2 text-sm font-medium"
                        :class="[
                            link.active
                                ? 'z-10 border-indigo-500 bg-indigo-50 text-indigo-600'
                                : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50',
                            i === 0 ? 'rounded-l-md' : '',
                            i === users.links.length - 1 ? 'rounded-r-md' : '',
                            !link.url ? 'bg-gray-100 text-gray-700' : '',
                        ]"
                    ></a>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { DotsVerticalIcon, PencilIcon, TrashIcon } from '@heroicons/vue/outline';
import { computed, onMounted, ref } from 'vue';
import Spinner from '../../components/core/Spinner.vue';
import TableHeading from '../../components/core/Table/TableHeading.vue';
import { USERS_PER_PAGE } from '../../constants';
import store from '../../store';

const emit = defineEmits(['clickEdit']);
const perPage = ref(USERS_PER_PAGE);
const search = ref('');
const users = computed(() => store.state.users);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

onMounted(() => {
    getUsers();
});

function getUsers(url = null) {
    console.log('🔍 Searching users with:', {
        search: search.value,
        sortField: sortField.value,
        sortDirection: sortDirection.value,
        perPage: perPage.value
    });

    store.dispatch('getUsers', {
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
    getUsers(link.url);
}

function sortUser(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    getUsers();
}

function editUser(user) {
    emit('clickEdit', user);
}

function deleteUser(user) {
    if (!confirm('Are you sure you want to delete this user?')) return;
    store.dispatch('deleteUser', user.id).then(() => {
        store.dispatch('getUsers');
    });
}
</script>
