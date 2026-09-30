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
    server: {
        // Wajib IPv4 eksplisit. Default Vite menulis public/hot berisi
        // "http://[::1]:5173" (IPv6). Kalau IPv6 mati/diblokir di browser atau
        // OS, @vite gagal ambil CSS/JS dan seluruh halaman tampil putih.
        host: '127.0.0.1',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
