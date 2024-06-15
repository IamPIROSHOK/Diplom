import { createRouter, createWebHistory } from 'vue-router';
import AdminDashboard from './views/AdminDashboard.vue';
import store from './store';

const routes = [
    {
        path: '/admin',
        name: 'AdminDashboard',
        component: AdminDashboard,
        beforeEnter: (to, from, next) => {
            const userRole = store.state.user ? store.state.user.role : '';
            if (userRole !== 'admin') {
                window.location.href = '/'; // Перенаправляем пользователя на клиентское приложение
            } else {
                next();
            }
        },
    },
    // добавьте другие административные маршруты
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router;
