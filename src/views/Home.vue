<template>
  <div class="container">
    <h1 class="text-center">Car Shop</h1>
    <div>
      <button @click="viewAddNewCar()" class="btn btn-success float-end">
        Add New Car
      </button>
      <div class="clearfix"></div>
      <br />
      <div v-if="!is_DB_Connected">
        <div role="alert" class="alert alert-danger fade show">
          {{ db_connection_msg }}
        </div>
      </div>
      <div v-else>
        <div
          role="alert"
          class="alert alert-warning fade show"
          v-if="emptyCars"
        >
          {{ emptyCars }}
        </div>
        <!-- Show my data -->
        <div class="justify-content-evenly row">
          <div
            class="col-12 col-sm-6 col-lg-4 each-car"
            v-for="(eachCar, i) in cars"
            :key="i"
          >
            <img
              :src="getImageUrl(eachCar.image)"
              class="rounded d-block mx-auto img-fluid"
              :title="eachCar.name"
              :alt="eachCar.name"
            />
            <h4 class="text-center">
              {{ eachCar.name }} {{ eachCar.modelYear }}
            </h4>
            <div>
              <div class="float-start">
                <button class="btn btn-outline-success">
                  {{ convertNumToPrice(eachCar.price) }}
                </button>
              </div>
              <div class="float-end">
                <button
                  class="btn btn-outline-primary"
                  type="button"
                  data-bs-toggle="modal"
                  :data-bs-target="'#showCarDetails' + eachCar.id"
                >
                  More Details
                </button>
                <ShowCarDetails :carData="eachCar" />
              </div>
            </div>
            <div class="clearfix"></div>
            <hr class="bg-success" />
            <div>
              <div class="float-start">
                <button
                  class="btn btn-danger"
                  type="button"
                  data-bs-toggle="modal"
                  :data-bs-target="'#deleteCar-' + eachCar.id"
                >
                  Delete
                </button>
                <DeleteCurrentCar :carData="eachCar" @updateCars="currentCars($events)"/>
              </div>
              <div class="float-end">
                <button type="button" class="btn btn-success" @click="goToUpdatePage(eachCar)">Update</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ShowCarDetails from "@/components/ShowCarDetails.vue";
import DeleteCurrentCar from "@/components/DeleteCurrentCar.vue";
import axios from "axios";
export default {
  name: "Home",
  components: {
    ShowCarDetails,
    DeleteCurrentCar,
  },
  data() {
    return {
      is_DB_Connected: "",
      db_connection_msg: "",
      emptyCars: "",
      cars: [],
    };
  },
  mounted() {
    this.dbConnection();
    this.myCars();
  },
  methods: {
    convertNumToPrice(num) {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
      }).format(num);
    },
    getImageUrl(imgName) {
      let img = require.context("@/assets/cars/");
      return img("./" + imgName);
    },
    viewAddNewCar() {
      this.$router.push({ name: "AddNewCar" });
    },
    goToUpdatePage(carData) {
      console.log(carData);
      localStorage.setItem("currentCar", JSON.stringify(carData));
      this.$router.push({ name: "UpdateCurrentCar" });
    },
    async dbConnection() {
      let res = await axios.get(
        "http://localhost/php/vue_php_car_shop/src/api/cars.php"
      );
      const resData = res.data;
      this.is_DB_Connected = resData.is_db_connected;
      this.db_connection_msg = resData.connection_msg;
    },
    async myCars() {
      let res = await axios.get(
        "http://localhost/php/vue_php_car_shop/src/api/cars.php?action=read"
      );
      const resData = res.data;
      if (res.status == 200) {
        if (resData.error) {
          this.emptyCars = resData.message;
        } else {
          this.cars = resData.cars;
        }
      }
    },
    //use this to update data coming from child component to this parent component on delete ea. car function
    currentCars(cars){
      this.cars = cars;
      this.myCars();
    }
  },
};
</script>
<style scoped>
.each-car {
  padding: 10px;
  margin-bottom: 10px;
  border: 10px solid #eee;
}
.each-car img {
  width: 320px;
  height: 215px;
  margin-bottom: 10px;
}
</style>
