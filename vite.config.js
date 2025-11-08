import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        cors: {
            origin: [
                'http://localhost:8000',
                'https://4m445w5p-8000.asse.devtunnels.ms',
                // 'https://fxkkrc1l-5173.asse.devtunnels.ms',
                // 'https://e080e8c99f28.ngrok-free.app',
                'https://da3a359b92ef.ngrok-free.app/p'
            ],
            methods: ['GET', 'HEAD', 'POST', 'OPTIONS'],
            credentials: true,
        }, 
        hmr: {
            // host: 'localhost:5173',
            host: 'fxkkrc1l-5173.asse.devtunnels.ms',
            protocol: 'wss',
            clientPort: 443,
        },
    }
})