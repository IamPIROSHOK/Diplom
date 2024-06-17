<template>
  <div>
    <h2>Запись на прием</h2>

    <!-- Выбор мастера -->
    <div>
      <h3>Выберите мастера</h3>
      <div class="masters-grid">
        <MasterCard
            v-for="master in allMasters"
            :key="master.id"
            :master="master"
            :isSelected="selectedMaster && master.id === selectedMaster.id"
            @select="selectMaster"
        />
      </div>
    </div>

    <!-- Выбор даты -->
    <div>
      <h3>Выберите дату</h3>
      <input type="date" v-model="appointmentDate" @change="fetchServicesAndTimeSlots">
    </div>

    <!-- Выбор времени -->
    <div>
      <h3>Выберите время</h3>
      <div class="times-grid">
        <TimeSlot
            v-for="time in timeSlots"
            :key="time"
            :time="time"
            :isSelected="time === startTime"
            @select="selectTimeSlot"
        />
      </div>
      <p v-if="timeSlots.length === 0">К сожалению на этот день нет свободного времени для записи</p>
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
import MasterCard from '../components/MasterCard.vue';
import TimeSlot from '../components/TimeSlot.vue';

export default {
  components: {
    MasterCard,
    TimeSlot
  },
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
    async fetchServicesAndTimeSlots() {
      try {
        const payload = {
          appointment_date: this.appointmentDate || null,
          master_id: this.selectedMaster ? this.selectedMaster.id : null,
          start_time: this.startTime || null,
        };

        console.log("Payload being sent to the server:", payload);

        const response = await axios.post('/get-available-time-slots-and-services', payload);
        this.timeSlots = response.data.time_slots;
        this.availableServices = response.data.services;
      } catch (error) {
        console.error("Error fetching services and time slots:", error);
        if (error.response) {
          console.error('Error details:', error.response.data);
          alert(`Error: ${error.response.data.message || 'An error occurred while fetching time slots and services'}`);
        }
      }
    },
    selectMaster(master) {
      this.selectedMaster = this.selectedMaster && this.selectedMaster.id === master.id ? null : master;
      this.fetchServicesAndTimeSlots();
    },
    selectTimeSlot(time) {
      this.startTime = time;
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
    this.fetchServicesAndTimeSlots();
  }
};
</script>

<style scoped>
.masters-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
}

.times-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
}
</style>
