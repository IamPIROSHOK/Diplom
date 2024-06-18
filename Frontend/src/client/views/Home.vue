<template>
  <section>
    <div class="row gx-0 d-block d-lg-flex flickity flickity-lg-none" data-flickity='{"watchCSS": true}'>
      <div class="col-12 d-flex flex-column bg-cover" style="background-image: url('/assets/images/slide-01.png'); min-height: 720px;">
        <div class="container h-100 d-flex align-items-center">
          <div class="text-container">
            <h1>Добро пожаловать <br class="br-none">в салон красоты!</h1>
            <p class="lead">Устали и истощены? Нужно расслабиться и привести в порядок свое тело и здоровье? Попробуйте наш салон красоты сегодня!</p>
            <div class="btn-wrap">
              <div class="group-xxl group-middle">
                <router-link to="/appointments" class="button button-primary">Записаться</router-link>
              </div>
            </div>
            <div class="contact-info">
              <p class="phone-title">Телефон: <a href="tel:10002347890" class="phone-number">+7(999)999-99-99</a></p>
              <p class="address">178 Западная 27-я улица, офис 527, Нью-Йорк, NY 10012, США</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <HomeMasters />
</template>

<script>
import HomeMasters from '../components/HomeMasters.vue';

export default {
  name: 'Home',
  components: {
    HomeMasters,
  },
};
</script>

<style scoped>
.text-container {
  color: #3d3d3d;
  text-align: left;
  max-width: 500px;
  padding-left: 23px;
}

.text-container h1 {
  font-size: 48px;
  font-weight: bold;
  color: #2b2b2b;
  margin-bottom: 20px;
}

.text-container p.lead {
  font-size: 18px;
  color: #2b2b2b;
  margin-bottom: 30px;
}

.btn-wrap {
  margin-bottom: 30px;
}

.button {
  display: inline-block;
  padding: 12px 24px;
  font-size: 16px;
  border-radius: 5px;
  text-decoration: none;
  margin-right: 10px;
  transition: background-color 0.3s, color 0.3s;
}

.button-primary {
  background-color: #ff6f61;
  color: white;
  border: none;
}

.button-primary:hover {
  background-color: #e65c50;
  color: white;
}

.button-secondary {
  background-color: white;
  color: #ff6f61;
  border: 2px solid #ff6f61;
}

.button-secondary:hover {
  background-color: #ffe6e2;
  color: #ff6f61;
}

.contact-info {
  margin-top: 20px;
}

.contact-info .phone-title {
  font-size: 18px;
  color: #2b2b2b;
}

.contact-info .phone-number {
  color: #ff6f61;
  text-decoration: none;
}

.contact-info .address {
  font-size: 14px;
  color: #2b2b2b;
}
</style>
