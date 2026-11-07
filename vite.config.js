import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/urun.js'],
            refresh: true,
            /*
             * Fontlar derleme aninda indirilip kendi sunucumuzdan servis edilir.
             * Calisma aninda hicbir dis istege cikilmaz (gizlilik + hiz).
             *
             * Lato govde, Poppins baslik — schaferkibris.com (Basel) ile ayni.
             */
            fonts: [
                bunny('Lato', { weights: [400, 700, 900] }),
                bunny('Poppins', { weights: [400, 500, 600, 700] }),
            ],
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
