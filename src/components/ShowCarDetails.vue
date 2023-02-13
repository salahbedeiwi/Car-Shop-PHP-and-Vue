<template>
  <div>
    <div
      class="modal fade"
      tabindex="-1"
      :id="'showCarDetails' + carData.id"
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
            <p class="text-muted bw">Description: {{ carData.description }}</p>
            <div>
              <div class="float-start">
                <button class="btn btn-outline-success">
                  {{ convertNumToPrice(carData.price) }}
                </button>
              </div>
              <div class="float-end">
                <button
                  class="btn btn-outline-primary"
                  type="button"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                >
                  Done Reading
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ShowCarDetails",
  props: {
    carData: Object,
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
