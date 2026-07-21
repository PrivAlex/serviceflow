<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Кнопка назад -->
                    <div class="mb-4">
                        <a :href="route('orders.index')" class="text-blue-500 hover:underline">
                            ← Назад до списку
                        </a>
                    </div>

                    <!-- Заголовок -->
                    <h1 class="text-2xl font-bold mb-4">{{ order.title }}</h1>

                    <!-- Статус -->
                    <div class="mb-4">
                        <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                            {{ getStatusText(order.status) }}
                        </span>
                    </div>

                    <!-- Информация о заявке -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-gray-600">
                                <span class="font-semibold">Опис:</span>
                                {{ order.description || 'Опис відсутній' }}
                            </p>
                            <p class="text-gray-600 mt-2">
                                <span class="font-semibold">Ціна:</span>
                                {{ order.price ?? 'Договірна' }} ₴
                            </p>
                            <p class="text-gray-600 mt-2">
                                <span class="font-semibold">Дедлайн:</span>
                                {{ order.deadline ? new Date(order.deadline).toLocaleDateString() : 'Не вказано' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">
                                <span class="font-semibold">Клієнт:</span>
                                {{ order.client?.name || 'Невідомий' }}
                            </p>
                            <p class="text-gray-600 mt-2">
                                <span class="font-semibold">Email клієнта:</span>
                                {{ order.client?.email || 'Не вказано' }}
                            </p>
                            <p class="text-gray-600 mt-2">
                                <span class="font-semibold">Створено:</span>
                                {{ new Date(order.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>

                    <!-- Теги -->
                    <div v-if="order.tags && order.tags.length" class="mb-6">
                        <h3 class="font-semibold text-gray-700 mb-2">Теги:</h3>
                        <div class="flex gap-2 flex-wrap">
                            <span
                                v-for="tag in order.tags" :key="tag.id" class="bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded">{{ tag.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Кнопка редактирования (пока для всех, потом уберём) -->
                    <div class="flex gap-2 mt-6">
                        <a :href="route('client.orders.edit', order.id)" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Редагувати
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    order: Object,
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
