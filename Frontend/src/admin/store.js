import { createStore } from 'vuex';
import router from './router'; // Импортируем router для управления маршрутизацией

export default createStore({
    state: {
        isLoggedIn: !!localStorage.getItem('token'),
        user: JSON.parse(localStorage.getItem('user')) || null,
    },
    mutations: {
        LOGIN(state, user) {
            state.isLoggedIn = true;
            state.user = user;
        },
        LOGOUT(state) {
            state.isLoggedIn = false;
            state.user = null;
        }
    },
    actions: {
        login({ commit }, { token, user }) {
            localStorage.setItem('token', token);
            localStorage.setItem('user', JSON.stringify(user));
            commit('LOGIN', user);

            // Перенаправление на основе роли пользователя
            if (user.role === 'admin') {
                router.push('/admin');
            } else {
                window.location.href = '/';
            }
        },
        logout({ commit }) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            commit('LOGOUT');
            window.location.href = '/login'; // Перенаправление на страницу логина после выхода
        },
        initializeStore({ commit }) {
            const user = JSON.parse(localStorage.getItem('user'));
            if (localStorage.getItem('token') && user) {
                commit('LOGIN', user);
            } else {
                commit('LOGOUT');
            }
        },
    },
});
