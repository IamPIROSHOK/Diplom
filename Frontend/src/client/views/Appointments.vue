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

    <!-- Выбор мастера -->
    <div>
      <h5>Выберите мастера</h5>
      <div class="masters-grid">
        <MasterCard
            v-for="master in allMasters"
            :key="master.id"
            :master="master"
            :isSelected="selectedMasters.includes(master.id)"
            @select="toggleMasterSelection"
        />
      </div>
    </div>

    <!-- Выбор даты -->
    <div>
      <h5>Выберите дату</h5>
      <input type="date" v-model="appointmentDate" :min="minDate" @change="fetchServicesAndTimeSlots">
    </div>

    <!-- Выбор времени -->
    <div>
      <h5>Выберите время</h5>
      <div class="times-grid">
        <TimeSlot
            v-for="time in uniqueTimeSlots"
            :key="time"
            :time="time"
            :isSelected="time === startTime"
            @select="selectTimeSlot"
        />
      </div>
      <p v-if="uniqueTimeSlots.length === 0 && !selectedMasters.length && !selectedServiceIds.length">Пожалуйста, выберите мастера или услугу для отображения доступного времени</p>
      <p v-else-if="uniqueTimeSlots.length === 0">К сожалению на этот день нет свободного времени для записи</p>
    </div>

    <!-- Кнопка записи -->
    <button @click="bookAppointment" :disabled="Object.keys(selectedServices).length === 0 || !startTime">Записаться</button>
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
      selectedMasters: [],
      appointmentDate: this.getTodayDate(),
      startTime: '',
      timeSlots: [],
      allMasters: [],
      availableServices: [],
      selectedServices: {},
      minDate: this.getTodayDate(),
    };
  },
  computed: {
    ...mapState(['user']),
    uniqueTimeSlots() {
      return [...new Set(this.timeSlots)];
    },
    selectedServiceIds() {
      return Object.keys(this.selectedServices);
    }
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
    async fetchServicesAndTimeSlots() {
      try {
        const payload = {
          appointment_date: this.appointmentDate || null,
          master_ids: this.selectedMasters.length ? this.selectedMasters : null,
          service_ids: this.selectedServiceIds.length ? this.selectedServiceIds : null,
          start_time: this.startTime || null,
        };

        const response = await axios.post('/get-available-time-slots-and-services', payload);
        this.timeSlots = response.data.time_slots;
        this.availableServices = response.data.services;
        this.allMasters = response.data.masters;
      } catch (error) {
        console.error("Error fetching services and time slots:", error);
        if (error.response) {
          console.error('Error details:', error.response.data);
          alert(`Error: ${error.response.data.message || 'An error occurred while fetching time slots and services'}`);
        }
      }
    },
    toggleMasterSelection(master) {
      if (this.selectedMasters.includes(master.id)) {
        this.selectedMasters = this.selectedMasters.filter(id => id !== master.id);
      } else {
        this.selectedMasters.push(master.id);
      }
      this.fetchServicesAndTimeSlots();
    },
    selectTimeSlot(time) {
      this.startTime = time;
    },
    toggleService(service) {
      if (this.selectedServices[service.id]) {
        delete this.selectedServices[service.id];
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
            services.push({ service_id: serviceId, master_id: this.selectedMasters[0] });
          }
        }

        const response = await axios.post('/appointments', {
          user_id: this.user.id,
          appointment_date: this.appointmentDate,
          start_time: this.startTime,
          services: services
        });
        alert(response.data.message);
        this.selectedMasters = [];
        this.appointmentDate = this.getTodayDate();
        this.startTime = '';
        this.timeSlots = [];
        this.availableServices = [];
        this.selectedServices = {};
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
