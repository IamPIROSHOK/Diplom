<template>
  <div class="container mt-5">
    <h2>Запись на прием</h2>

    <!-- Выбор услуги -->
    <div>
      <h5>Выберите услугу</h5>
      <div class="services-grid">
        <ServiceCard
            v-for="service in availableServices"
            :key="service.id"
            :service="service"
            :isSelected="!!selectedServices[service.id]"
            @select="toggleService"
        />
      </div>
    </div>

    <!-- Выбор мастера и времени для каждой услуги -->
    <div v-for="serviceId in selectedServiceIds" :key="serviceId">
      <h5>Выберите мастера и время для услуги</h5>
      <div>
        <h6>{{ availableServices.find(service => service.id === parseInt(serviceId)).name }}</h6>
        <div class="masters-grid">
          <MasterCard
              v-for="master in filteredMasters(serviceId)"
              :key="master.id"
              :master="master"
              :isSelected="selectedMasters[serviceId] && selectedMasters[serviceId] === master.id"
              @select="() => selectMasterForService(serviceId, master.id)"
          />
        </div>
        <div class="times-grid">
          <TimeSlot
              v-for="time in uniqueTimeSlots(serviceId, selectedMasters[serviceId])"
              :key="time"
              :time="time"
              :isSelected="selectedTimeSlots[serviceId] && selectedTimeSlots[serviceId] === time"
              @select="() => selectTimeForService(serviceId, time)"
          />
        </div>
      </div>
    </div>

    <!-- Выбор даты -->
    <div>
      <h5>Выберите дату</h5>
      <input type="date" v-model="appointmentDate" :min="minDate" @change="fetchServicesAndTimeSlots">
    </div>

    <p v-if="selectedServiceIds.length > 0 && !canBook">Пожалуйста, выберите мастера и время для каждой услуги</p>
    <p v-else-if="selectedServiceIds.length === 0">К сожалению на этот день нет свободного времени для записи</p>

    <!-- Кнопка записи -->
    <button class="btn btn-primary" @click="bookAppointment" :disabled="Object.keys(selectedServices).length === 0 || !canBook">Записаться</button>
  </div>
</template>

<script>
import axios from '@/axios';
import { mapState } from 'vuex';
import MasterCard from '../components/MasterCard.vue';
import TimeSlot from '../components/TimeSlot.vue';
import ServiceCard from '../components/ServiceCard.vue';

export default {
  components: {
    MasterCard,
    TimeSlot,
    ServiceCard
  },
  data() {
    return {
      selectedMasters: {},
      appointmentDate: this.getTodayDate(),
      selectedTimeSlots: {},
      timeSlots: {},
      allMasters: [],
      availableServices: [],
      selectedServices: {},
      minDate: this.getTodayDate(),
    };
  },
  computed: {
    ...mapState(['user']),
    selectedServiceIds() {
      return Object.keys(this.selectedServices);
    },
    canBook() {
      return this.selectedServiceIds.every(serviceId => this.selectedMasters[serviceId] && this.selectedTimeSlots[serviceId]);
    }
  },
  methods: {
    async fetchAllMasters() {
      try {
        const response = await axios.get('/masters');
        console.log("Fetched Masters:", response.data);
        this.allMasters = response.data;
      } catch (error) {
        console.error("Error fetching masters:", error);
      }
    },
    async fetchServicesAndTimeSlots() {
      try {
        const payload = {
          appointment_date: this.appointmentDate || null,
          master_ids: Object.values(this.selectedMasters).length ? Object.values(this.selectedMasters) : null,
          service_ids: this.selectedServiceIds.length ? this.selectedServiceIds : null,
        };

        console.log("Payload being sent to the server:", payload);

        const response = await axios.post('/get-available-time-slots-and-services', payload);
        this.timeSlots = response.data.time_slots;
        this.availableServices = response.data.services;
        this.allMasters = response.data.masters;

        console.log("Fetched Time Slots:", this.timeSlots);
        console.log("Fetched Services:", this.availableServices);
        console.log("Fetched Masters:", this.allMasters);
      } catch (error) {
        console.error("Error fetching services and time slots:", error);
        if (error.response) {
          console.error('Error details:', error.response.data);
          alert(`Error: ${error.response.data.message || 'An error occurred while fetching time slots and services'}`);
        }
      }
    },
    selectMasterForService(serviceId, masterId) {
      this.selectedMasters = { ...this.selectedMasters, [serviceId]: masterId };
      this.fetchServicesAndTimeSlots();
    },
    selectTimeForService(serviceId, time) {
      this.selectedTimeSlots = { ...this.selectedTimeSlots, [serviceId]: time };
    },
    toggleService(service) {
      if (this.selectedServices[service.id]) {
        const { [service.id]: _, ...rest } = this.selectedServices;
        this.selectedServices = rest;
      } else {
        this.selectedServices = { ...this.selectedServices, [service.id]: true };
      }
      this.fetchServicesAndTimeSlots();
    },
    async bookAppointment() {
      try {
        const services = [];
        for (const serviceId in this.selectedServices) {
          if (this.selectedServices[serviceId]) {
            services.push({
              service_id: serviceId,
              master_id: this.selectedMasters[serviceId],
              start_time: this.selectedTimeSlots[serviceId]
            });
          }
        }

        const response = await axios.post('/appointments', {
          user_id: this.user.id,
          appointment_date: this.appointmentDate,
          services: services
        });
        alert(response.data.message);
        this.selectedMasters = {};
        this.appointmentDate = this.getTodayDate();
        this.timeSlots = {};
        this.availableServices = [];
        this.selectedServices = {};
        this.selectedTimeSlots = {};
        this.fetchServicesAndTimeSlots();
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
    },
    getTodayDate() {
      const today = new Date();
      return today.toISOString().substr(0, 10);
    },
    filteredMasters(serviceId) {
      console.log("Filtering masters for service ID:", serviceId);
      return this.allMasters.filter(master => {
        return master.services && master.services.some(service => service.id === parseInt(serviceId));
      });
    },
    uniqueTimeSlots(serviceId, masterId) {
      if (this.timeSlots[masterId]) {
        return [...new Set(this.timeSlots[masterId])];
      }
      return [];
    }
  },
  mounted() {
    this.initializeStore();
    this.fetchAllMasters();
    this.fetchServicesAndTimeSlots();
  }
};
</script>

<style scoped>
.masters-grid, .services-grid, .times-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
</style>
