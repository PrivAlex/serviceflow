<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Кнопка повернення -->
                    <div class="mb-4">
                        <a :href="route('orders.index')" class="text-blue-500 hover:underline">
                            ← Назад до списку
                        </a>
                    </div>

                    <!-- Заголовок -->
                    <h1 class="text-2xl font-bold mb-4">{{ order.title }}</h1>

                    <!-- Статус -->
                    <div class="mb-4">
                        <span :class="getStatusClass(order.status)" class="text-sm px-3 py-1 rounded-full">
                            {{ getStatusText(order.status) }}
                        </span>
                    </div>

                    <!-- Інформація про заявку -->
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
                                <span class="font-semibold">Виконавець:</span>
                                {{ order.worker?.name || 'Не призначений' }}
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
                                v-for="tag in order.tags"
                                :key="tag.id"
                                class="bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded"
                            >
                                {{ tag.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Кнопки дій -->
                    <div class="flex gap-2 mt-6 flex-wrap">
                        <!-- Взяти в роботу -->
                        <button
                            v-if="order.status === 'new' && order.worker_id === null"
                            @click="takeOrder"
                            :disabled="takingOrder"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ takingOrder ? 'Завантаження...' : '📋 Взяти в роботу' }}
                        </button>

                        <!-- Завершити -->
                        <button
                            v-if="order.status === 'in_progress'"
                            @click="completeOrder"
                            :disabled="completingOrder"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ completingOrder ? 'Завантаження...' : '✅ Завершити' }}
                        </button>

                        <!-- Скасувати -->
                        <button
                            v-if="order.status === 'in_progress'"
                            @click="cancelOrder"
                            :disabled="cancellingOrder"
                            class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ cancellingOrder ? 'Завантаження...' : '❌ Скасувати' }}
                        </button>

                        <!-- Редагувати -->
                        <a
                            :href="route('client.orders.edit', order.id)"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        >
                            Редагувати
                        </a>

                        <!-- Видалити -->
                        <button
                            @click="confirmDelete"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                        >
                            Видалити
                        </button>
                    </div>

                    <!-- Повідомлення про помилку -->
                    <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ errorMessage }}
                    </div>

                    <!-- Повідомлення про успіх -->
                    <div v-if="successMessage" class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ successMessage }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модалка підтвердження видалення -->
    <div
        v-if="showDeleteModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
        @click="closeModalOnOutside"
    >
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <!-- Іконка -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <!-- Заголовок -->
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">
                    Підтвердження видалення
                </h3>

                <!-- Текст -->
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Ви впевнені, що хочете видалити заявку
                        <span class="font-semibold text-gray-700">"{{ order.title }}"</span>?
                    </p>
                    <p class="text-xs text-red-500 mt-2">
                        ⚠️ Цю дію не можна скасувати!
                    </p>
                </div>

                <!-- Кнопки -->
                <div class="items-center px-4 py-3">
                    <button
                        @click="closeDeleteModal"
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2"
                    >
                        Скасувати
                    </button>
                    <button
                        @click="deleteOrder"
                        :disabled="deleting"
                        class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ deleting ? 'Видалення...' : 'Так, видалити' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
});

const showDeleteModal = ref(false);
const deleting = ref(false);
const takingOrder = ref(false);
const completingOrder = ref(false);
const cancellingOrder = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

/**
 * Отримати текст статусу
 */
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

/**
 * Отримати CSS-клас для статусу
 */
const getStatusClass = (status) => {
    const map = {
        new: 'bg-yellow-100 text-yellow-800',
        in_progress: 'bg-blue-100 text-blue-800',
        ready: 'bg-purple-100 text-purple-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    };
    return map[status] || 'bg-gray-100 text-gray-800';
};

/**
 * Взяти заявку в роботу
 */
const takeOrder = () => {
    if (!confirm('Ви впевнені, що хочете взяти цю заявку в роботу?')) {
        return;
    }

    takingOrder.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    router.put(`/worker/orders/${props.order.id}/take`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            takingOrder.value = false;
            successMessage.value = 'Заявку успішно взято в роботу!';
            // Оновлюємо сторінку, щоб побачити зміни
            router.reload();
        },
        onError: (errors) => {
            takingOrder.value = false;
            console.error('Помилка при взятті заявки:', errors);
            errorMessage.value = errors?.message || 'Не вдалося взяти заявку. Спробуйте пізніше.';
        },
    });
};

/**
 * Завершити заявку
 */
const completeOrder = () => {
    if (!confirm('Ви впевнені, що хочете завершити цю заявку?')) {
        return;
    }

    completingOrder.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    router.put(`/worker/orders/${props.order.id}/complete`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            completingOrder.value = false;
            successMessage.value = 'Заявку успішно завершено!';
            router.reload();
        },
        onError: (errors) => {
            completingOrder.value = false;
            console.error('Помилка при завершенні заявки:', errors);
            errorMessage.value = errors?.message || 'Не вдалося завершити заявку. Спробуйте пізніше.';
        },
    });
};

/**
 * Скасувати заявку
 */
const cancelOrder = () => {
    if (!confirm('Ви впевнені, що хочете скасувати виконання цієї заявки?')) {
        return;
    }

    cancellingOrder.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    // TODO: додати маршрут для скасування
    router.put(`/worker/orders/${props.order.id}/cancel`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            cancellingOrder.value = false;
            successMessage.value = 'Виконання заявки скасовано!';
            router.reload();
        },
        onError: (errors) => {
            cancellingOrder.value = false;
            console.error('Помилка при скасуванні заявки:', errors);
            errorMessage.value = errors?.message || 'Не вдалося скасувати заявку. Спробуйте пізніше.';
        },
    });
};

/**
 * Відкрити модалку підтвердження видалення
 */
const confirmDelete = () => {
    showDeleteModal.value = true;
};

/**
 * Закрити модалку
 */
const closeDeleteModal = () => {
    showDeleteModal.value = false;
};

/**
 * Закрити при кліку поза модалкою
 */
const closeModalOnOutside = (event) => {
    if (event.target === event.currentTarget) {
        closeDeleteModal();
    }
};

/**
 * Видалити заявку
 */
const deleteOrder = () => {
    deleting.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    router.delete(route('client.orders.destroy', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            deleting.value = false;
            showDeleteModal.value = false;
            successMessage.value = 'Заявку успішно видалено!';
            // Переходимо на список заявок
            router.visit(route('orders.index'));
        },
        onError: (errors) => {
            deleting.value = false;
            console.error('Помилка при видаленні:', errors);
            errorMessage.value = errors?.message || 'Не вдалося видалити заявку. Спробуйте пізніше.';
        },
    });
};
</script>
