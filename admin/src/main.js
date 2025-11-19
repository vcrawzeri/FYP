import { createApp } from 'vue';
// import './style.css'
import App from './App.vue';
import './assets/tailwind.css';
import store from './store';
import router from './router';

createApp(App).use(store).use(router).mount('#app');

// custom input need to be changed
