<template>
  <div>
    <h2>Create Master</h2>
    <form @submit.prevent="createMaster">
      <div>
        <label for="name">Name</label>
        <input type="text" v-model="master.name" id="name" required>
      </div>
      <div>
        <label for="description">Description</label>
        <textarea v-model="master.description" id="description"></textarea>
      </div>
      <div>
        <label for="photo">Photo URL</label>
        <input type="text" v-model="master.photo" id="photo">
      </div>
      <button type="submit">Create</button>
    </form>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  name: 'CreateMaster',
  data() {
    return {
      master: {
        name: '',
        description: '',
        photo: ''
      }
    };
  },
  methods: {
    async createMaster() {
      try {
        const response = await axios.post('/admin/masters', this.master);
        alert('Master created successfully');
        this.master = { name: '', description: '', photo: '' };
      } catch (error) {
        console.error('Error creating master:', error);
        alert('Error creating master');
      }
    }
  }
};
</script>

<style scoped>
/* Стили для CreateMaster */
</style>
