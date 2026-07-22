import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: false,
        manifest: true,
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                css: 'resources/css/app.css'
            }
        }
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
