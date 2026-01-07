<template>
  <div class="p-6 bg-gray-50 min-h-screen">
    <!-- Customers Card -->
    <div class="bg-white rounded-2xl shadow-md p-6">
      <!-- Top Controls: Per Page + Search -->
      <div class="pb-3 flex justify-between border-b-2 mb-4">
        <div class="flex items-center gap-3">
          <span class="whitespace-nowrap font-medium">Per page</span>
          <select
            v-model="perPage"
            @change="getCustomers(null)"
            class="w-24 px-3 py-2 border-gray-300 text-gray-900 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
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
            @change="getCustomers(null)"
            class="w-64 px-3 py-2 border-gray-300 text-gray-900 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            placeholder="Type to search customers"
          />
        </div>
      </div>

      <!-- Customers Table -->
      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
          <thead class="bg-gray-100">
            <tr>
              <TableHeading
                @click="sortCustomers('id')"
                field="id"
                :sort-field="sortField"
                :sort-direction="sortDirection"
                class="p-2 text-left font-medium text-gray-700"
              >
                ID
              </TableHeading>
              <TableHeading
                @click="sortCustomers('name')"
                field="name"
                :sort-field="sortField"
                :sort-direction="sortDirection"
                class="p-2 text-left font-medium text-gray-700"
              >
                Name
              </TableHeading>
              <TableHeading
                @click="sortCustomers('email')"
                field="email"
                :sort-field="sortField"
                :sort-direction="sortDirection"
                class="p-2 text-left font-medium text-gray-700"
              >
                Email
              </TableHeading>
              <TableHeading
                @click="sortCustomers('phone')"
                field="phone"
                :sort-field="sortField"
                :sort-direction="sortDirection"
                class="p-2 text-left font-medium text-gray-700"
              >
                Phone
              </TableHeading>
              <TableHeading
                @click="sortCustomers('status')"
                field="status"
                :sort-field="sortField"
                :sort-direction="sortDirection"
                class="p-2 text-left font-medium text-gray-700"
              >
                Status
              </TableHeading>
              <TableHeading
                @click="sortCustomers('created_at')"
                field="created_at"
                :sort-field="sortField"
                :sort-direction="sortDirection"
                class="p-2 text-left font-medium text-gray-700"
              >
                Date
              </TableHeading>
              <TableHeading field="actions" class="p-2 font-medium text-gray-700">
                Actions
              </TableHeading>
            </tr>
          </thead>

          <!-- Loading State -->
          <tbody v-if="customers.loading">
            <tr>
              <td colspan="6" class="text-center py-6">
                <Spinner></Spinner>
              </td>
            </tr>
          </tbody>

          <!-- Customers Rows -->
          <tbody v-else>
            <tr
              v-for="customer in customers.data"
              :key="customer.id"
              class="border-b hover:bg-gray-50 transition-colors"
            >
              <td class="p-2">{{ customer.id }}</td>
              <td class="p-2">
                {{ customer.first_name }} {{ customer.last_name }}
              </td>
              <td class="p-2 max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap">
                {{ customer.email }}
              </td>
              <td class="p-2">{{ customer.phone }}</td>
               <td class="p-2">{{ customer.status }}</td>
                <td class="p-2">{{ customer.created_at }}</td>
              <td class="p-2">
                <Menu as="div" class="relative inline-block text-left">
                  <div>
                    <MenuButton
                      class="h-10 w-10 text-sm font-medium inline-flex items-center justify-center rounded-full hover:bg-gray-200 focus:outline-none"
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
                      class="absolute right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                    >
                      <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                          <button
                            :class="[
                              active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                              'flex items-center w-full px-2 py-2 text-sm rounded-md',
                            ]"
                            @click="editCustomer(customer)"
                          >
                            <PencilIcon
                              :class="[active ? 'text-white' : 'text-black', 'mr-2 h-5 w-5']"
                            />
                            Edit
                          </button>
                        </MenuItem>

                        <MenuItem v-slot="{ active }">
                          <button
                            :class="[
                              active ? 'bg-red-600 text-white' : 'text-gray-900',
                              'flex items-center w-full px-2 py-2 text-sm rounded-md',
                            ]"
                            @click="delelteCustomer(customer)"
                          >
                            <TrashIcon
                              :class="[active ? 'text-white' : 'text-black', 'mr-2 h-5 w-5']"
                            />
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
      <div v-if="!customers.loading" class="mt-5 flex items-center justify-between">
        <span> Showing from {{ customers.from }} to {{ customers.to }} </span>
        <nav
          v-if="customers.total > customers.limit"
          class="rounded-md shadow-sm relative z-0 inline-flex justify-center -space-x-px"
          aria-label="Pagination"
        >
          <a
            v-for="(link, i) in customers.links"
            :key="i"
            href="#"
            @click.prevent="getForPage($event, link)"
            v-html="link.label"
            class="px-4 py-2 text-sm font-medium relative inline-flex items-center border whitespace-nowrap"
            :class="[
              link.active
                ? 'bg-indigo-50 border-indigo-500 text-indigo-600 z-10'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              i === 0 ? 'rounded-l-md' : '',
              i === customers.links.length - 1 ? 'rounded-r-md' : '',
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
const customers = computed(() => store.state.customers);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

onMounted(() => {
    getCustomers();
});

// I NEED TO ADD HERO ICON

function getCustomers(url = null) {
    store.dispatch('getCustomers', {
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
    getCustomers(link.url);
}

function sortCustomers(field) {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    getCustomers();
}

function editCustomer(customer) {
    emit('clickEdit', customer);
}

function delelteCustomer(customer) {
    if (!confirm('Are you sure you want to delete this customer?')) return;
    store.dispatch('deleteCustomer', customer.id).then(() => {
        store.dispatch('getCustomers');
    });
}
</script>
