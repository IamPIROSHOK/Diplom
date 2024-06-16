<template>
  <div>
    <h2>Запись на прием</h2>

    <!-- Выбор мастера -->
    <div>
      <h3>Выберите мастера</h3>
      <select v-model="selectedMaster" @change="fetchServices">
        <option value="" disabled>Выберите мастера</option>
        <option v-for="master in allMasters" :key="master.id" :value="master">{{ master.name }}</option>
      </select>
    </div>

    <!-- Выбор даты -->
    <div>
      <h3>Выберите дату</h3>
      <input type="date" v-model="appointmentDate" @change="fetchServices">
    </div>

    <!-- Выбор времени -->
    <div>
      <h3>Выберите время</h3>
      <select v-model="startTime" @change="fetchServices" :disabled="timeSlots.length === 0">
        <option value="" disabled>Выберите время</option>
        <option v-for="time in timeSlots" :key="time" :value="time">{{ time }}</option>
      </select>
    </div>

    <!-- Выбор услуг -->
    <div>
      <h3>Выберите услугу</h3>
      <div v-for="service in availableServices" :key="service.id">
        <label>
          <input type="checkbox" :value="service.id" @change="toggleService(service.id)">
          {{ service.name }} ({{ service.duration }} мин)
        </label>
      </div>
    </div>

    <!-- Кнопка записи -->
    <button @click="bookAppointment" :disabled="Object.keys(selectedServices).length === 0 || !startTime">Записаться</button>
  </div>
</template>

<script>
import axios from '@/axios';
import { mapState } from 'vuex';

export default {
  data() {
    return {
      selectedMaster: null,
      appointmentDate: '',
      startTime: '',
      timeSlots: [],
      allMasters: [],
      availableServices: [],
      selectedServices: {},
    };
  },
  computed: {
    ...mapState(['user'])
  },
  methods: {
    async fetchAllMasters() {
      try {
        const response = await axios.get('/masters');
        this.allMasters = response.data;
      } catch (error) {
        console.error("Error fetching masters:", error);
      }
    },
    async fetchServices() {
      if (!this.selectedMaster && !this.appointmentDate) {
        this.fetchAllServices();
        return;
      }

      try {
        const response = await axios.post('/get-available-services', {
          appointment_date: this.appointmentDate,
          master_id: this.selectedMaster ? this.selectedMaster.id : null,
          start_time: this.startTime || null,
        });
        this.availableServices = response.data.services;
      } catch (error) {
        console.error("Error fetching services:", error);
      }

      if (this.selectedMaster && this.appointmentDate) {
        await this.fetchTimeSlotsAndServices();
      }
    },
    async fetchAllServices() {
      try {
        const response = await axios.get('/services');
        this.availableServices = response.data;
      } catch (error) {
        console.error("Error fetching all services:", error);
      }
    },
    async fetchTimeSlotsAndServices() {
      if (!this.selectedMaster || !this.appointmentDate) return;

      try {
        const response = await axios.post('/get-available-time-slots-and-services', {
          appointment_date: this.appointmentDate,
          master_id: this.selectedMaster.id
        });
        this.timeSlots = response.data.time_slots;
        this.availableServices = response.data.services;
      } catch (error) {
        console.error("Error fetching time slots and services:", error);
      }
    },
    toggleService(serviceId) {
      if (!this.selectedServices[this.selectedMaster.id]) {
        this.selectedServices[this.selectedMaster.id] = [];
      }
      const index = this.selectedServices[this.selectedMaster.id].indexOf(serviceId);
      if (index === -1) {
        this.selectedServices[this.selectedMaster.id].push(serviceId);
      } else {
        this.selectedServices[this.selectedMaster.id].splice(index, 1);
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
        this.selectedMaster = null;
        this.appointmentDate = '';
        this.startTime = '';
        this.timeSlots = [];
        this.availableServices = [];
        this.selectedServices = {};
      } catch (error) {
        console.error("Error booking appointment:", error);
        if (error.response) {
          console.error('Error details:', error.response.data);
          alert(`Error: ${error.response.data.message || 'An error occurred while booking the appointment'}`);
        }
      }
    },
    initializeStore() {
      this.$store.dispatch('initializeStore');
    }
  },
  mounted() {
    this.initializeStore();
    this.fetchAllMasters();
    this.fetchAllServices();
  }
};
</script>

<style scoped>
/* Добавьте свои стили здесь */
</style>
