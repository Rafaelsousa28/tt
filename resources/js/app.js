import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router/index.js'
import App from './App.vue'
import axios from 'axios'
import '../css/app.css'

axios.defaults.baseURL = '/api'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.config.globalProperties.$axios = axios

app.mount('#app')
