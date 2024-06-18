<template>
  <div class="container mt-5 home-masters">
    <h2 class="mb-8 text-center">Наши мастера</h2>
    <swiper
        :slides-per-view="3"
        :centered-slides="true"
        :loop="true"
        :autoplay="{ delay: 4000 }"
        :navigation="{
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
      }"
    >
      <swiper-slide v-for="master in masters" :key="master.id" class="slider-item">
        <div class="card text-center">
          <img :src="master.photo" class="card-img-top rounded-circle mx-auto d-block" alt="Фото мастера" style="width: 150px; height: 150px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title">{{ master.name }}</h5>
            <p class="card-text">{{ master.description }}</p>
          </div>
        </div>
      </swiper-slide>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-pagination"></div>
    </swiper>
    <div class="text-center mt-4">
      <router-link to="/masters" class="btn btn-primary">Посмотреть полный список</router-link>
    </div>
  </div>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/swiper-bundle.css';

import SwiperCore from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';

// Install modules
SwiperCore.use([Autoplay, Navigation]);

import axios from '@/axios';

export default {
  components: {
    Swiper,
    SwiperSlide,
  },
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
/* Стили для слайдера */
.home-masters {
  padding: 25px 0;
}

.slider-item {
  padding: 0 10px;
  outline: none;
}

.card {
  border: none;
}

.card-img-top {
  border-radius: 50%;
  margin-bottom: 20px;
}

.card-title {
  font-size: 1.25rem;
  font-weight: bold;
}

.card-text {
  font-size: 1rem;
  color: #6c757d;
}

.btn-primary {
  background-color: #ff6f61;
  border-color: #ff6f61;
}

.btn-primary:hover {
  background-color: #ff6f61;
  border-color: #ff6f61;
}

.swiper-button-next, .swiper-button-prev {
  color: #000;
}

.swiper-pagination-bullet {
  background: #000;
}
</style>
