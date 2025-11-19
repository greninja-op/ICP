import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3002,
    strictPort: false,
    host: 'localhost',
    open: false,
    cors: true,
    hmr: {
      overlay: true
    }
  },
  optimizeDeps: {
    include: ['react', 'react-dom', 'react-router-dom', 'motion']
  }
})
