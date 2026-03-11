<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({ product: Object });
const isUploading = ref(false); // Trạng thái chờ khi đang upload lên API thứ 3

const form = useForm({
    name: props.product?.name ?? '',
    price: props.product?.price ?? 0,
    category: props.product?.category ?? '',
    image_main: props.product?.image_main ?? '',
    images_sub: props.product?.images_sub ?? [],
    description: props.product?.description ?? '',
    specs: props.product?.specs ?? [{ key: '', value: '' }],
    document: props.product?.document ?? '',
});

/**
 * Hàm upload lên API bên thứ 3 
 * Ở đây tôi ví dụ dùng Cloudinary. Bạn thay URL và upload_preset của bạn vào nhé.
 */
const uploadFile = async (file) => {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', 'YOUR_UPLOAD_PRESET'); // Thay bằng preset của bạn

    try {
        const res = await axios.post('https://api.cloudinary.com/v1_1/YOUR_CLOUD_NAME/upload', formData);
        return res.data.secure_url; // Trả về URL HTTPS của file
    } catch (err) {
        console.error("Upload thất bại:", err);
        alert("Lỗi upload file, vui lòng kiểm tra lại cấu hình API");
        return null;
    }
};

// Xử lý upload ảnh chính
const handleMainImageUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    isUploading.value = true;
    const url = await uploadFile(file);
    if (url) form.image_main = url;
    isUploading.value = false;
};

// Xử lý upload tài liệu
const handleDocUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    isUploading.value = true;
    const url = await uploadFile(file);
    if (url) form.document = url;
    isUploading.value = false;
};

// Xử lý upload nhiều ảnh phụ (Gallery)
const handleSubImagesUpload = async (e) => {
    const files = Array.from(e.target.files);
    isUploading.value = true;
    for (const file of files) {
        const url = await uploadFile(file);
        if (url) form.images_sub.push(url);
    }
    isUploading.value = false;
};

const removeSpec = (index) => form.specs.splice(index, 1);
const addSpec = () => form.specs.push({ key: '', value: '' });
const removeImageSub = (index) => form.images_sub.splice(index, 1);

const submit = () => {
    if (isUploading.value) return; // Chặn submit khi đang upload dở

    if (props.product) {
        form.put(route('products.update', props.product.id));
    } else {
        form.post(route('products.store'));
    }
};
</script>

<template>
    <Head title="Quản lý sản phẩm" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ props.product ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto bg-white p-8 shadow rounded-lg">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Tên sản phẩm</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-md shadow-sm" required />
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Giá (VND)</label>
                            <input v-model="form.price" type="number" class="w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                    </div>

                    <div class="border p-4 rounded bg-gray-50">
                        <label class="block font-bold mb-2 text-sm">Hình ảnh chính</label>
                        <input type="file" @change="handleMainImageUpload" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        <div v-if="form.image_main" class="mt-3">
                            <img :src="form.image_main" class="h-32 w-32 object-cover rounded-md border" />
                        </div>
                    </div>

                    <div class="border p-4 rounded bg-gray-50">
                        <label class="block font-bold mb-2 text-sm">Hình ảnh phụ (Gallery)</label>
                        <input type="file" multiple @change="handleSubImagesUpload" class="mb-4 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        
                        <div class="grid grid-cols-4 gap-4">
                            <div v-for="(url, index) in form.images_sub" :key="index" class="relative group">
                                <img :src="url" class="h-24 w-full object-cover rounded-md border" />
                                <button @click="removeImageSub(index)" type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="border p-4 rounded bg-gray-50">
                        <label class="block font-bold mb-2">Thông số kỹ thuật</label>
                        <div v-for="(spec, index) in form.specs" :key="index" class="flex gap-2 mb-2">
                            <input v-model="spec.key" placeholder="Ví dụ: Công suất" class="w-1/3 border-gray-300 rounded-md" />
                            <input v-model="spec.value" placeholder="Ví dụ: 100W" class="flex-1 border-gray-300 rounded-md" />
                            <button @click="removeSpec(index)" type="button" class="text-red-500 font-bold px-2">X</button>
                        </div>
                        <button @click="addSpec" type="button" class="text-indigo-600 text-sm font-semibold hover:underline">+ Thêm thông số</button>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">Tài liệu (PDF/Document)</label>
                        <input type="file" @change="handleDocUpload" class="mt-1 block w-full" accept=".pdf,.doc,.docx" />
                        <p v-if="form.document" class="mt-2 text-xs text-green-600 truncate">File hiện tại: {{ form.document }}</p>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-indigo-600 text-white py-3 rounded-md font-bold hover:bg-indigo-700 transition disabled:bg-gray-400" 
                        :disabled="form.processing || isUploading"
                    >
                        <span v-if="isUploading">Đang tải file lên...</span>
                        <span v-else>Lưu sản phẩm</span>
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>