import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/login.css',
                'resources/css/login_denied.css',  // ou outro arquivo CSS
                'resources/js/login.js',
                'resources/js/login_denied.js',  // ou outro arquivo JS
            ],
            refresh: true,
        }),
    ],
});
