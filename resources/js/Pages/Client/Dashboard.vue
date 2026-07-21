<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">👋 Вітаємо, {{ $page.props.auth.user.name }}!</h1>
                    <p class="text-gray-600 mb-6">Це ваша клієнтська панель.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-700">Мої заявки</h3>
                            <p class="text-sm text-gray-600">Переглянути всі свої заявки</p>
                            <a :href="route('client.orders.my')" class="text-blue-500 hover:underline text-sm">Перейти →</a>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-700">Створити заявку</h3>
                            <p class="text-sm text-gray-600">Створити нову заявку</p>
                            <a :href="route('client.orders.create')" class="text-green-500 hover:underline text-sm">Створити →</a>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-purple-700">Доступні заявки</h3>
                            <p class="text-sm text-gray-600">Переглянути всі доступні заявки</p>
                            <a :href="route('orders.index')" class="text-purple-500 hover:underline text-sm">Переглянути →</a>
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
