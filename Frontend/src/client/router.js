import { createRouter, createWebHistory } from 'vue-router';

import Home from './views/Home.vue';
import Masters from './views/Masters.vue';
import Services from './views/Services.vue';
import Appointments from './views/Appointments.vue';
import Login from './components/Auth/Login.vue';
import Register from './components/Auth/Register.vue';
import store from './store';

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/masters', name: 'Masters', component: Masters },
    { path: '/services', name: 'Services', component: Services },
    { path: '/appointments', name: 'Appointments', component: Appointments },
    { path: '/login', name: 'Login', component: Login },
    { path: '/register', name: 'Register', component: Register },
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

router.beforeEach((to, from, next) => {
    const isLoggedIn = store.state.isLoggedIn;
    const userRole = store.state.user ? store.state.user.role : '';

    if (!isLoggedIn && to.path !== '/login' && to.path !== '/register') {
        next('/login');
    } else if (isLoggedIn && userRole !== 'admin' && to.path.startsWith('/admin')) {
        next('/');
    } else {
        next();
    }
});

export default router;
