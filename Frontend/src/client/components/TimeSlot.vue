<template>
  <div class="time-slot" :class="{ 'selected': isSelected }" @click="selectTimeSlot">
    {{ time }}
  </div>
</template>

<script>
export default {
  props: {
    time: {
      type: String,
      required: true
    },
    isSelected: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    selectTimeSlot() {
      this.$emit('select', this.time);
    }
  }
};
</script>

<style scoped>
.time-slot {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 80px;
  height: 40px;
  margin: 5px;
  border-radius: 5px;
  background-color: #f0f0f0;
  cursor: pointer;
  transition: background-color 0.3s;
}

.time-slot.selected {
  background-color: #e0e0e0;
}
</style>
