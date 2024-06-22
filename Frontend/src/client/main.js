import { createApp } from 'vue';
import App from './App.vue';
import store from './store';
import router from './router';


import 'swiper/swiper-bundle.css';
import './assets/css/libs.bundle.css';
import './assets/css/theme.bundle.css';

createApp(App)
    .use(router)
    .use(store)
    .mount('#client-app');



