<template>
  <div>
    <h2>Create Discount</h2>
    <form @submit.prevent="createDiscount">
      <div>
        <label for="title">Title</label>
        <input type="text" v-model="discount.title" id="title" required>
      </div>
      <div>
        <label for="description">Description</label>
        <textarea v-model="discount.description" id="description"></textarea>
      </div>
      <div>
        <label for="percentage">Percentage</label>
        <input type="number" v-model="discount.percentage" id="percentage" min="0" max="100" required>
      </div>
      <button type="submit">Create</button>
    </form>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  name: 'CreateDiscount',
  data() {
    return {
      discount: {
        title: '',
        description: '',
        percentage: 0
      }
    };
  },
  methods: {
    async createDiscount() {
      try {
        const response = await axios.post('/admin/discounts', this.discount);
        alert('Discount created successfully');
        this.discount = { title: '', description: '', percentage: 0 };
      } catch (error) {
        console.error('Error creating discount:', error);
        alert('Error creating discount');
      }
    }
  }
};
</script>

<style scoped>
/* Стили для CreateDiscount */
</style>
