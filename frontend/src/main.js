import { createApp } from 'vue'
import { createPinia } from 'pinia'
import '@fontsource-variable/fraunces'
import '@fontsource-variable/manrope'
import './assets/styles.css'
import App from './App.vue'
import router from './router'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
