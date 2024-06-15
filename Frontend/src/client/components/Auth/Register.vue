<template>
  <!-- Form for registration -->
  <div class="form-container">
    <form @submit.prevent="register" class="register-form">
      <!-- Name, email, and password inputs -->
      <input type="text" v-model="name" placeholder="Name" required />
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <!-- Submit button -->
      <button type="submit">Зарегистрироваться</button>
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
      name: '',
      email: '',
      password: '',
      errorMessages: [],
    };
  },
  methods: {
    async register() {
      this.errorMessages = []; // Сброс ошибок перед новой попыткой
      try {
        const response = await axios.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
        });

        if (response.data.token) {
          this.$store.dispatch('login', { token: response.data.token, user: response.data.user });
          this.$router.push('/');
        }
      } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
          this.errorMessages = Object.values(error.response.data.errors).flat();
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


.register-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 300px;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


.register-form input, .register-form button {
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
  font-size: 16px;
}


.register-form button {
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