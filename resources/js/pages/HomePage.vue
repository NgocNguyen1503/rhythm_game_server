<template>
    <div class="min-h-screen bg-black text-white flex items-center justify-center px-6">
        <div class="w-full max-w-lg space-y-8">
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight">Thêm bài hát mới</h1>
                <p class="text-gray-400">Quản lý danh sách nhạc cho Rhythm Game</p>
            </div>
            <div class="backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-8 space-y-6 shadow-lg">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Tên bài hát</label>
                    <input v-model="song.name" type="text" placeholder="Nhập tên bài hát..."
                        class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Link nhạc YouTube</label>
                    <input v-model="song.audio" type="text" placeholder="Dán link YouTube..."
                        class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Giá (VNĐ)</label>
                    <input v-model.number="song.price" type="number" min="0" placeholder="Nhập giá..."
                        class="w-full px-3 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Thumbnail</label>
                    <input type="file" @change="onFileChange" accept="image/*"
                        class="w-full file:cursor-pointer cursor-pointer rounded-lg bg-white/10 border border-white/20 text-sm text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition" />
                    <div v-if="previewUrl" class="relative mt-3 group">
                        <img :src="previewUrl" alt="Preview Thumbnail"
                            class="w-full h-48 object-cover rounded-lg border border-white/20 shadow-sm" />
                        <button @click="removeThumbnail"
                            class="absolute cursor-pointer top-2 right-2 bg-black/60 hover:bg-black/80 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm transition"
                            title="Xoá ảnh">
                            ✕
                        </button>
                    </div>
                </div>
                <button @click="addNewSong" :disabled="loading"
                    class="w-full cursor-pointer py-3 rounded-lg font-medium bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed transition">
                    <span v-if="!loading">Thêm bài hát</span>
                    <span v-else>Đang xử lý...</span>
                </button>
            </div>
            <transition name="fade">
                <p v-if="message" class="text-center text-sm font-medium"
                    :class="success ? 'text-green-500' : 'text-red-500'">
                    {{ message }}
                </p>
            </transition>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            song: { name: "", audio: "", price: 0, thumbnail: null },
            previewUrl: null,
            message: "",
            success: false,
            loading: false,
        };
    },
    methods: {
        onFileChange(e) {
            const file = e.target.files[0];
            this.song.thumbnail = file;
            if (file) this.previewUrl = URL.createObjectURL(file);
        },
        removeThumbnail() {
            this.song.thumbnail = null;
            this.previewUrl = null;
        },
        async addNewSong() {
            if (!this.song.name || !this.song.audio || !this.song.thumbnail) {
                this.message = "Vui lòng nhập đầy đủ thông tin!";
                this.success = false;
                return;
            }

            this.loading = true;
            this.message = "";
            try {
                const token = sessionStorage.getItem("token");
                const formData = new FormData();
                formData.append("name", this.song.name);
                formData.append("audio", this.song.audio);
                formData.append("price", this.song.price);
                formData.append("thumbnail", this.song.thumbnail);

                const res = await axios.post(
                    "http://localhost:8000/api/add-new-song?input=song",
                    formData,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                if (res.data.success) {
                    this.message = "Bài hát đã được thêm và đang xử lý!";
                    this.success = true;
                    this.song = { name: "", audio: "", price: 0, thumbnail: null };
                    this.previewUrl = null;
                } else {
                    this.message = "Lỗi khi thêm bài hát.";
                    this.success = false;
                }
            } catch {
                this.message = "Đã xảy ra lỗi trong quá trình xử lý.";
                this.success = false;
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
