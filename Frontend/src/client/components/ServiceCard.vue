<template>
  <div class="service-card" :class="{ selected: isSelected }" @click="selectService">
    <div class="service-info">
      <h3>{{ service.name }}</h3>
      <p>{{ service.price }} руб.</p>
      <p>Длительность: {{ service.duration }} мин</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    service: {
      type: Object,
      required: true
    },
    isSelected: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    selectService() {
      this.$emit('select', this.service);
    }
  }
};
</script>

<style scoped>
.service-card {
  display: flex;
  align-items: center;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 10px;
  margin: 10px;
  width: 250px;
  cursor: pointer;
  transition: background-color 0.3s;
  background-color: #fff;
}

.service-card:hover, .service-card.selected {
  background-color: #f0f0f0;
}

.service-info h3 {
  font-size: 16px;
  margin: 0;
}

.service-info p {
  font-size: 14px;
  margin: 0;
  color: #666;
}
</style>
