<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

// Giả sử Controller trả về phân trang (paginate) thì dùng Object, nếu get() thì dùng Array
defineProps({ products: Object });

const deleteProduct = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        router.delete(route('products.destroy', id));
    }
};
</script>

<template>
    <Head title="Quản lý sản phẩm" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Danh sách sản phẩm
                </h2>
                <Link
                    :href="route('products.create')"
                    class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                >
                    + Thêm sản phẩm
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <table class="w-full border-collapse text-left text-sm">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="border-b p-4 font-medium">Hình ảnh</th>
                                    <th class="border-b p-4 font-medium">Tên sản phẩm</th>
                                    <th class="border-b p-4 font-medium text-right">Giá</th>
                                    <th class="border-b p-4 font-medium text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                                    <td class="p-4">
                                        <img 
                                            :src="product.image || 'https://via.placeholder.com/50'" 
                                            class="h-12 w-12 rounded object-cover shadow-sm"
                                            alt="product"
                                        />
                                    </td>
                                    <td class="p-4 font-medium text-gray-900">
                                        {{ product.name }}
                                    </td>
                                    <td class="p-4 text-right text-gray-700 font-mono">
                                        {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price) }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center space-x-3">
                                            <Link
                                                :href="route('products.edit', product.id)"
                                                class="text-indigo-600 hover:text-indigo-900 font-medium"
                                            >
                                                Sửa
                                            </Link>
                                            <button
                                                @click="deleteProduct(product.id)"
                                                class="text-red-600 hover:text-red-900 font-medium"
                                            >
                                                Xóa
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td colspan="4" class="p-8 text-center text-gray-500 italic">
                                        Chưa có sản phẩm nào được tạo.
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="products.links" class="mt-6 flex justify-center">
                            <nav class="flex space-x-1">
                                <Link
                                    v-for="(link, k) in products.links"
                                    :key="k"
                                    :href="link.url || '#'"
                                    v-html="link.label"
                                    class="px-3 py-1 text-sm border rounded"
                                    :class="{
                                        'bg-indigo-600 text-white border-indigo-600': link.active,
                                        'text-gray-500 bg-white hover:bg-gray-100': !link.active,
                                        'opacity-50 cursor-not-allowed': !link.url
                                    }"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>