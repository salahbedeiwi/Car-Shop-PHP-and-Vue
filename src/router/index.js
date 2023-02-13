import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import AddNewCar from "@/components/AddNewCar.vue";
import UpdateCurrentCar from "@/components/UpdateCurrentCar.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/add/new-car",
    name: "AddNewCar",
    component: AddNewCar,
  },
  {
    path: "/update/current-car",
    name: "UpdateCurrentCar",
    component: UpdateCurrentCar,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
