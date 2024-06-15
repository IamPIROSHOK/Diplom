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

import './assets/css/style.css';
// import './assets/js/main.js';

export function loadScript(src, type = 'text/javascript', nomodule = false) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.type = type;
        if (nomodule) {
            script.setAttribute('nomodule', '');
        }
        script.onload = () => resolve();
        script.onerror = () => reject(new Error(`Failed to load script ${src}`));
        document.head.appendChild(script);
    });
}

// Загружаем внешние скрипты
loadScript('https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js', 'module')
    .then(() => console.log('Module script loaded successfully'))
    .catch(error => console.error('Error loading module script:', error));

loadScript('https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js', 'text/javascript', true)
    .then(() => console.log('NoModule script loaded successfully'))
    .catch(error => console.error('Error loading nomodule script:', error));

createApp(App)
    .use(router)
    .use(store)
    .mount('#admin-app');

console.log('ADMIN Client app mounted'); // Отладочное сообщение
