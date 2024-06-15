<template>
  <div id="client-app" class="d-flex flex-column min-vh-100">
    <Navbar />
    <router-view></router-view>
    <Footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
export default {
  name: 'ClientApp',
  components: {
    Navbar,
    Footer,
  },
};
</script>

