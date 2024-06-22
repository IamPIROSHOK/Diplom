<template>
  <div class="recentAppointments">
    <div class="cardHeader">
      <h2>Recent Appointments</h2>
      <a href="#" class="btn">Посмотреть все</a>
    </div>
    <table>
      <thead>
      <tr>
        <td>Имя</td>
        <td>Дата</td>
        <td>Статус</td>
        <td>Действие</td>
      </tr>
      </thead>
      <tbody>
      <tr v-for="appointment in appointments" :key="appointment.id">
        <td>{{ appointment.user.name }}</td>
        <td>{{ appointment.appointment_date }}</td>
        <td>
          <select v-model="appointment.status" @change="updateAppointmentStatus(appointment)">
            <option value="pending">Pending</option>
            <option value="inProgress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </td>
        <td><button @click="updateAppointmentStatus(appointment)">Обновить</button></td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  name: 'RecentAppointments',
  data() {
    return {
      appointments: [],
    };
  },
  mounted() {
    this.fetchAppointments();
  },
  methods: {
    async fetchAppointments() {
      try {
        const response = await axios.get('/appointments');
        this.appointments = response.data;
      } catch (error) {
        console.error('Ошибка при получении записей:', error);
      }
    },
    async updateAppointmentStatus(appointment) {
      try {
        await axios.put(`/appointments/${appointment.id}/status`, { status: appointment.status });
        alert('Статус записи успешно обновлен');
      } catch (error) {
        console.error('Ошибка при обновлении статуса записи:', error);
        alert('Ошибка при обновлении статуса записи');
      }
    },
  },
};
</script>

<style scoped>
/* Стили для RecentAppointments */
</style>
