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
            v-for="master in filteredMasters"
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
    <div style="padding-top:20px;">
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
    <button class="btn btn-primary" @click="bookAppointment" :disabled="Object.keys(selectedServices).length === 0 || !startTime">Записаться</button>
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
      // Flatten the timeSlots object to get unique time slots
      const allSlots = Object.values(this.timeSlots).flat();
      return [...new Set(allSlots)];
    },
    selectedServiceIds() {
      return Object.keys(this.selectedServices);
    },
    filteredMasters() {
      if (this.selectedServiceIds.length === 0) {
        return this.allMasters;
      }
      return this.allMasters.filter(master => {
        return master.services && master.services.some(service => this.selectedServiceIds.includes(service.id.toString()));
      });
    },
    canBook() {
      return this.selectedServiceIds.length > 0 && this.selectedMasters.length > 0 && this.startTime;
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
          master_ids: this.selectedMasters.length ? this.selectedMasters : null,
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
        let currentStartTime = this.startTime;

        for (const serviceId of this.selectedServiceIds) {
          const service = this.availableServices.find(s => s.id === parseInt(serviceId));
          const duration = service ? service.duration : 0;
          const endTime = new Date(new Date(`1970-01-01T${currentStartTime}Z`).getTime() + duration * 60000).toISOString().substr(11, 5);

          services.push({ service_id: serviceId, master_id: this.selectedMasters[0], start_time: currentStartTime, end_time: endTime });

          currentStartTime = endTime;
        }

        const response = await axios.post('/appointments', {
          user_id: this.user.id,
          appointment_date: this.appointmentDate,
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
    },
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
