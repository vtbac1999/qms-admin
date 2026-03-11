<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

const props = defineProps({ settings: Object });

// Logo chỉ có 1 ảnh
const logoPreview = ref(props.settings.logo || null);

// Banner là mảng nhiều ảnh
const bannerPreviews = ref(Array.isArray(props.settings.banner) ? props.settings.banner : []);

// Trạng thái upload
const isUploading = ref(false); 

const form = useForm({
  site_name: props.settings.site_name || "",
  logo: props.settings.logo || "",
  banner: Array.isArray(props.settings.banner) ? props.settings.banner : [], // Luôn khởi tạo là mảng
  footer_text: props.settings.footer_text || "",
});

/**
 * Xử lý Upload ảnh lên API bên thứ 3
 */
const handleFileChange = async (e, field) => {
  const file = e.target.files[0];
  if (!file) return;

  isUploading.value = true;
  const formData = new FormData();
  formData.append("image", file);

  try {
    const response = await axios.post(
      "https://upload.vtbac.io.vn/upload", 
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
          "x-api-key": "bacdeptraivl" // API Key của bạn
        },
      }
    );

    const uploadedUrl = response.data?.url || response.data?.data?.url; 
    
    if (uploadedUrl) {
        if (field === 'logo') {
            form.logo = uploadedUrl;
            logoPreview.value = uploadedUrl;
        } else if (field === 'banner') {
            // Thêm URL mới vào mảng thay vì ghi đè
            form.banner.push(uploadedUrl);
            bannerPreviews.value.push(uploadedUrl);
        }
    }
  } catch (error) {
    console.error("Lỗi upload:", error);
    alert("Không thể upload ảnh, vui lòng kiểm tra lại!");
  } finally {
    isUploading.value = false;
  }
};

/**
 * Hàm xóa ảnh trong mảng banner
 */
const removeBanner = (index) => {
    form.banner.splice(index, 1);
    bannerPreviews.value.splice(index, 1);
};

const submit = () => {
  form.post(route("settings.update"), {
    preserveScroll: true,
    onSuccess: () => {
        // Thông báo lưu thành công
    },
  });
};
</script>

<template>
  <Head title="Cấu hình hệ thống" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cấu hình hệ thống</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="space-y-6">
          
          <div class="bg-white shadow sm:rounded-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
              <h3 class="text-lg font-medium text-gray-900">Thông tin cơ bản</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tên Website</label>
                <input v-model="form.site_name" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Text</label>
                <input v-model="form.footer_text" type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
              </div>
            </div>
          </div>

          <div class="bg-white shadow sm:rounded-lg border border-gray-100">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
              <h3 class="text-lg font-medium text-gray-900">Logo Website</h3>
            </div>
            <div class="p-6">
              <div class="flex items-center space-x-6">
                <div class="relative shrink-0">
                  <img v-if="logoPreview" :src="logoPreview" class="h-24 w-24 object-contain rounded-lg border p-2 bg-gray-50 shadow-inner" />
                  <div v-else class="h-24 w-24 flex items-center justify-center border-2 border-dashed rounded-lg text-gray-400 text-xs text-center px-2">Chưa có logo</div>
                </div>
                <div class="flex-1 max-w-sm">
                  <input type="file" @change="handleFileChange($event, 'logo')" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                  <p class="mt-2 text-xs text-gray-500 italic">Khuyên dùng: Ảnh PNG hoặc SVG nền trong suốt.</p>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white shadow sm:rounded-lg border border-gray-100">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
              <h3 class="text-lg font-medium text-gray-900">Danh sách Banner Slider</h3>
              <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">{{ bannerPreviews.length }} ảnh</span>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="(url, index) in bannerPreviews" :key="index" class="relative group aspect-video bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                  <img :src="url" class="w-full h-full object-cover transition duration-300 group-hover:scale-110" />
                  <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                    <button @click.prevent="removeBanner(index)" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 shadow-lg">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                       </svg>
                    </button>
                  </div>
                </div>

                <label class="relative aspect-video flex flex-col items-center justify-center border-2 border-dashed border-indigo-200 rounded-lg bg-indigo-50/30 hover:bg-indigo-50 cursor-pointer transition border-spacing-4 group">
                  <div class="text-indigo-600 group-hover:scale-110 transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                  </div>
                  <span class="mt-2 text-xs font-bold text-indigo-500">Thêm Banner</span>
                  <input type="file" @change="handleFileChange($event, 'banner')" class="hidden" accept="image/*" />
                  
                  <div v-if="isUploading" class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-lg">
                    <div class="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-4 sticky bottom-6 z-10">
            <div v-if="form.recentlySuccessful" class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-bold shadow-sm animate-bounce">
                ✓ Lưu cấu hình thành công
            </div>
            
            <button
              type="submit"
              :disabled="form.processing || isUploading"
              class="inline-flex items-center px-8 py-3 bg-indigo-600 text-white rounded-md font-bold uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50 transition shadow-indigo-200 shadow-xl"
            >
              <span v-if="isUploading">Đang upload...</span>
              <span v-else-if="form.processing">Đang lưu dữ liệu...</span>
              <span v-else>Lưu tất cả thay đổi</span>
            </button>
          </div>

        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>