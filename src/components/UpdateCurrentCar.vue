<template>
  <div class="container">
    <br />
    <button @click="goHome()" class="btn btn-success">Go Back</button>
    <h1 class="text-center">Update Current Car</h1>
    <div>
      <div v-if="!is_DB_Connected">
        <div role="alert" class="alert alert-danger fade show">
          {{ db_connection_msg }}
        </div>
      </div>
      <!-- Add New Car Form -->
      <form v-else method="post" action="#" enctype="multipart/form-data">
        <div class="row g-3 align-items-center mb-3">
          <div class="col-auto d-block mx-auto">
            <div
              class="form-floating"
              :class="{ 'form-data-error': carNameErr }"
            >
              <input
                type="text"
                id="carShopName"
                placeholder="Car Name"
                @input="validateCarName($event)"
                @change="validateCarName($event)"
                class="form-control w300"
                v-model.trim="carName"
              />
              <label for="carShopName">Car Name</label>
              <span class="error-feedback" v-if="carNameErr">
                {{ carNameMsg }}
              </span>
            </div>
          </div>
        </div>
        <div class="row g-3 align-items-center mb-3">
          <div class="col-auto d-block mx-auto">
            <div
              class="form-floating"
              :class="{ 'form-data-error': carPriceErr }"
            >
              <input
                type="text"
                id="carShopPrice"
                placeholder="Car Price"
                class="form-control w300"
                @input="validateCarPrice($event)"
                @change="validateCarPrice($event)"
                v-model.trim="carPrice"
              />
              <label for="carShopPrice">Car Price</label>
              <span class="error-feedback" v-if="carPriceErr">
                {{ carPriceMsg }}
              </span>
            </div>
          </div>
        </div>
        <div class="row g-3 align-items-center mb-3">
          <div class="col-auto d-block mx-auto">
            <div
              class="form-floating"
              :class="{ 'form-data-error': carModelYearErr }"
            >
              <input
                type="text"
                id="carShopModelYear"
                placeholder="Car ModelYear"
                class="form-control w300"
                @input="validateCarModelYear($event)"
                @change="validateCarModelYear($event)"
                v-model.trim="carModelYear"
              />
              <label for="carShopModelYear">Car Model Year</label>
              <span class="error-feedback" v-if="carModelYearErr">
                {{ carModelYearMsg }}
              </span>
            </div>
          </div>
        </div>
        <div class="row g-3 align-items-center mb-3">
          <div class="col-auto d-block mx-auto">
            <div
              class="form-floating"
              :class="{ 'form-data-error': carImageErr }"
              v-if="!image"
            >
              <input
                type="file"
                id="carShopImage"
                placeholder="Car Image"
                class="form-control w300 customFileField"
                @input="validateCarImage()"
                @change="validateCarImage()"
                @click="validateCarImage()"
                ref="carImage"
              />
              <label for="carShopImage">Car Image</label>
              <span class="error-feedback" v-if="carImageErr">
                {{ carImageMsg }}
              </span>
            </div>
            <div v-else-if="image && newImage">
              <span>Uploaded Car Image: </span><br />
              <img :src="image" class="w150 rounded" />
              <br />
              <button
                class="btn btn-outline-danger my-2"
                @click="removeImage()"
              >
                Remove Car Image
              </button>
            </div>
            <div v-else>
              <span>Uploaded Car Image: </span><br />
              <img :src="getImageUrl(image)" class="w150 rounded" />
              <br />
              <button
                class="btn btn-outline-danger my-2"
                @click="removeImage()"
              >
                Remove Car Image
              </button>
            </div>
          </div>
        </div>
        <div class="row g-3 align-items-center mb-3">
          <div class="col-auto d-block mx-auto">
            <div
              class="form-floating"
              :class="{ 'form-data-error': carDescriptionErr }"
            >
              <textarea
                id="carShopDescription"
                placeholder="Car Description"
                class="form-control w300 h125"
                @keyup="validateCarDescription($event)"
                @change="validateCarDescription($event)"
                v-model.trim="carDescription"
              ></textarea>
              <label for="carShopDescription">Car Description</label>
              <span class="error-feedback" v-if="carDescriptionErr">
                {{ carDescriptionMsg }}
              </span>
            </div>
          </div>
        </div>
        <div class="row d-grid gap-2 w300 col-auto d-block mx-auto mb-3">
          <button class="btn btn-outline-warning" @click.prevent="UpdateCurrentCar()">
            Update Current Car
          </button>
          <button
            @click.prevent="resetFormError()"
            class="btn btn-outline-primary"
          >
            Reset
          </button>
        </div>
        <div class="row d-grid gap-2 w300 col-auto d-block mx-auto mb-3">
          <div class="alert alert-success" role="alert" v-if="resultSuccess">
            {{ resultSuccessMsg }}
          </div>
          <div class="alert alert-danger" role="alert" v-if="resultErr">
            {{ resultErrMsg }}
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "UpdateCurrentCar",
  data() {
    return {
      carName: "",
      carNameErr: false,
      carNameMsg: "",
      isCarNameValidated: false,
      carPrice: "",
      carPriceErr: false,
      carPriceMsg: "",
      isCarPriceValidated: false,
      carModelYear: "",
      carModelYearErr: false,
      carModelYearMsg: "",
      isCarModelYearValidated: false,
      carImage: "",
      carImageErr: false,
      carImageMsg: "",
      isCarImageValidated: false,
      carDescription: "",
      carDescriptionErr: false,
      carDescriptionMsg: "",
      isCarDescriptionValidated: false,
      resultSuccess: false,
      resultSuccessMsg: "",
      resultErr: false,
      resultErrMsg: "",
      image: "",
      is_DB_Connected: "",
      db_connection_msg: "",
      oldUploadedImage: "",
      currentCarId: "",
      newImage: false,
    };
  },
  mounted() {
    //check if currentCar does exist
    let checkCurrentCar = localStorage.getItem("currentCar");
    if (checkCurrentCar) {
      this.dbConnection();
      this.currentCarId = JSON.parse(checkCurrentCar).id;
      this.carName = JSON.parse(checkCurrentCar).name;
      this.carPrice = JSON.parse(checkCurrentCar).price;
      this.carModelYear = JSON.parse(checkCurrentCar).modelYear;
      this.carDescription = JSON.parse(checkCurrentCar).description;
      this.carImage = JSON.parse(checkCurrentCar).image;
      this.image = JSON.parse(checkCurrentCar).image;
      this.isCarNameValidated = true;
      this.isCarPriceValidated = true;
      this.isCarModelYearValidated = true;
      this.isCarImageValidated = true;
      this.isCarDescriptionValidated = true;
      this.oldUploadedImage = JSON.parse(checkCurrentCar).image;
    } else {
      this.$router.push({ name: "Home" });
    }
  },
  methods: {
    getImageUrl(imgName) {
      let img = require.context("@/assets/cars/");
      return img("./" + imgName);
    },
    goHome() {
      this.$router.push({ name: "Home" });
    },
    //validate car name
    validateCarName(e) {
      let val = e.target.value;
      this.validateCarNameInput(val);
    },
    validateCarNameInput(val) {
      if (val == "") {
        this.carNameErr = true;
        this.isCarNameValidated = false;
        this.carNameMsg = "Must Enter Car Name";
      } else if (val != "" && !isNaN(val)) {
        this.carNameErr = true;
        this.isCarNameValidated = false;
        this.carNameMsg = "Car Name Must be Real Text";
      } else if (val.length > 20) {
        this.carNameErr = true;
        this.isCarNameValidated = false;
        this.carNameMsg = "Car Name Must be 20 Chars or Less";
      } else {
        this.carNameErr = false;
        this.isCarNameValidated = true;
        this.carNameMsg = "";
      }
    },
    validateCarPrice(e) {
      let val = e.target.value;
      this.validateCarPriceInput(val);
    },
    validateCarPriceInput(val) {
      if (val == "") {
        this.carPriceErr = true;
        this.isCarPriceValidated = false;
        this.carPriceMsg = "Must Enter Car Price";
      } else if (val != "" && isNaN(val)) {
        this.carPriceErr = true;
        this.isCarPriceValidated = false;
        this.carPriceMsg = "Car Price has to be number";
      } else if (val < 0) {
        this.carPriceErr = true;
        this.isCarPriceValidated = false;
        this.carPriceMsg = "Car Price Can not be negative";
      } else if (val == 0 || val === 0.0) {
        this.carPriceErr = true;
        this.isCarPriceValidated = false;
        this.carPriceMsg = "Car Price Can not be 0";
      } else {
        this.carPriceErr = false;
        this.isCarPriceValidated = true;
        this.carPriceMsg = "";
      }
    },
    validateCarModelYear(e) {
      let val = e.target.value;
      this.validateCarModelYearInput(val);
    },
    validateCarModelYearInput(val) {
      if (val == "") {
        this.carModelYearErr = true;
        this.isCarModelYearValidated = false;
        this.carModelYearMsg = "Must Enter Car Model Year. Ex: 2006";
      } else if (/^[0-9]+$/.test(val) === false) {
        this.carModelYearErr = true;
        this.isCarModelYearValidated = false;
        this.carModelYearMsg = "Only Numbers. Ex: 2006";
      } else if (val.length < 4 || val.length > 4) {
        this.carModelYearErr = true;
        this.isCarModelYearValidated = false;
        this.carModelYearMsg = "Must Enter Valid Year. Ex: 2006";
      } else {
        this.carModelYearErr = false;
        this.isCarModelYearValidated = true;
        this.carModelYearMsg = "";
      }
    },
    validateCarDescription(e) {
      let val = e.target.value;
      this.validateCarDescriptionInput(val);
    },
    validateCarDescriptionInput(val) {
      if (val == "") {
        this.carDescriptionErr = true;
        this.isDescriptionearValidated = false;
        this.carDescriptionMsg = "Must Enter Car Description";
      } else if (val.length > 100) {
        this.carDescriptionErr = true;
        this.isDescriptionearValidated = false;
        this.carDescriptionMsg = "Description has to be 100 Chars or less..";
      } else {
        this.carDescriptionErr = false;
        this.isCarDescriptionValidated = true;
        this.carDescriptionMsg = "";
      }
    },
    validateFileExtension(id) {
      let fileInput = document.getElementById(id);
      let filePath = fileInput.value;
      //Allowing file type
      let allowExtensions = /(\.jpg|\.png|\.jpeg|\.gif)$/i;
      if (!allowExtensions.exec(filePath)) {
        return false;
      } else {
        return true;
      }
    },
    createImage(file) {
      new Image();
      let reader = new FileReader();
      reader.onload = (e) => {
        console.log(e.target.result);
        this.image = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    removeImage() {
      this.image = "";
      setTimeout(() => {
        this.validateCarImage();
      }, 500);
    },
    validateCarImage() {
      if (this.$refs.carImage.files[0]) {
        if (this.validateFileExtension("carShopImage") == true) {
          this.newImage = true;
          this.carImageErr = false;
          this.isCarImageValidated = true;
          this.carImageMsg = "";
          this.createImage(this.$refs.carImage.files[0]);
          this.carImage = this.$refs.carImage.files[0];
        } else {
          this.newImage = false;
          this.carImageErr = true;
          this.isCarImageValidated = false;
          this.carImageMsg = "Car Image has to be : PNG, JPEG, JPG, or GIF";
        }
      } else {
        this.newImage = false;
        this.carImageErr = true;
        this.isCarImageValidated = false;
        this.carImageMsg = "Must Select Car Image";
      }
    },
    async UpdateCurrentCar() {
      //if all validated successfully
      if (
        this.isCarNameValidated &&
        this.isCarPriceValidated &&
        this.isCarModelYearValidated &&
        this.isCarImageValidated &&
        this.isCarDescriptionValidated
      ) {
        console.log("Validated Successfully");
        //show Result
        let fd = new FormData();
        fd.append("id", this.currentCarId);
        fd.append("name", this.carName);
        fd.append("price", this.carPrice);
        fd.append("description", this.carDescription);
        fd.append("yearModel", this.carModelYear);
        fd.append("image", this.carImage);
        fd.append("oldImage", this.oldUploadedImage);
        if (!this.newImage) {
          console.log("use old image");
          fd.append("newImageUploaded", "f");
        } else {
          console.log("use new image");
          fd.append("newImageUploaded", "t");
        }
        let rst = await axios.post(
          `http://localhost/php/vue_php_car_shop/src/api/cars.php?action=updateCar`,
          fd
        );
        console.log(rst);
        const resultData = rst.data;
        if (rst.status == 200) {
          if (resultData.error) {
            this.resultErrMsg = resultData.message;
            this.resultErr = true;
            this.resultSuccessMsg = "";
            this.resultSuccess = false;
          } else {
            //if everything is okay
            this.resultErrMsg = "";
            this.resultErr = false;
            this.resultSuccessMsg = resultData.message;
            this.resultSuccess = true;
            //remember to clear out localstorage
            setTimeout(() => {
              this.$router.push({ name: "Home" });
              localStorage.clear();
            }, 2000);
          }
        }
        /*
        //print formdata in console
        for (let pair of fd.entries()) {
          console.log(pair[0] + ", " + pair[1]);
        }
        //print formdata in network
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "https://localhost:8080/add/new-car", true);
        xhr.send(fd);
        */
      } else {
        console.log("Failed Validating Form");
        this.resultSuccessMsg = "";
        this.resultSuccess = false;

        this.resultErrMsg = "Failed Validating Form";
        this.resultErr = true;

        this.validateCarNameInput(this.carName);
        this.validateCarPriceInput(this.carPrice);
        this.validateCarModelYearInput(this.carModelYear);
        this.validateCarDescriptionInput(this.carDescription);
        this.validateCarImage();
      }
    },
    resetFormError() {
      location.reload();
    },
    async dbConnection() {
      let res = await axios.get(
        "http://localhost/php/vue_php_car_shop/src/api/cars.php"
      );
      const resData = res.data;
      this.is_DB_Connected = resData.is_db_connected;
      this.db_connection_msg = resData.connection_msg;
    },
  },
};
</script>

<style>
.w300 {
  width: 300px !important;
}
.w150 {
  width: 150px !important;
}
.h125 {
  height: 125px !important;
}
.form-data-error {
  color: red;
}
.form-data-error input,
.form-data-error textarea {
  border-color: red;
}
.error-feedback {
  padding-left: 3px;
  font-size: 0.9rem;
}
.customFileField {
  padding-left: 24px !important;
  padding-top: 38px !important;
  padding-bottom: 10px !important;
  height: 75px !important;
}
</style>
