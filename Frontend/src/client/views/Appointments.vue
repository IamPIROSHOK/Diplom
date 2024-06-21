<template>
  <div class="container mt-5">
    <h2>Запись на прием</h2>
    <!-- Выбор услуг и мастеров -->
    <div v-for="(pair, index) in serviceMasterPairs" :key="index" class="service-master-pair">
      <div class="service-number">{{ index + 1 }}</div>
      <div class="form-group flex-grow-1">
        <label>Услуга</label>
        <div class="dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle custom-select" type="button" @click="toggleServiceDropdown(index)">
            {{ pair.serviceId ? getServiceById(pair.serviceId).name : 'Выберите услугу' }}
          </button>
          <ul v-if="serviceDropdownIndex === index" class="dropdown-menu show">
            <li v-for="service in availableServices" :key="service.id">
              <a class="dropdown-item" @click="selectService(index, service.id)">
                {{ service.name }} - {{ service.price }} руб - {{ service.duration }} мин
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="form-group flex-grow-1">
        <label>Мастер</label>
        <div class="dropdown">
          <button class="btn btn-outline-secondary dropdown-toggle custom-select" type="button" @click="toggleMasterDropdown(index)">
            <img v-if="pair.masterId" :src="getMasterById(pair.masterId).photo" alt="Master Photo" class="option-photo">
            {{ pair.masterId ? getMasterById(pair.masterId).name : 'Выберите мастера' }}
          </button>
          <ul v-if="masterDropdownIndex === index" class="dropdown-menu show">
            <li v-for="master in filteredMastersForService(pair.serviceId)" :key="master.id">
              <a class="dropdown-item" @click="selectMaster(index, master.id)">
                <img :src="master.photo" alt="Master Photo" class="option-photo"> {{ master.name }}
              </a>
            </li>
          </ul>
        </div>
      </div>
      <button class="btn btn-danger btn-sm remove-btn" @click="removeServiceMasterPair(index)">×</button>
    </div>
    <!-- Кнопка добавления услуги -->
    <button class="btn btn-secondary add-service-btn mt-3" @click="addServiceMasterPair">Добавить услугу</button>
    <!-- Выбор даты -->
    <div class="form-group mt-4">
      <h5>Выберите дату</h5>
      <input type="date" class="form-control date-picker" v-model="appointmentDate" :min="minDate" @change="fetchServicesAndTimeSlots">
    </div>
    <!-- Выбор времени -->
    <div class="mt-4">
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
      <p v-if="uniqueTimeSlots.length === 0 && !serviceMasterPairs.length">Пожалуйста, выберите мастера или услугу для отображения доступного времени</p>
      <p v-else-if="uniqueTimeSlots.length === 0">К сожалению на этот день нет свободного времени для записи</p>
    </div>
    <!-- Кнопка записи -->
    <button class="btn btn-primary rounded-pill mt-4" @click="bookAppointment" :disabled="!canBook">Записаться</button>
  </div>
</template>

<script>
import axios from '@/axios';
import { mapState } from 'vuex';
import TimeSlot from '../components/TimeSlot.vue';
export default {
  components: {
    TimeSlot
  },
  data() {
    return {
      serviceMasterPairs: [{ serviceId: null, masterId: null }],
      appointmentDate: this.getTodayDate(),
      startTime: '',
      timeSlots: [],
      allMasters: [],
      availableServices: [],
      minDate: this.getTodayDate(),
      serviceDropdownIndex: null,
      masterDropdownIndex: null,
    };
  },
  computed: {
    ...mapState(['user']),
    uniqueTimeSlots() {
      const allSlots = Object.values(this.timeSlots).flat();
      return [...new Set(allSlots)];
    },
    canBook() {
      return this.serviceMasterPairs.every(pair => pair.serviceId && pair.masterId) && this.startTime;
    }
  },
  methods: {
    addServiceMasterPair() {
      this.serviceMasterPairs.push({ serviceId: null, masterId: null });
    },
    removeServiceMasterPair(index) {
      this.serviceMasterPairs.splice(index, 1);
      this.fetchServicesAndTimeSlots();
    },
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
          master_ids: this.serviceMasterPairs.map(pair => pair.masterId).filter(id => id),
          service_ids: this.serviceMasterPairs.map(pair => pair.serviceId).filter(id => id),
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
    filteredMastersForService(serviceId) {
      if (!serviceId) return this.allMasters;
      return this.allMasters.filter(master => {
        return master.services && master.services.some(service => service.id === parseInt(serviceId));
      });
    },
    toggleServiceDropdown(index) {
      this.serviceDropdownIndex = this.serviceDropdownIndex === index ? null : index;
    },
    toggleMasterDropdown(index) {
      this.masterDropdownIndex = this.masterDropdownIndex === index ? null : index;
    },
    selectService(index, serviceId) {
      this.serviceMasterPairs[index].serviceId = serviceId;
      this.serviceDropdownIndex = null;
      this.fetchServicesAndTimeSlots();
    },
    selectMaster(index, masterId) {
      this.serviceMasterPairs[index].masterId = masterId;
      this.masterDropdownIndex = null;
      this.fetchServicesAndTimeSlots();
    },
    selectTimeSlot(time) {
      this.startTime = time;
    },
    getServiceById(serviceId) {
      return this.availableServices.find(service => service.id === serviceId) || {};
    },
    getMasterById(masterId) {
      return this.allMasters.find(master => master.id === masterId) || {};
    },
    async bookAppointment() {
      try {
        const services = this.serviceMasterPairs.map(pair => {
          const service = this.availableServices.find(s => s.id === parseInt(pair.serviceId));
          const duration = service ? service.duration : 0;
          const endTime = new Date(new Date(`1970-01-01T${this.startTime}Z`).getTime() + duration * 60000).toISOString().substr(11, 5);
          return { service_id: pair.serviceId, master_id: pair.masterId, start_time: this.startTime, end_time: endTime };
        });

        const response = await axios.post('/appointments', {
          user_id: this.user.id,
          appointment_date: this.appointmentDate,
          services: services
        });

        alert(response.data.message);
        this.resetForm();
      } catch (error) {
        console.error("Error booking appointment:", error);
        if (error.response) {
          console.error('Error details:', error.response.data);
          alert(`Error: ${error.response.data.message || 'An error occurred while booking the appointment'}`);
        }
      }
    },
    resetForm() {
      this.serviceMasterPairs = [{ serviceId: null, masterId: null }];
      this.appointmentDate = this.getTodayDate();
      this.startTime = '';
      this.timeSlots = [];
      this.availableServices = [];
      this.fetchServicesAndTimeSlots();
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
h2 {
  margin-bottom: 20px;
}

.masters-grid, .services-grid, .times-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.service-master-pair {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 10px;
}
.service-number {
  font-weight: bold;
  margin-top: 10px;
}
.option-photo {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 50%;
  margin-right: 8px;
}
.dropdown-menu {
  width: 100%;
}
.btn {
  border-radius: 50px;
}
.custom-select {
  width: 100%;
  text-align: left;
}
.date-picker {
  border-radius: 50px;
  width: 300px;
}
.add-service-btn {
  display: block;
  margin: 0 auto;
  width: fit-content;
}
</style>
