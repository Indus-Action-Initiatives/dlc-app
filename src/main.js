import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './assets/main.css';


import App from './App.vue'
import router from './router'

// Vuetify setup
import 'vuetify/styles' // Import Vuetify CSS
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import { mdi } from 'vuetify/iconsets/mdi'
import { aliases } from 'vuetify/iconsets/mdi'

import '@mdi/font/css/materialdesignicons.css'

import VueGoogleMaps from '@fawmi/vue-google-maps'

// const vuetify = createVuetify({
//     components,
//     directives,
//     icons: {
//         defaultSet: 'mdi',
//         aliases,
//         sets: { mdi },
//     },
//     theme: {
//         defaultTheme: 'light',
//     },
// })


const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: { mdi },
    },
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                // colors: {
                //     primary: '#FF6D00',        // Vibrant orange for primary actions
                //     secondary: '#FFC107',      // Amber for secondary elements
                //     background: '#FFF8F1',     // Soft cream/light orange background
                //     surface: '#FFFFFF',        // White for cards, surfaces
                //     error: '#D32F2F',          // Dark red for errors
                //     info: '#0288D1',           // Deep sky blue for info
                //     success: '#388E3C',        // Dark green for success
                //     warning: '#F57C00',        // Deep orange for warnings
                //     accent: '#FFAB40'           // Extra accent, lighter orange
                // }
                // colors: {
                //     primary: '#FF6D00',        // Vibrant Orange - main actions
                //     secondary: '#FAD7A0',      // Light Orange - secondary elements
                //     background: '#FFF5E6',     // Very Light Orange - soft backdrop
                //     surface: '#FFFFFF',         // White - cards, modals, etc.
                //     error: '#B00020',           // Red - errors/alerts
                //     info: '#2196F3',            // Blue - informational messages
                //     success: '#4CAF50',         // Green - success messages
                //     warning: '#FB8C00',         // Darker orange for warnings
                //     accent: '#FF8C42'           // Lighter orange - highlights/buttons
                // }
                colors: {
                    primary: '#1976D2',        // Bright Royal Blue - main buttons / highlights
                    secondary: '#BBDEFB',      // Light Blue - for secondary UI elements
                    background: '#E3F2FD',     // Very light sky blue - soft page background
                    surface: '#FFFFFF',        // White - for cards, modals, and sections
                    error: '#D32F2F',          // Vibrant Red - error / alert messages
                    info: '#0288D1',           // Deep Sky Blue - for info or links
                    success: '#2E7D32',        // Medium Green - success states
                    warning: '#FBC02D',        // Amber Yellow - warnings or attention highlights
                    accent: '#42A5F5'          // Sky Blue - accent elements, hover states, links
                }
            },
        },
    },
})
const app = createApp(App)

app.use(createPinia())
app.use(router)

app.use(vuetify)

app.use(VueGoogleMaps, {
    load: {
        key: 'YOUR_GOOGLE_MAPS_API_KEY',
    },
})


app.mount('#app')
