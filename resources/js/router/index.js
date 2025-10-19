import { createRouter, createWebHistory } from "vue-router";
import BaseComponent from "../components/BaseComponent.vue";
import LoginComponent from "../components/LoginComponent.vue";
import AuthPage from "../components/AuthPage.vue";
import HomePage from "../components/HomePage.vue";

const routes = [
    {
        path: "/test-vue",
        component: BaseComponent,
        name: "BaseComponent",
    },
    {
        path: "/login",
        component: LoginComponent,
        name: "LoginComponent",
    },
    {
        path: "/auth",
        component: AuthPage,
        name: "AuthPage",
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
