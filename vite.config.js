import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/admin.scss',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: [
                    'import',
                    'global-builtin',
                    'color-functions',
                ],
                quietDeps: true,
            },
        },
    },
    resolve: {
        alias: {
            '@fortawesome/fontawesome-free': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free'),
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
