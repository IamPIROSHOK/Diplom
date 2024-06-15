<template>
  <div>
    <h1>Запись на прием</h1>
    <form @submit.prevent="bookAppointment">
      <label>Дата:</label>
      <input type="date" v-model="appointment_date" @change="fetchAllAvailableTimes" />

      <div v-for="(service, index) in selectedServices" :key="index" class="service-selection">
        <label>Мастер:</label>
        <select v-model="service.master_id" @change="fetchAvailableServices(service)">
          <option v-for="master in masters" :key="master.id" :value="master.id">{{ master.name }}</option>
        </select>

        <label>Услуга:</label>
        <select v-model="service.service_id" @change="fetchAvailableTimes(service)">
          <option v-for="serviceOption in service.availableServices" :key="serviceOption.id" :value="serviceOption.id">{{ serviceOption.name }}</option>
        </select>

        <label>Время:</label>
        <select v-model="service.start_time">
          <option v-for="time in service.availableTimes" :key="time" :value="time">{{ time }}</option>
        </select>

        <button type="button" @click="removeService(index)">Удалить услугу</button>
      </div>

      <button type="button" @click="addService">Добавить услугу</button>
      <button type="submit">Записаться</button>
    </form>
    <div v-if="error" class="error">{{ error }}</div>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  data() {
    return {
      masters: [],
      services: [],
      selectedServices: [
        {
          master_id: null,
          service_id: null,
          start_time: null,
          availableServices: [],
          availableTimes: [],
        },
      ],
      appointment_date: '',
      error: null,
    };
  },
  methods: {
    async fetchMasters() {
      const response = await axios.get('/masters');
      this.masters = response.data;
    },
    async fetchAvailableServices(service) {
      if (service.master_id) {
        const response = await axios.get(`/masters/${service.master_id}/services`);
        service.availableServices = response.data;
      }
    },
    async fetchAvailableTimes(service) {
      if (service.master_id && service.service_id && this.appointment_date) {
        const response = await axios.post('/schedules/available-times', {
          master_id: service.master_id,
          date: this.appointment_date,
        });
        service.availableTimes = response.data;
      }
    },
    async fetchAllAvailableTimes() {
      for (const service of this.selectedServices) {
        await this.fetchAvailableTimes(service);
      }
    },
    addService() {
      this.selectedServices.push({
        master_id: null,
        service_id: null,
        start_time: null,
        availableServices: [],
        availableTimes: [],
      });
    },
    removeService(index) {
      this.selectedServices.splice(index, 1);
    },
    async bookAppointment() {
      try {
        const services = this.selectedServices.map(service => ({
          master_id: service.master_id,
          service_id: service.service_id,
          start_time: service.start_time,
        }));
        const response = await axios.post('/appointments', {
          user_id: this.$store.state.user.id,
          appointment_date: this.appointment_date,
          services,
        });
        alert(response.data.message);
      } catch (error) {
        if (error.response) {
          this.error = error.response.data.message;
          console.error(error.response.data.details);
        } else {
          this.error = 'An unexpected error occurred';
        }
      }
    },
  },
  mounted() {
    this.fetchMasters();
  },
};
</script>

<style scoped>
.service-selection {
  margin-bottom: 10px;
}
.error {
  color: red;
}
</style>
