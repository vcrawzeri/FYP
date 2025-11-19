import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],

    server: {
        watch: {
            ignored: ['**/storage/**', '**/vendor/**', '**/public/build/**'],
        },
        port: 3001, // keep same port you are using
    },
});
