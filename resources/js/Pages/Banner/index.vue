<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ products: Array });
</script>

<template>
    <Head title="Quản lý sản phẩm" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow sm:rounded-lg">
                    <h1 class="text-xl font-bold mb-4">Danh sách sản phẩm</h1>
                    <table class="w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border">Tên</th>
                                <th class="p-2 border">Giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id">
                                <td class="p-2 border">{{ product.name }}</td>
                                <td class="p-2 border">{{ product.price }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>