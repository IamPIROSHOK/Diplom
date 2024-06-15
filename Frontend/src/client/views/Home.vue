<!-- src/views/Home.vue -->

<template>
  <section>
    <div class="row gx-0 d-block d-lg-flex flickity flickity-lg-none" data-flickity='{"watchCSS": true}'>
      <div class="col-12 d-flex flex-column bg-cover" style="background-image: url('/assets/images/slide-01.png'); min-height: 720px;">
      </div>
    </div>
  </section>
  <HomeMasters />
</template>

<script>
import HomeMasters from '../components/HomeMasters.vue';

export default {
  name: 'Home',
  components: {
    HomeMasters,
  },
};
</script>

<style scoped>
/* Добавьте сюда стили, если нужно */
</style>
