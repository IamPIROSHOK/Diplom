<template>
  <div class="container">
    <Navigation/>

    <div class="main">
      <ChatsList/>
    </div>
  </div>
</template>

<script>
import ChatsList from '../components/ChatsList.vue';
import Navigation from '../components/Navigation.vue';

export default {
  name: 'Chats',
  components: {
    ChatsList,
    Navigation,
  }
};
</script>
