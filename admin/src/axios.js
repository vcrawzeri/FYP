import axios from 'axios';
import router from './router';
import store from './store';
//import { error } from "console";
//import { response } from "express";
//import { config } from "process";

const axiosClient = axios.create({
    baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`,
});

axiosClient.interceptors.request.use((config) => {
    config.headers.Authorization = `Bearer ${store.state.user.token}`;
    return config;
});

axiosClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response && error.response.status === 401) {
            sessionStorage.removeItem('TOKEN');
            store.commit('setToken', null);
            store.commit('setUser', null);
            router.push({ name: 'login' });
        }

        console.error(error);
        return Promise.reject(error); // ✅ important! but with all their fuatls well if later we use it
    },
);

export default axiosClient;
