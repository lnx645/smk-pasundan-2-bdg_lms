<script lang="ts" setup>
// ... imports yang sudah ada
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { usePage } from '@inertiajs/vue3';
import { MoreVertical, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Paging from '../paging.vue';
import { getInitials } from '@/lib/utils';

const page = usePage().props as any;

const userList = computed(() => {
    return page.users?.data || [];
});

const links = computed(() => {
    return page.users?.links || [];
});


const breadcrumbs = [{ label: 'Dashboard' }, { label: 'User Management' }];

const search = ref('');
</script>

<template>
    <div class="w-full  pb-10">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Breadcrumb :items="breadcrumbs" />
            <div class="flex px-4 flex-col gap-3 sm:flex-row">
                <div class="relative">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari siswa..."
                        class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-4 pl-10 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 sm:w-64"
                    />
                </div>

                <Link
                    href="/users/create"
                    class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
                >
                    <Plus class="h-4 w-4" />
                    <span>Tambah Siswa</span>
                </Link>
            </div>
        </div>

        <div class="hidden mx-4 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm md:block">
            <table class="w-full  text-left text-sm text-gray-500">
                <thead class="bg-gray-50 text-xs text-gray-700 uppercase">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Siswa</th>
                        <th class="px-6 py-4 font-semibold">NIS / Email</th>
                        <th class="px-6 py-4 font-semibold">Kelas</th>
                        <th class="px-6 py-4 text-center font-semibold">Status</th>
                        <th class="px-6 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-gray-100">
                    <tr v-for="user in userList" :key="user.id" class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                                    {{ getInitials(user.name) }}
                                </div>
                                <div class="font-medium text-gray-900">{{ user.name }}</div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-gray-900">{{ user.siswa.nis || '-' }}</span>
                                <span class="text-xs text-gray-400">{{ user.email }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                {{ user.kelas.nama || 'X RPL 1' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="user.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ user.siswa.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-indigo-600">
                                    <Pencil class="h-4 w-4" />
                                </button>
                                <button class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="userList.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Tidak ada data siswa ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-4 md:hidden">
            <div v-for="user in userList" :key="user.id + '-mobile'" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                            {{ getInitials(user.name) }}
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ user.name }}</h3>
                            <p class="text-xs text-gray-500">{{ user.email }}</p>
                        </div>
                    </div>
                    <button class="text-gray-400">
                        <MoreVertical class="h-5 w-5" />
                    </button>
                </div>

                <div class="mb-4 grid grid-cols-2 gap-4 border-y border-gray-50 py-3">
                    <div>
                        <p class="text-xs text-gray-400">NIS</p>
                        <p class="text-sm font-medium text-gray-700">{{ user.siswa.nis || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Kelas</p>
                        <p class="text-sm font-medium text-gray-700">{{ user.kelas.nama || 'X RPL 1' }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="user.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    >
                        {{ user.siswa.status }}
                    </span>

                    <div class="flex gap-2">
                        <button
                            class="flex items-center gap-1 rounded-md border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50"
                        >
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <Paging class="px-4" :links="links" />
    </div>
</template>
