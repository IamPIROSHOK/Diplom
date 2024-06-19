<template>
  <div class="service-card" :class="{ selected: isSelected }" @click="selectService">
    <h3>{{ service.name }}</h3>
    <p>{{ service.price }} руб.</p>
    <p>Длительность: {{ service.duration }} мин</p>
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

.service-card h3 {
  font-size: 18px;
  margin: 0 0 10px;
}

.service-card p {
  margin: 5px 0;
}
</style>
