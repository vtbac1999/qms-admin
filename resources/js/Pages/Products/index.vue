<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    products: Object // Nhận từ paginate() của Controller
});

const deleteProduct = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
        router.delete(route('products.destroy', id));
    }
};

// Hàm hỗ trợ parse JSON cho ảnh phụ và thông số kỹ thuật
const parseJson = (data) => {
    try {
        return typeof data === 'string' ? JSON.parse(data) : data;
    } catch (e) {
        return [];
    }
};
</script>

<template>
    <Head title="Quản lý Sản phẩm" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">📦 Danh sách Sản phẩm</h2>
                <Link :href="route('products.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition shadow-sm text-sm font-medium">
                    + Thêm sản phẩm mới
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-200">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Sản phẩm</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase">Loại</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase text-right">Giá</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase text-center">Thông số</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="flex items-center">
                                        <div class="relative h-14 w-14 shrink-0">
                                            <img :src="product.image_main" class="h-full w-full rounded-md object-cover border border-gray-100 shadow-sm" alt="" />
                                            <span v-if="parseJson(product.images_sub).length > 0" class="absolute -top-2 -right-2 bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded-full border border-white">
                                                +{{ parseJson(product.images_sub).length }}
                                            </span>
                                        </div>
                                        <div class="ms-4">
                                            <div class="text-sm font-bold text-gray-900">{{ product.name }}</div>
                                            <div class="text-xs text-gray-500 truncate max-w-[200px]">ID: #{{ product.id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 text-sm text-gray-600">
                                    <span class="px-2 py-1 bg-gray-100 rounded-full text-[11px] font-semibold">{{ product.category }}</span>
                                </td>

                                <td class="p-4 text-sm font-mono font-bold text-right text-indigo-600">
                                    {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price) }}
                                </td>

                                <td class="p-4 text-center">
                                    <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded border border-green-200">
                                        {{ parseJson(product.specs).length }} thông số
                                    </span>
                                </td>

                                <td class="p-4 text-right space-x-3">
                                    <Link :href="route('products.edit', product.id)" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Sửa</Link>
                                    <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900 font-semibold text-sm">Xóa</button>
                                </td>
                            </tr>
                            <tr v-if="products.data.length === 0">
                                <td colspan="5" class="p-10 text-center text-gray-400">
                                    Hiện chưa có sản phẩm nào. Hãy bấm "Thêm sản phẩm mới".
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="products.links" class="p-4 border-t border-gray-100 flex justify-center">
                        <nav class="flex gap-1">
                            <Link v-for="(link, k) in products.links" :key="k" 
                                  :href="link.url || '#'" 
                                  v-html="link.label" 
                                  class="px-3 py-1 text-xs border rounded transition"
                                  :class="{ 'bg-indigo-600 text-white border-indigo-600': link.active, 'text-gray-400': !link.url, 'hover:bg-gray-50': link.url && !link.active }" 
                            />
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>