<template>
    <div class="min-h-screen bg-background text-foreground flex items-center justify-center px-6">
        <div class="w-full max-w-lg space-y-8">
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight">Thêm bài hát mới</h1>
                <p class="text-muted-foreground">Quản lý danh sách nhạc cho Rhythm Game</p>
            </div>

            <div class="bg-card border border-border rounded-xl p-8 space-y-6 shadow-sm">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Tên bài hát</label>
                    <input v-model="song.name" type="text" placeholder="Nhập tên bài hát..." class="input" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Link nhạc YouTube</label>
                    <input v-model="song.audio" type="text" placeholder="Dán link YouTube..." class="input" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Giá (VNĐ)</label>
                    <input v-model.number="song.price" type="number" min="0" placeholder="Nhập giá..." class="input" />
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Thumbnail</label>
                    <input type="file" @change="onFileChange" accept="image/*" class="input cursor-pointer" />

                    <!-- Preview -->
                    <div v-if="previewUrl" class="relative mt-3 group">
                        <img :src="previewUrl" alt="Preview Thumbnail"
                            class="w-full h-48 object-cover rounded-lg border border-border shadow-sm" />
                        <button @click="removeThumbnail"
                            class="absolute top-2 right-2 bg-black/60 hover:bg-black/80 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm transition"
                            title="Xoá ảnh">
                            ✕
                        </button>
                    </div>
                </div>

                <button @click="addNewSong" :disabled="loading" class="btn w-full">
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
    name: "AddSongPage",
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

<style scoped>
:root {
    --background: #0a0a0a;
    --foreground: #fafafa;
    --card: #111111;
    --border: #3a3a3a;
    --input-bg: #1e1e1e;
    --muted: #888888;
}

.bg-background {
    background-color: var(--background);
}

.text-foreground {
    color: var(--foreground);
}

.bg-card {
    background-color: var(--card);
}

.border-border {
    border-color: var(--border);
}

.text-muted-foreground {
    color: var(--muted);
}

/* --- Input --- */
.input {
    width: 100%;
    padding: 0.75rem 0.875rem;
    border-radius: 0.5rem;
    background-color: var(--input-bg);
    border: 1px solid var(--border);
    color: var(--foreground);
    font-size: 0.95rem;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.4);
}

.input::placeholder {
    color: #aaa;
}

.input:focus {
    border-color: #60a5fa;
    background-color: var(--input-bg);
    outline: none;
    box-shadow: 0 0 0 1.5px #60a5fa;
}

/* --- Button --- */
.btn {
    background-color: #2563eb;
    color: white;
    padding: 0.75rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: background-color 0.2s ease, opacity 0.2s ease;
}

.btn:hover:not(:disabled) {
    background-color: #1e40af;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* --- Animation --- */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>