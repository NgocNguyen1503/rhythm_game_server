<template>
    <div class="flex flex-col items-center justify-center h-screen bg-black text-white">
        <p v-if="loading">Processing</p>
        <p v-if="error" class="text-red-400">{{ error }}</p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "AuthPage",
    data() {
        return {
            loading: true,
            error: null,
        };
    },
    async mounted() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get("code");
            const state = urlParams.get("state");

            if (!code) {
                this.error = "Token not found!";
                this.loading = false;
                return;
            }

            const res = await axios.get("http://localhost:8000/api/auth/google/get-token", {
                params: { state },
            });

            if (res.data.code === 200) {
                const token = res.data.data;
                sessionStorage.setItem("access_token", token);
                window.location.href = "/home";
            } else {
                this.error = "Fail!";
            }
        } catch (err) {
            console.error(err);
            this.error = "Login failed";
        } finally {
            this.loading = false;
        }
    },
};
</script>