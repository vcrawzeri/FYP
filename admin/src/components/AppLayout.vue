<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import store from '../store';
import Sidebar from './Sidebar.vue';
import TopHeader from './TopHeader.vue';
import Spinner from '../components/core/Spinner.vue';
import Toast from './core/Toast.vue';

const { title } = defineProps({
  title: String,
});

const sidebarOpened = ref(true);
const CurrentUser = computed(() => store.state.user.data);

function toggleSidebar() {
  sidebarOpened.value = !sidebarOpened.value;
}

onMounted(() => {
  store.dispatch('getCurrentUser');
  store.dispatch('getCountries');
  handleSidebarOpened();
  window.addEventListener('resize', handleSidebarOpened);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleSidebarOpened);
});

function handleSidebarOpened() {
  sidebarOpened.value = window.outerWidth > 768;
}
</script>

<template>
  <div v-if="CurrentUser.id" class="flex min-h-screen bg-gray-200">
    <!-- ✅ Sticky Sidebar -->
    <div
      class="sticky top-0 h-screen flex-shrink-0"
      :class="{ '-ml-[200px]': !sidebarOpened }"
    >
      <Sidebar class="h-full" />
    </div>

    <!-- ✅ Main Content Area -->
    <div class="flex flex-1 flex-col min-h-screen overflow-y-auto">
      <!-- ✅ Sticky Top Header -->
      <div class="sticky top-0 z-50 shadow-md">
        <TopHeader @toggle-sidebar="toggleSidebar" />
      </div>

      <!-- ✅ Scrollable Page Content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <div class="p-4 bg-white rounded-2xl shadow-sm">
          <router-view />
        </div>
      </main>
    </div>
  </div>

  <!-- Loading Spinner -->
  <div v-else class="bg-gray-200 flex min-h-screen items-center justify-center">
    <Spinner />
  </div>

  <Toast />
</template>
