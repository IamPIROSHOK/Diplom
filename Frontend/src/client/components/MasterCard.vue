<template>
  <div :class="['master-card', { 'selected': isSelected }]" @click="selectMaster">
    <img :src="master.photo" alt="Master Photo" class="master-photo">
    <div class="master-info">
      <h3>{{ master.name }}</h3>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    master: {
      type: Object,
      required: true
    },
    isSelected: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    selectMaster() {
      this.$emit('select', this.master);
    }
  }
};
</script>

<style scoped>
.master-card {
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

.master-card:hover, .master-card.selected {
  background-color: #f0f0f0;
}

.master-photo {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-right: 10px;
}

.master-info h3 {
  font-size: 16px;
  margin: 0;
}
</style>
