<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">Мої заявки</h1>
                        <a
                            :href="route('client.orders.create')"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        >
                            + Створити заявку
                        </a>
                    </div>

                    <!-- Вкладки -->
                    <div class="flex gap-4 mb-4">
                        <button
                            @click="activeTab = 'active'" :class="activeTab === 'active' ? 'bg-blue-500 text-white' : 'bg-gray-200'" class="px-4 py-2 rounded">
                            Активні
                        </button>
                        <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'bg-blue-500 text-white' : 'bg-gray-200'" class="px-4 py-2 rounded">
                            Завершені
                        </button>
                    </div>

                    <!-- Список заявок -->
                    <div v-if="orders.data.length">
                        <div v-for="order in orders.data" :key="order.id" class="border-b border-gray-200 py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-semibold">
                                        <a :href="route('orders.show', order.id)" class="text-blue-600 hover:underline">{{ order.title }}</a>
                                    </h2>
                                    <p class="text-gray-600 mt-1">{{ order.description || 'Опис відсутній' }}</p>
                                    <div class="flex gap-2 mt-2">
                                        <span v-for="tag in order.tags":key="tag.id" class="bg-gray-200 text-gray-700 text-sm px-2 py-1 rounded">
                                            {{ tag.name }}
                                        </span>
                                    </div>
                                    <div class="flex gap-4 mt-2 text-sm text-gray-500">
                                        <span>💰 {{ order.price ?? 'Договірна' }} ₴</span>
                                        <span>👤 {{ order.client?.name || 'Невідомий' }}</span>
                                        <span v-if="order.deadline">📅 {{ new Date(order.deadline).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span :class="getStatusClass(order.status)" class="text-sm px-3 py-1 rounded-full">
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 text-gray-500">
                        У вас поки немає заявок.
                    </div>

                    <!-- Пагінація -->
                    <div class="mt-6">
                        <p class="text-sm text-gray-600">
                            Показано {{ orders.from }}–{{ orders.to }} з {{ orders.total }} заявок
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
    activeTab: String,
});

const activeTab = ref(props.activeTab || 'active');

watch(activeTab, (newTab) => {
    router.get('/client/orders', { status: newTab === 'active' ? 'active' : 'completed' }, {
        preserveState: true,
        replace: true,
    });
});

const getStatusText = (status) => {
    const map = {
        new: 'Нова',
        in_progress: 'В роботі',
        ready: 'Готова',
        completed: 'Завершена',
        cancelled: 'Скасована',
    };
    return map[status] || status;
};

const getStatusClass = (status) => {
    const map = {
        new: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        ready: 'bg-green-100 text-green-800',
        completed: 'bg-gray-100 text-gray-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return map[status] || 'bg-gray-100 text-gray-800';
};
</script>
