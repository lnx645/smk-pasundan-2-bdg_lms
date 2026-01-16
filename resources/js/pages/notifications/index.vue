<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
// Import Icon tambahan: ClipboardList (untuk Tugas)
import { BellOff, BookOpen, ClipboardList, Info } from 'lucide-vue-next';

const page = usePage();
const notifications = computed(() => page.props.notifications || []);

// FUNGSI: Mengubah tanggal menjadi "Waktu yang lalu"
const formatTimeAgo = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Baru saja';

    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes} mnt lalu`;

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours} jam lalu`;

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 7) return `${diffInDays} hari lalu`;

    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(date);
};

// FUNGSI: Tandai semua dibaca
const markAllAsRead = () => {
    router.post(
        route('notifications.markAllRead'),
        {},
        {
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <Head title="Notifikasi" />

    <div class="mx-auto mt-6 max-w-2xl overflow-hidden border border-gray-100 bg-white shadow-sm sm:rounded-lg">
        <div class="flex items-center justify-between border-b border-gray-100 p-4">
            <h1 class="text-lg font-bold text-gray-800">Notifikasi</h1>

            <button
                v-if="notifications.length > 0"
                @click="markAllAsRead"
                class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline"
            >
                Tandai semua dibaca
            </button>
        </div>

        <div v-if="notifications.length > 0">
            <div v-for="notification in notifications" :key="notification.id">
                <div
                    class="flex cursor-pointer items-start border-b border-gray-50 p-4 transition last:border-0 hover:bg-gray-50"
                    :class="{ 'bg-blue-50/40': !notification.read_at }"
                >
                    <div class="mr-3 flex-shrink-0">
                        <div
                            v-if="notification.data.type === 'materi'"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600"
                        >
                            <BookOpen class="h-5 w-5" />
                        </div>

                        <div
                            v-else-if="notification.data.type === 'tugas'"
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-600"
                        >
                            <ClipboardList class="h-5 w-5" />
                        </div>

                        <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                            <Info class="h-5 w-5" />
                        </div>
                    </div>

                    <div class="flex-1 text-sm leading-snug">
                        <div v-if="notification.data.type === 'materi'">
                            <span class="font-bold text-gray-900">{{ notification.data.guru }}</span>
                            <span class="text-gray-600"> membagikan materi baru </span>

                            <span :class="!notification.read_at ? 'font-bold text-black' : 'font-medium text-gray-800'">
                                "{{ notification.data.materi_title }}"
                            </span>

                            <span v-if="!notification.read_at" class="ml-1 inline-block h-2 w-2 rounded-full bg-red-500"></span>

                            <div class="mt-0.5 text-xs text-gray-400">{{ notification.data.matpel }}</div>
                        </div>

                        <div v-else-if="notification.data.type === 'tugas'">
                            <span class="font-bold text-gray-900">{{ notification.data.guru }}</span>
                            <span class="text-gray-600"> memberikan tugas baru </span>

                            <span :class="!notification.read_at ? 'font-bold text-black' : 'font-medium text-gray-800'">
                                "{{ notification.data.title }}"
                            </span>

                            <span v-if="!notification.read_at" class="ml-1 inline-block h-2 w-2 rounded-full bg-red-500"></span>

                            <div class="mt-0.5 text-xs text-gray-400">{{ notification.data.matpel }}</div>
                        </div>
                    </div>

                    <div class="ml-2 pt-0.5 text-xs whitespace-nowrap text-gray-400">
                        {{ formatTimeAgo(notification.created_at) }}
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="p-10 text-center">
            <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100">
                <BellOff class="h-6 w-6 text-gray-400" />
            </div>
            <p class="text-sm text-gray-500">Belum ada notifikasi.</p>
        </div>
    </div>
</template>
