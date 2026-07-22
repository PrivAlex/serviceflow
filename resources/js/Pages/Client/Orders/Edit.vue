<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Редагувати заявку</h1>

                    <form @submit.prevent="submit">
                        <!-- Назва -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Назва</label>
                            <input
                                type="text"
                                v-model="form.title"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            />
                            <div v-if="errors.title" class="text-red-500 text-sm mt-1">
                                {{ errors.title }}
                            </div>
                        </div>

                        <!-- Опис -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Опис</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            ></textarea>
                        </div>

                        <!-- Ціна -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Ціна</label>
                            <input
                                type="number"
                                step="0.01"
                                v-model="form.price"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            />
                            <div v-if="errors.price" class="text-red-500 text-sm mt-1">
                                {{ errors.price }}
                            </div>
                        </div>

                        <!-- Дедлайн -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Дедлайн</label>
                            <input
                                type="date"
                                v-model="form.deadline"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            />
                        </div>

                        <!-- Теги -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Теги</label>
                            <select
                                v-model="form.tags"
                                multiple
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            >
                                <option v-for="tag in tags" :key="tag.id" :value="tag.id">
                                    {{ tag.name }}
                                </option>
                            </select>
                            <div v-if="errors.tags" class="text-red-500 text-sm mt-1">
                                {{ errors.tags }}
                            </div>
                        </div>

                        <!-- Кнопки -->
                        <div class="flex items-center justify-end mt-4">
                            <a
                                :href="route('client.orders.index')"
                                class="text-gray-600 hover:text-gray-900 mr-4"
                            >
                                Скасувати
                            </a>
                            <button
                                type="submit"
                                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                Оновити
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
    tags: Array,
});

const { errors } = usePage().props;

const form = useForm({
    title: props.order.title,
    description: props.order.description || '',
    price: props.order.price || '',
    deadline: props.order.deadline || '',
    tags: props.order.tags.map(tag => tag.id),
});

const submit = () => {
    form.put(route('client.orders.update', props.order.id), {
        onSuccess: () => {
            window.location.href = route('client.orders.index');
        },
    });
};
</script>
