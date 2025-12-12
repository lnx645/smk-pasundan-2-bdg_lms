<script setup lang="ts">
import { kerjakan } from '@/actions/App/Http/Controllers/TugasSiswaController';
import { Link } from '@inertiajs/vue3';
import { checkDeadline, getDeadlineStatus } from '@/lib/utils';
// Definisikan props
interface TugasItem {
    id: number;
    tugasID: number; // Pastikan ini ada sesuai penggunaan di Link
    title: string;
    matpel?: { nama: string };
    is_dikerjakan: boolean;
    is_deadline_over: boolean;
    deadline: string;
    created_at: string;
    user: {
        name: string;
        avatar_url?: string;
    };
    receiver_type: string;
    receiver_type_id: any[];
}

const props = defineProps<{
    // Props dummy untuk contoh, aslinya dari $page.props
}>();

const formatDeadlineLengkap = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    }).format(date);
};


</script>

<template>
    <div class="mt-5 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
            v-for="item in $page.props.tugas"
            :key="item.id"
            class="group relative flex flex-col justify-between rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:border-blue-300 hover:shadow-md"
        >
            <div class="mb-3 flex items-start justify-between">
                <span class="inline-flex items-center rounded-md bg-neutral-100 px-2.5 py-1 text-xs font-medium text-neutral-600">
                    <svg class="mr-1.5 h-3 w-3 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    {{ item.matpel?.nama || 'Umum' }}
                </span>

                <span
                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] md:text-xs font-semibold whitespace-nowrap"
                    :class="getDeadlineStatus(item.deadline, item.is_deadline_over, item.is_dikerjakan).class"
                >
                    {{ getDeadlineStatus(item.deadline, item.is_deadline_over, item.is_dikerjakan).text }}
                </span>
            </div>

            <div class="mb-6">
                <Link :href="kerjakan({ id: item.tugasID })" class="block">
                    <h3 class="line-clamp-2 text-lg font-bold text-neutral-800 transition-colors group-hover:text-blue-600">
                        {{ item.title }}
                    </h3>
                </Link>
                <p class="mt-2 line-clamp-2 text-sm text-neutral-500">
                    Silakan kerjakan tugas ini sebelum tenggat waktu berakhir.
                </p>
            </div>

            <div class="mt-auto border-t border-neutral-100 pt-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <img
                            :src="`https://ui-avatars.com/api/?name=${item.user.name}&background=random&color=fff`"
                            alt="Avatar"
                            class="h-8 w-8 rounded-full border border-white shadow-sm"
                        />
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-neutral-700 truncate max-w-[80px]">{{ item.user.name }}</span>
                            <span class="text-[10px] text-neutral-400">Pengajar</span>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-[10px] font-semibold tracking-wider text-neutral-400 uppercase">Batas Waktu</p>
                        <p class="text-xs font-medium tabular-nums" :class="item.is_deadline_over ? 'text-red-600' : 'text-neutral-700'">
                            {{ formatDeadlineLengkap(item.deadline) }}
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <Link
                        v-if="!checkDeadline(item.deadline) && !item.is_dikerjakan"
                        :href="kerjakan({ id: item.tugasID })"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-all hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 active:scale-95"
                    >
                        Kerjakan Tugas
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>

                    <Link
                        v-else-if="item.is_dikerjakan"
                        :href="kerjakan({ id: item.tugasID })"
                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition-all hover:bg-emerald-100"
                    >
                        Lihat Nilai
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </Link>

                    <button
                        v-else
                        disabled
                        class="flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-neutral-100 px-4 py-2.5 text-sm font-semibold text-neutral-400"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Akses Ditutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>