<!-- src/components/Masters.vue -->

<template>
  <div class="container mt-5">
    <h1 class="mb-4">Наши мастера</h1>
    <div class="row">
      <div class="col-md-4" v-for="master in masters" :key="master.id">
        <div class="card mb-4">
          <img :src="master.photo" class="card-img-top" alt="Фото мастера">
          <div class="card-body">
            <h5 class="card-title">{{ master.name }}</h5>
            <p class="card-text">{{ master.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  data() {
    return {
      masters: [],
    };
  },
  created() {
    this.fetchMasters();
  },
  methods: {
    async fetchMasters() {
      try {
        const response = await axios.get('/masters');
        this.masters = response.data;
      } catch (error) {
        console.error('Error fetching masters:', error);
      }
    },
  },
};
</script>

<style scoped>
.card-img-top {
  height: 350px;
  object-fit: cover;
}
</style>
