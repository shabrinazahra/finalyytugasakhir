import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/pages/login.js",
                "resources/js/pages/users/create.js",
                "resources/js/pages/users/edit.js",
                "resources/js/pages/perhitungan-ahp.js",
                "resources/js/pages/kategori-penilaian-create.js",
            ],
            refresh: true,
        }),
    ],
    server: {
        host: "127.0.0.1",
    },
});
