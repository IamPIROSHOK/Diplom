import { createRouter, createWebHistory } from 'vue-router';

import AdminDashboard from './views/AdminDashboard.vue';
import Clients from './views/Clients.vue';
import Masters from './views/Masters.vue';
import Appointments from './views/Appointments.vue';
import Discounts from './views/Discounts.vue';
import Chats from './views/Chats.vue';
import Logout from './views/Logout.vue';

const routes = [
    { path: '/admin', name: 'AdminDashboard', component: AdminDashboard },
    { path: '/admin/clients', name: 'Clients', component: Clients },
    { path: '/admin/masters', name: 'Masters', component: Masters },
    { path: '/admin/appointments', name: 'Appointments', component: Appointments },
    { path: '/admin/discounts', name: 'Discounts', component: Discounts },
    { path: '/admin/chats', name: 'Chats', component: Chats },
    { path: '/admin/logout', name: 'Logout', component: Logout },
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

export default router;
