<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">👋 Вітаємо, {{ $page.props.auth.user.name }}!</h1>
                    <p class="text-gray-600 mb-6">Це адмін-панель.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-red-700">Всі заявки</h3>
                            <p class="text-sm text-gray-600">Управління всіма заявками</p>
                            <a :href="route('admin.orders.index')" class="text-red-500 hover:underline text-sm">Переглянути →</a>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-purple-700">Користувачі</h3>
                            <p class="text-sm text-gray-600">Управління користувачами</p>
                            <a href="#" class="text-purple-500 hover:underline text-sm">Переглянути →</a>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-yellow-700">Теги</h3>
                            <p class="text-sm text-gray-600">Управління тегами</p>
                            <a href="#" class="text-yellow-500 hover:underline text-sm">Переглянути →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';

const { auth } = usePage().props;
</script>
