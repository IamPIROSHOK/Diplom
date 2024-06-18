<template>
  <div id="client-app" class="d-flex flex-column min-vh-100">
    <Navbar />
    <router-view></router-view>
    <ChatIcon />
    <Footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import ChatIcon from './components/ChatIcon.vue';

export default {
  name: 'ClientApp',
  components: {
    Navbar,
    Footer,
    ChatIcon,
  },
};
</script>

