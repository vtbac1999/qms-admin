<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios"; // Đảm bảo bạn đã cài axios: npm install axios

const props = defineProps({ settings: Object });

// Quản lý Preview cho từng loại ảnh
const logoPreview = ref(props.settings.logo || null);
const bannerPreview = ref(props.settings.banner || null);

// Trạng thái đang upload lên API bên thứ 3
const isUploading = ref(false); 

const form = useForm({
  site_name: props.settings.site_name || "",
  logo: props.settings.logo || "", // Lưu URL từ API
  banner: props.settings.banner || "", // Lưu URL từ API
  footer_text: props.settings.footer_text || "",
});

/**
 * Xử lý Upload ảnh lên API bên thứ 3
 */
const handleFileChange = async (e, field) => {
  const file = e.target.files[0];
  if (!file) return;

  // 1. Tạo preview nhanh (Base64) để người dùng thấy ngay
  const localUrl = URL.createObjectURL(file);
  if (field === 'logo') logoPreview.value = localUrl;
  if (field === 'banner') bannerPreview.value = localUrl;

  // 2. Chuẩn bị FormData để gửi lên API bên thứ 3
  isUploading.value = true;
  const formData = new FormData();
  formData.append("image", file); // Kiểm tra key 'image' này có đúng với API của bạn không

  try {
    const response = await axios.post(
      "https://upload.vtbac.io.vn/upload", // Thay bằng API của bạn
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
          "x-api-key":"bacdeptraivl"
        },
      }
    );

    // 3. Lấy URL trả về từ API và cập nhật vào form
    // Giả sử API trả về { data: { url: '...' } } - Hãy chỉnh lại cho đúng cấu trúc API của bạn
    const uploadedUrl = response.data?.url || response.data?.data?.url; 
    
    if (uploadedUrl) {
        form[field] = uploadedUrl;
        console.log(`Đã nhận URL cho ${field}:`, uploadedUrl);
    }
  } catch (error) {
    console.error("Lỗi upload bên thứ 3:", error);
    alert("Không thể upload ảnh, vui lòng thử lại!");
    // Reset lại preview nếu lỗi
    if (field === 'logo') logoPreview.value = props.settings.logo;
    if (field === 'banner') bannerPreview.value = props.settings.banner;
  } finally {
    isUploading.value = false;
  }
};

const submit = () => {
  // Gửi các URL đã nhận được về Laravel để lưu vào DB
  form.post(route("settings.update"), {
    preserveScroll: true,
    onSuccess: () => {
        // Có thể thêm thông báo thành công ở đây
    },
  });
};
</script>

<template>
  <Head title="Cấu hình hệ thống" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Cấu hình hệ thống
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="space-y-6">
          
          <div class="bg-white shadow sm:rounded-lg border border-gray-100">
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
              <h3 class="text-lg font-medium text-gray-900">Hình ảnh & Thương hiệu</h3>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Logo Website</label>
                <div class="flex items-center space-x-6">
                  <div class="relative shrink-0">
                    <img v-if="logoPreview" :src="logoPreview" class="h-20 w-20 object-contain rounded-lg border p-2 bg-gray-50" />
                    <div v-if="isUploading" class="absolute inset-0 bg-white/50 flex items-center justify-center rounded-lg">
                        <div class="w-5 h-5 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                  </div>
                  <input type="file" @change="handleFileChange($event, 'logo')" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                </div>
              </div>

              <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700">Banner Chính</label>
                <div class="space-y-3">
                  <div class="relative w-full h-32 bg-gray-50 rounded-lg border overflow-hidden">
                    <img v-if="bannerPreview" :src="bannerPreview" class="w-full h-full object-cover" />
                    <div v-if="isUploading" class="absolute inset-0 bg-black/10 flex items-center justify-center">
                        <div class="px-3 py-1 bg-white rounded-full text-xs font-bold animate-pulse">ĐANG TẢI...</div>
                    </div>
                  </div>
                  <input type="file" @change="handleFileChange($event, 'banner')" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end space-x-4 sticky bottom-6 z-10">
            <div v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">✓ Đã lưu thành công</div>
            
            <button
              type="submit"
              :disabled="form.processing || isUploading"
              class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-md font-semibold uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50 transition shadow-lg"
            >
              <span v-if="isUploading">Đang đợi upload ảnh...</span>
              <span v-else-if="form.processing">Đang lưu...</span>
              <span v-else>Lưu cấu hình ngay</span>
            </button>
          </div>

        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>