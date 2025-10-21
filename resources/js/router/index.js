import { createRouter, createWebHistory } from "vue-router";
import LoginPage from "../pages/LoginPage.vue";
import HomePage from "../pages/HomePage.vue";

const routes = [
    {
        path: "/login",
        component: LoginPage,
        name: "LoginPage",
    },
    {
        path: "/home",
        component: HomePage,
        name: "HomePage",
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
