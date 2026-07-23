<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Заголовок -->
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Мої заявки</h1>
                    </div>

                    <!-- Таби -->
                    <div class="flex gap-4 mb-6 border-b">
                        <button
                            @click="activeTab = 'active'"
                            class="pb-2 px-4 font-medium"
                            :class="activeTab === 'active' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                        >
                            Активні
                        </button>
                        <button
                            @click="activeTab = 'completed'"
                            class="pb-2 px-4 font-medium"
                            :class="activeTab === 'completed' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                        >
                            Завершені
                        </button>
                    </div>

                    <!-- Список заявок -->
                    <div v-if="filteredOrders.length > 0" class="space-y-4">
                        <div
                            v-for="order in filteredOrders"
                            :key="order.id"
                            class="border rounded-lg p-4 hover:shadow-md transition-shadow"
                        >
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <!-- Назва -->
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ order.title }}
                                    </h3>

                                    <!-- Опис -->
                                    <p class="text-gray-600 mt-1">
                                        {{ order.description || 'Опис відсутній' }}
                                    </p>

                                    <!-- Теги -->
                                    <div v-if="order.tags && order.tags.length" class="flex gap-2 flex-wrap mt-2">
                                        <span
                                            v-for="tag in order.tags"
                                            :key="tag.id"
                                            class="bg-gray-200 text-gray-700 text-xs px-3 py-1 rounded-full"
                                        >
                                            {{ tag.name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Інформація справа -->
                                <div class="text-right ml-4 flex-shrink-0">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ order.price ?? 'Договірна' }} ₴
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        📅 {{ order.deadline ? new Date(order.deadline).toLocaleDateString() : 'Невідомий' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        📅 {{ new Date(order.created_at).toLocaleDateString() }}
                                    </p>
                                    <!-- Статус -->
                                    <span
                                        class="inline-block mt-2 text-xs px-3 py-1 rounded-full"
                                        :class="getStatusBadge(order.status)"
                                    >
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Кнопки дій -->
                            <div class="mt-4 pt-4 border-t flex gap-2">
                                <!-- Используем ПУБЛИЧНЫЙ маршрут для просмотра -->
                                <a
                                    :href="route('orders.show', order.id)"
                                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm"
                                >
                                    Переглянути
                                </a>
                                <button
                                    v-if="order.status === 'in_progress'"
                                    @click="completeOrder(order.id)"
                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-sm"
                                >
                                    Завершити
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Пустий стан -->
                    <div v-else class="text-center py-12">
                        <p class="text-gray-500">Немає заявок</p>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ activeTab === 'active' ? 'Ви ще не взяли жодної заявки в роботу' : 'Немає завершених заявок' }}
                        </p>
                    </div>

                    <!-- Пагінація -->
                    <div v-if="filteredOrders.length > 0" class="mt-6 pt-4 border-t">
                        <p class="text-sm text-gray-500">
                            Показано {{ filteredOrders.length }} з {{ orders.total }} заявок
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
    activeTab: String,
});

const activeTab = ref(props.activeTab || 'active');

// Фільтрація заявок
const filteredOrders = computed(() => {
    const orders = props.orders.data || [];
    if (activeTab.value === 'active') {
        return orders.filter(order =>
            order.status === 'in_progress' || order.status === 'new'
        );
    } else {
        return orders.filter(order =>
            order.status === 'completed' || order.status === 'ready'
        );
    }
});

// Текст статусу
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

// Кольори статусів
const getStatusBadge = (status) => {
    const map = {
        new: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        ready: 'bg-purple-100 text-purple-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return map[status] || 'bg-gray-100 text-gray-800';
};

// Завершити заявку
const completeOrder = (orderId) => {
    if (confirm('Ви впевнені, що хочете завершити цю заявку?')) {
        router.put(route('worker.orders.complete', orderId), {
            onSuccess: () => {
                router.reload();
            },
            onError: (errors) => {
                alert('Не вдалося завершити заявку');
                console.error(errors);
            },
        });
    }
};
</script>
