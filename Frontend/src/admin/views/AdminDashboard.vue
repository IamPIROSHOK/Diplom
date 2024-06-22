<template>
  <div class="container">
    <Navigation />

    <div class="main">
      <TopBar />

      <CardBox />

      <div class="details">
        <RecentOrders />
        <MasterList />
      </div>
    </div>
  </div>

</template>

<script>
import Navigation from '../components/Navigation.vue';
import TopBar from '../components/TopBar.vue';
import CardBox from '../components/CardBox.vue';
import RecentOrders from '../components/RecentOrders.vue';
import MasterList from '../components/MasterList.vue';

export default {
  name: 'AdminDashboard',
  components: {
    Navigation,
    TopBar,
    CardBox,
    RecentOrders,
    MasterList,
  },
};
</script>
