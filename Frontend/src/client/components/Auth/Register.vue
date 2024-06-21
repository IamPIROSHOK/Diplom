<template>
  <div class="form-container">
    <form @submit.prevent="register" class="register-form">
      <input type="text" v-model="name" placeholder="Имя" required />
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="tel" v-model="phone" placeholder="Телефон" ref="phoneInput" required />
      <input type="password" v-model="password" placeholder="Пароль" required />
      <input type="password" v-model="password_confirmation" placeholder="Подтверждение пароля" required />
      <div>
        <input type="checkbox" v-model="subscribe" id="subscribe" />
        <label for="subscribe">Получать рассылку</label>
      </div>
      <div v-if="subscribe">
        <select v-model="subscription_type" required>
          <option value="">Выберите тип рассылки</option>
          <option value="email">Email</option>
          <option value="sms">SMS</option>
          <option value="telegram">Telegram</option>
        </select>
      </div>
      <button type="submit">Зарегистрироваться</button>
      <ul v-if="errorMessages.length" class="error-messages">
        <li v-for="(message, index) in errorMessages" :key="index">{{ message }}</li>
      </ul>
    </form>
  </div>
</template>

<script>
import axios from '@/axios';
import Inputmask from 'inputmask';
export default {
  data() {
    return {
      name: '', email: '', phone: '', password: '', password_confirmation: '',
      subscribe: false, subscription_type: '', errorMessages: [],
    };
  },
  mounted() {
    const phoneInput = this.$refs.phoneInput;
    Inputmask({ mask: '+7 (999) 999-99-99' }).mask(phoneInput);
  },
  methods: {
    async register() {
      this.errorMessages = [];
      try {
        const response = await axios.post('/register', {
          name: this.name,
          email: this.email,
          phone: this.phone,
          password: this.password,
          password_confirmation: this.password_confirmation,
          subscription_type: this.subscribe ? this.subscription_type : null,
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
.register-form input,
.register-form select,
.register-form button {
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
#subscribe {
  width: 20px;
  margin-right: 5px;
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
