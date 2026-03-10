<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    content: '',
    image: null,
});

const submit = () => {
    // Nó sẽ tự map đến đúng Route::post('news') ở trên
    form.post(route('news.store'), {
        forceFormData: true, // Quan trọng để upload file
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <input v-model="form.title" type="text" />
        <textarea v-model="form.content"></textarea>
        <input type="file" @input="form.image = $event.target.files[0]" />
        
        <button :disabled="form.processing">Lưu bài viết</button>
    </form>
</template> 