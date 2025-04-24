import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/login.css',  // ou outro arquivo CSS
                'resources/js/login.js'     // ou outro arquivo JS
            ],
            refresh: true,
        }),
    ],
});
