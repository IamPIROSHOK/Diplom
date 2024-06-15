<!-- src/components/Navbar.vue -->

<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">

      <!-- Brand -->
      <router-link class="navbar-brand" to="/">
        Shopper.
      </router-link>

      <!-- Toggler -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Collapse -->
      <div class="collapse navbar-collapse" id="navbarCollapse">

        <!-- Nav -->
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Главная</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/masters">Сотрудники</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/services">Услуги</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/appointments">Запись</router-link>
          </li>
        </ul>

        <!-- Nav -->
        <ul class="navbar-nav flex-row">
          <li class="nav-item" v-if="!isLoggedIn">
            <router-link class="nav-link" to="/login">Вход</router-link>
          </li>
          <li class="nav-item ms-lg-n4"  v-if="!isLoggedIn">
            <router-link class="nav-link" to="/register">Регистрация</router-link>
          </li>
          <li class="nav-item" v-if="isLoggedIn && user">
            <span class="nav-link">{{ user.name }}</span>
          </li>
          <li class="nav-item" v-if="isLoggedIn">
            <a class="nav-link" href="#" @click="logout">Выйти</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapState, mapActions } from 'vuex'


export default {
  name: 'Navbar',
  computed: {
// Using Vuex helpers to map the 'isLoggedIn' state to a computed property
    ...mapState(['isLoggedIn', 'user'])
  },
  methods: {
// Using Vuex helpers to map the 'logout' action to a method
    ...mapActions(['logout'])
  }
}
</script>

<style scoped>
/* Добавьте сюда стили, если нужно */
</style>
