import { defineConfig } from 'vite'
import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import typo3 from 'vite-plugin-typo3'
import autoOrigin from 'vite-plugin-auto-origin'

const currentDir = dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  plugins: [
    typo3(),
    autoOrigin(),
  ],
  build: {
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(currentDir, 'Resources/Private/JavaScript/main.js'),
      },
    },
    outDir: resolve(currentDir, 'Resources/Public/Vite/'),
  },
  css: {
    devSourcemap: true,
  },
  publicDir: false,
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    cors: true,
    allowedHosts: ['kfv-freising.ddev.site'],
    origin: 'https://kfv-freising.ddev.site:5173',
    watch: {
      usePolling: true,
      interval: 300,
    },
  },
})
