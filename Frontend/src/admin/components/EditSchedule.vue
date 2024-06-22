<template>
  <div>
    <h2>Edit Schedule</h2>
    <form @submit.prevent="saveSchedule">
      <div>
        <label for="master">Master</label>
        <select v-model="schedule.master_id" id="master" required>
          <option v-for="master in masters" :key="master.id" :value="master.id">{{ master.name }}</option>
        </select>
      </div>
      <div>
        <label for="date">Date</label>
        <input type="date" v-model="schedule.date" id="date" required>
      </div>
      <div>
        <label for="start_time">Start Time</label>
        <input type="time" v-model="schedule.start_time" id="start_time" required>
      </div>
      <div>
        <label for="end_time">End Time</label>
        <input type="time" v-model="schedule.end_time" id="end_time" required>
      </div>
      <button type="submit">Save</button>
    </form>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  name: 'EditSchedule',
  data() {
    return {
      masters: [],
      schedule: {
        master_id: '',
        date: '',
        start_time: '',
        end_time: ''
      }
    };
  },
  async mounted() {
    await this.fetchMasters();
  },
  methods: {
    async fetchMasters() {
      try {
        const response = await axios.get('/admin/masters');
        this.masters = response.data;
      } catch (error) {
        console.error('Error fetching masters:', error);
      }
    },
    async saveSchedule() {
      try {
        if (this.schedule.id) {
          await axios.put(`/admin/schedules/${this.schedule.id}`, this.schedule);
          alert('Schedule updated successfully');
        } else {
          await axios.post('/admin/schedules', this.schedule);
          alert('Schedule created successfully');
        }
        this.schedule = { master_id: '', date: '', start_time: '', end_time: '' };
      } catch (error) {
        console.error('Error saving schedule:', error);
        alert('Error saving schedule');
      }
    }
  }
};
</script>

<style scoped>
/* Стили для EditSchedule */
</style>
