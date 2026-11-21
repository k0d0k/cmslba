import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                // Pustaka ikon dipisah agar chunk utama tetap ringan & mudah di-cache.
                manualChunks(id) {
                    if (id.includes('node_modules/lucide')) {
                        return 'lucide';
                    }
                },
            },
        },
    },
});
