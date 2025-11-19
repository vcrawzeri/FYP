<template>
    <div class="mb-3 flex items-center justify-between">
        <h1 class="text-3xl font-semibold">Users</h1>
        <button
            @click="showUserModal"
            type="submit"
            class="py-2 px-4 text-sm font-medium rounded-md text-white bg-indigo-600 flex justify-center border border-transparent"
        >
            Add new user
        </button>
    </div>
    <UserModal v-model="showModel" :user="userModal" @close="onModalClose"></UserModal>
    <UsersTable @clickEdit="editUser"></UsersTable>
</template>

<script setup>
import { ref } from 'vue';
import UserModal from './UserModal.vue';
import UsersTable from './UsersTable.vue';
import store from '../../store';

const DEFAULT_EMPTY_OBJECT = {
    id: '',
    title: '',
    image: '',
    description: '',
    price: '',
}

const showModel = ref(false);
const userModal = ref({ ...DEFAULT_EMPTY_OBJECT });

function showUserModal() {
    showModel.value = true;
}

function editUser(u) {
    store.dispatch('getUser', u.id)
        .then(response => {
            // API returns the user resource in response.data
            userModal.value = response.data;
            showUserModal();
        })
        .catch(err => {
            console.error('Failed to fetch user', err);
            // fallback to the provided object if you want:
            userModal.value = u;
            showUserModal();
        });
}

function onModalClose() {
    userModal.value = { ...DEFAULT_EMPTY_OBJECT };
}
</script>

<style></style>
