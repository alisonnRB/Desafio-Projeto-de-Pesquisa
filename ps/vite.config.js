import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/login.css',
                'resources/css/login_denied.css',
                'resources/css/home.css',
                'resources/js/login.js',
                'resources/js/login_denied.js',
                'resources/js/home.js',

            ],
            refresh: true,
        }),
    ],
});
