<template>
  <div>
    <h2>Запись на прием</h2>
    <!-- Выбор даты -->
    <div>
      <h3>Выберите дату</h3>
      <input type="date" v-model="appointmentDate" @change="fetchTimeSlots">
    </div>
    <!-- Выбор времени -->
    <div>
      <h3>Выберите время</h3>
      <select v-model="startTime" @change="fetchMastersAndServices" :disabled="timeSlots.length === 0">
        <option v-for="time in timeSlots" :key="time" :value="time">{{ time }}</option>
      </select>
    </div>
    <!-- Выбор мастера и услуги -->
    <div>
      <h3>Выберите мастера и услугу</h3>
      <div v-for="master in masters" :key="master.id">
        <h4>{{ master.name }}</h4>
        <div v-for="service in master.services" :key="service.id">
          <label>
            <input type="checkbox" :value="service.id" @change="toggleService(master.id, service.id)" :disabled="startTime === ''">
            {{ service.name }} ({{ service.duration }} мин)
          </label>
        </div>
      </div>
    </div>
    <!-- Кнопка записи -->
    <button @click="bookAppointment" :disabled="Object.keys(selectedServices).length === 0 || startTime === ''">Записаться</button>
  </div>
</template>

<script>
import axios from '@/axios';
import { mapActions, mapState } from 'vuex';

export default {
  data() {
    return {
      appointmentDate: '',
      startTime: '',
      timeSlots: [],
      masters: [],
      selectedServices: {}
    };
  },
  computed: {
    ...mapState(['user'])
  },
  methods: {
    ...mapActions(['login', 'logout', 'initializeStore']),
    async fetchTimeSlots() {
      if (this.appointmentDate === '') return;
      try {
        const response = await axios.post('/get-available-time-slots', { appointment_date: this.appointmentDate });
        this.timeSlots = response.data.time_slots;
        this.startTime = ''; // Сброс времени при изменении даты
        this.masters = []; // Сброс мастеров и услуг при изменении даты
        this.selectedServices = {}; // Сброс выбранных услуг при изменении даты
      } catch (error) {
        console.error("Error fetching time slots:", error);
      }
    },
    async fetchMastersAndServices() {
      if (this.startTime === '') return;
      try {
        const response = await axios.post('/get-available-masters-and-services', {
          appointment_date: this.appointmentDate,
          start_time: this.startTime
        });
        this.masters = response.data.masters;
        this.selectedServices = {}; // Сброс выбранных услуг при изменении времени
      } catch (error) {
        console.error("Error fetching masters and services:", error);
      }
    },
    toggleService(masterId, serviceId) {
      if (!this.selectedServices[masterId]) {
        this.selectedServices[masterId] = [];
      }
      const index = this.selectedServices[masterId].indexOf(serviceId);
      if (index === -1) {
        this.selectedServices[masterId].push(serviceId);
      } else {
        this.selectedServices[masterId].splice(index, 1);
      }
    },
    async bookAppointment() {
      try {
        const services = [];
        for (const masterId in this.selectedServices) {
          const serviceIds = this.selectedServices[masterId];
          if (Array.isArray(serviceIds)) {
            serviceIds.forEach(serviceId => {
              services.push({ service_id: serviceId, master_id: masterId });
            });
          }
        }

        const response = await axios.post('/appointments', {
          user_id: this.user.id,
          appointment_date: this.appointmentDate,
          start_time: this.startTime,
          services: services
        });
        alert(response.data.message);
        // Сброс полей после успешного бронирования
        this.appointmentDate = '';
        this.startTime = '';
        this.timeSlots = [];
        this.masters = [];
        this.selectedServices = {};
      } catch (error) {
        console.error("Error booking appointment:", error);
        if (error.response) {
          console.error('Error details:', error.response.data);
          alert(`Error: ${error.response.data.message || 'An error occurred while booking the appointment'}`);
        }
      }
    }
  },
  mounted() {
    this.initializeStore();
  }
};
</script>

<style scoped>
/* Добавьте свои стили здесь */
</style>
