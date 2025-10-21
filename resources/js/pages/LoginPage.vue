<template>
    <div class="flex items-center justify-center h-screen bg-black">
        <div class="bg-white h-[65%] w-[50%] rounded-2xl flex">
            <!-- Thumbnail -->
            <div class="m-2 w-[600px] rounded-xl overflow-hidden">
                <div class="w-full h-full overflow-hidden rounded-xl">
                    <img class="object-cover w-full h-full transition-all duration-300 cursor-pointer hover:scale-105 hover:blur-sm"
                        :src="thumbnailSrc" alt="login thumbnail" />
                </div>
            </div>

            <!-- Login content -->
            <div class="flex flex-col items-center justify-center w-full gap-10">
                <h1 class="text-4xl font-bold text-center">Đăng nhập vào Rhythm Game</h1>

                <div class="flex flex-col gap-3">
                    <button @click="redirectToGoogle"
                        class="flex gap-4 border border-gray-300 w-[450px] p-5 rounded-2xl hover:border-gray-800 transition-all duration-200">
                        <img class="w-8" :src="googleIcon" alt="google icon" />
                        <p class="text-xl">Tiếp tục với Google</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            thumbnailSrc: "/assets/images/login_thumbnail.png",
            googleIcon: "/assets/images/google.png",
        };
    },
    mounted() {
        const queryString = window.location.href.split('?')[1];
        const token = new URLSearchParams(queryString).get('code');
        if (token) {
            sessionStorage.setItem('token', token);
            this.$router.push('/home');
        }
    },
    methods: {
        async redirectToGoogle() {
            try {
                const res = await axios.get("http://localhost:8000/api/auth/google/redirect");
                if (res.data.success && res.data.data) {
                    window.location.href = res.data.data;
                } else {
                    alert("Không thể khởi tạo đăng nhập Google.");
                }
            } catch (err) {
                console.error(err);
                alert("Đã xảy ra lỗi khi khởi tạo đăng nhập Google.");
            }
        },
    },
};
</script>

<style scoped></style>