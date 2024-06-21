<template>
  <div class="form-container">
    <form @submit.prevent="login" class="login-form">
      <input type="email" v-model="email" placeholder="Email" required/>
      <input type="password" v-model="password" placeholder="Пароль" required/>
      <button type="submit">Войти</button>
      <ul v-if="errorMessages.length" class="error-messages">
        <li v-for="(message, index) in errorMessages" :key="index">{{ message }}</li>
      </ul>
    </form>
  </div>
</template>


<script>
import axios from '@/axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessages: [],
    };
  },
  methods: {
    async login() {
      this.errorMessages = [];
      try {
        const response = await axios.post('/login', {
          email: this.email,
          password: this.password,
        });
        if (response.data.token) {
          this.$store.dispatch('login', { token: response.data.token, user: response.data.user });
          this.$router.push('/');
        }
      } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
          this.errorMessages = [error.response.data.message];
        } else {
          this.errorMessages = ['Произошла ошибка. Пожалуйста, попробуйте снова.'];
        }
      }
    },
  },
};
</script>

<style scoped>
.form-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 300px;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.login-form input, .login-form button {
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 16px;
}

.login-form button {
  background-color: #007BFF;
  color: white;
  cursor: pointer;
}

.error-messages {
  color: red;
  margin-top: 10px;
  padding-left: 0;
}

.error-messages li {
  list-style-type: none;
}
</style>