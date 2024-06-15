// src/main.js

import { createApp } from 'vue';
import App from './App.vue';
import store from './store'; // Vuex store
import router from './router';

// Import Bootstrap CSS and JS
// import 'bootstrap/dist/css/bootstrap.min.css';
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Import custom CSS files if any
// import './assets/css/style.css'
// ;
console.log('ADMIN Mounting client app'); // Отладочное сообщение

import 'swiper/swiper-bundle.css';
import './assets/css/libs.bundle.css';
import './assets/css/theme.bundle.css';

createApp(App)
    .use(router)
    .use(store)
    .mount('#client-app');

console.log('ADMIN Client app mounted'); // Отладочное сообщение


