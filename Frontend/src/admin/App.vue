<template>
  <div id="admin-app">
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'AdminApp',
  mounted() {
    console.log('AdminApp mounted'); // Отладочное сообщение
  }
};
</script>

<style scoped>
/* Добавьте стили при необходимости */
</style>
