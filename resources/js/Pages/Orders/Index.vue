<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Доступні заявки</h1>

                    <!-- Список заявок -->
                    <div v-if="orders.data.length">
                        <div v-for="order in orders.data" :key="order.id" class="border-b border-gray-200 py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-semibold">
                                        <a :href="route('orders.show', order.id)" class="text-blue-600 hover:underline">
                                            {{ order.title }}
                                        </a>
                                    </h2>
                                    <p class="text-gray-600 mt-1">{{ order.description || 'Опис відсутній' }}</p>
                                    <div class="flex gap-2 mt-2">
                                        <span v-for="tag in order.tags" :key="tag.id" class="bg-gray-200 text-gray-700 text-sm px-2 py-1 rounded">
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
                                    <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                                        {{ getStatusText(order.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8 text-gray-500">
                        Заявок поки немає.
                    </div>
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
defineProps({
    orders: Object,
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
</script>
