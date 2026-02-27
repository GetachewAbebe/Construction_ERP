// vite.config.mjs
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        entryFileNames: 'assets/app-main.js',
        assetFileNames: 'assets/app-main.[ext]',
      },
    },
  },
  plugins: [
    laravel({
      input: ['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/erp-premium.css', 'resources/js/erp-premium.js'],
      refresh: true,
    }),
  ],
  css: {
    preprocessorOptions: {
      scss: {
        // Quiet Bootstrap’s Sass deprecation warnings
        quietDeps: true,
      },
    },
  },
})
