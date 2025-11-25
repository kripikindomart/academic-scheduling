import { createApp, h } from 'vue'
import { createPinia } from 'pinia'
import { createInertiaApp } from '@inertiajs/vue3'
import '../css/app.css'

createInertiaApp({
  title: (title) => `${title} - Academic Scheduling System`,
  resolve: (name) => import(`./Pages/${name}.vue`),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    app.use(createPinia())
    app.use(plugin)

    app.mount(el)
  },
  progress: {
    color: '#3b82f6',
  },
})
