<template>
  <div>
    <h2>Список чатов</h2>
    <ul>
      <li v-for="chat in chats" :key="chat.id" @click="selectChat(chat.id)">
        {{ chat.title }}
      </li>
    </ul>
    <div v-if="selectedChatId">
      <h3>Чат #{{ selectedChatId }}</h3>
      <div v-for="message in messages" :key="message.id" class="chat-message">
        <strong>{{ message.user.name }}:</strong> {{ message.message }}
      </div>
      <input v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type your message here...">
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      chats: [],
      messages: [],
      newMessage: '',
      selectedChatId: null,
    };
  },
  mounted() {
    this.fetchChats();
  },
  methods: {
    fetchChats() {
      axios.get('/chats').then(response => {
        this.chats = response.data;
      });
    },
    fetchMessages() {
      axios.get(`/messages/${this.selectedChatId}`).then(response => {
        this.messages = response.data;
      });
    },
    listenForMessages() {
      window.Echo.private(`chat.${this.selectedChatId}`)
          .listen('MessageSent', (e) => {
            this.messages.push({
              message: e.message.message,
              user: e.message.user
            });
          });
    },
    selectChat(chatId) {
      this.selectedChatId = chatId;
      this.fetchMessages();
      this.listenForMessages();
    },
    sendMessage() {
      axios.post('/send-message', {
        chat_id: this.selectedChatId,
        message: this.newMessage
      }).then(() => {
        this.newMessage = '';
      });
    }
  }
};
</script>

<style scoped>
.chat-message {
  margin-bottom: 10px;
}
</style>
