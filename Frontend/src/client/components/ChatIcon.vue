<template>
  <div class="chat-icon" @click="openChat">
    💬
  </div>
</template>

<script>
export default {
  methods: {
    openChat() {
      // Логика открытия чата
      alert("Открытие чата");
    }
  }
};
</script>

<style scoped>
.chat-icon {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 60px;
  height: 60px;
  background-color: #ff6f61;
  color: white;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  transition: background-color 0.3s, transform 0.3s;
}
.chat-icon:hover {
  background-color: #ff6f61;
  transform: scale(1.1);
}
</style>
