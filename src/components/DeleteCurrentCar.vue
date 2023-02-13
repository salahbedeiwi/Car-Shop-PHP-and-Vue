<template>
  <div>
    <div
      class="modal fade"
      tabindex="-1"
      :id="'deleteCar-' + carData.id"
      :class="'deleteCar-' + carData.id"
      :aria-labelledby="carData.id"
      aria-hidden="true"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ carData.name }}</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body carDataDetails">
            <img
              :src="getImageUrl(carData.image)"
              class="rounded d-block mx-auto img-fluid"
              :title="carData.name"
              :alt="carData.name"
            />
            <h4 class="text-center">
              {{ carData.name }} {{ carData.modelYear }}
            </h4>
            <hr class="bg-info" />
            <div>
              <div class="float-start">
                <button
                  class="btn btn-outline-warning"
                  type="button"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                >
                  No, Don't Delete
                </button>
              </div>
              <div class="float-end">
                <button
                  class="btn btn-outline-danger"
                  type="button"
                  @click="deleteCar(carData)"
                >
                  Yes, Delete Now
                </button>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="row d-grid gap-2 col-auto d-block mx-auto">
              <div
                class="alert alert-success my-3"
                role="alert"
                v-if="resultSuccess"
              >
                {{ resultSuccessMsg }}
              </div>
              <div class="alert alert-danger my-3" role="alert" v-if="resultErr">
                {{ resultErrMsg }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "DeleteCurrentCar",
  props: {
    carData: Object,
  },
  data() {
    return {
      resultSuccess: false,
      resultSuccessMsg: "",
      resultErr: false,
      resultErrMsg: "",
    };
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
    async deleteCar(obj) {
      let fd = new FormData();
      fd.append("name", obj.name);
      fd.append("id", obj.id);
      fd.append("image", obj.image);
      for (let pair of fd.entries()) {
        console.log(pair[0] + ": " + pair[1]);
      }
      let res = await axios.post(
        "http://localhost/php/vue_php_car_shop/src/api/cars.php?action=deleteCar",
        fd
      );
      const resData = res.data;
      if (res.status == 200) {
        if (resData.error) {
          this.resultErr = true;
          this.resultErrMsg = resData.message;
          this.resultSuccess = false;
          this.resultSuccessMsg = "";
        } else {
          this.resultErr = false;
          this.resultErrMsg = "";
          this.resultSuccess = true;
          this.resultSuccessMsg = resData.message;
          let res2 = await axios.get(
            "http://localhost/php/vue_php_car_shop/src/api/cars.php?action=read"
          );
          setTimeout(() => {
            const resData2 = res2.data;
            this.$emit("updateCars", resData2.cars);
            this.hideCurrentModal("deleteCar-" + this.carData.id);
          }, 2000);
        }
      }
    },
    hideCurrentModal(id) {
      document.getElementsByTagName("body")[0].removeAttribute("class");
      document.getElementsByTagName("body")[0].removeAttribute("style");

      //remove modal-backdrop element
      const elements = document.getElementsByClassName("modal-backdrop");
      while (elements.length > 0) elements[0].remove();

      //remove modal html
      const currentModal = document.getElementsByClassName(id);
      while (currentModal.length > 0) currentModal[0].remove();
    },
  },
};
</script>

<style>
.carDataDetails {
  padding: 10px;
  margin-bottom: 10px;
  border: 10px solid #eee;
}
.carDataDetails img {
  width: 320px;
  height: 215px;
  margin-bottom: 10px;
}
.bw {
  overflow-wrap: break-word;
}
</style>
