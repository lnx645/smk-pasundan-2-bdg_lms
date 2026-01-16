<script lang="ts" setup>
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { BookOpen, Eye, FileSpreadsheet, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
//@ts-ignore
import Paging from '@/components/paging.vue'; // Pastikan path paging benar
import Modal from '@/components/ui/modal.vue';
import { debounce } from 'lodash';
//@ts-ignore
import Avatar from 'vue3-avatar';
import PenugasanGuru from './penugasan-guru.vue';
// 2. Import Modal Import
import ImportGuruModal from './import-modal.vue';

// Define Props agar data reaktif & search tidak hilang
const props = defineProps<{
    users: any;
    filters: { search?: string };
}>();

const page = computed(() => usePage().props as any);
const userList = computed(() => props.users?.data || []); // Gunakan props
const links = computed(() => props.users?.links || []); // Gunakan props

const breadcrumbs = [{ label: 'Dashboard' }, { label: 'Manajemen Guru' }];
const search = ref(props.filters?.search || '');

// State Modal
const showModal = ref(false); // Modal Penugasan
const showImportModal = ref(false); // 3. State Modal Import
const selectedUser = ref<any>(null);

const openAssignmentModal = (user: any) => {
    selectedUser.value = user;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    setTimeout(() => {
        selectedUser.value = null;
    }, 200);
};

// Search Logic
watch(
    search,
    debounce((value: string) => {
        router.get(
            // Pastikan URL ini benar. Jika pakai ziggy: route('user-management.guru')
            UserManagementController.guru().url,
            { search: value },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 300),
);
</script>

<template>
    <div class="relative min-h-screen w-full bg-gray-50/50 pb-12 font-sans">
        <div class="mb-8 flex flex-col justify-between gap-4 px-1 sm:flex-row sm:items-end">
            <Breadcrumb :items="breadcrumbs" />

            <div class="flex flex-col gap-2 sm:flex-row">
                <button
                    @click="showImportModal = true"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-50"
                >
                    <FileSpreadsheet class="h-4 w-4 text-green-600" />
                    <span>Import Excel</span>
                </button>

                <Link
                    :href="UserManagementController.tambahGuru()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-indigo-700"
                >
                    <Plus class="h-4 w-4" />
                    <span>Tambah Guru</span>
                </Link>
            </div>
        </div>

        <div class="mx-3 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-4">
                <div class="relative max-w-md">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <input
                        v-model="search"
                        type="text"
                        class="block w-full rounded-lg border-0 bg-gray-50 py-2.5 pl-10 text-sm ring-1 ring-gray-200 ring-inset focus:ring-2 focus:ring-indigo-600"
                        placeholder="Cari Nama / NIP / Email..."
                    />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50/50 text-gray-500">
                        <tr>
                            <th class="px-6 py-4 font-medium">Informasi Guru</th>
                            <th class="px-6 py-4 font-medium">NIP</th>
                            <th class="px-6 py-4 font-medium">Penugasan</th>
                            <th class="px-6 py-4 font-medium">Keahlian Utama</th>
                            <th class="px-6 py-4 text-center font-medium">Status</th>
                            <th class="px-6 py-4 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <tr v-for="user in userList" :key="user.id" class="group transition-colors hover:bg-gray-50/60">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative h-10 w-10 flex-shrink-0 overflow-hidden rounded-full shadow-sm ring-2 ring-white">
                                        <Avatar :imageSrc="user.guru?.foto" :name="user.nama || user.name" class="h-full w-full object-cover" />
                                    </div>
                                    <div class="font-monospace">
                                        <div class="font-semibold text-gray-900">{{ user.nama || user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-mono text-gray-600">{{ user.guru?.nip || '-' }}</td>

                            <td class="px-6 py-4">
                                <div>
                                    <button
                                        @click="openAssignmentModal(user)"
                                        class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white py-1.5 pr-3 pl-2 text-xs font-medium text-gray-700 shadow-sm transition-all hover:border-indigo-300 hover:text-indigo-600"
                                    >
                                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                            <BookOpen class="h-3 w-3" />
                                        </div>
                                        <span>{{ user.guru?.pengajarans?.length || 0 }} Kelas Diampu</span>
                                        <Eye class="h-3 w-3 text-gray-400" />
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                <span v-if="user.guru?.spesialis_matpel">
                                    {{ user.guru.spesialis_matpel.nama }}
                                </span>
                                <span v-else class="text-gray-400 italic">-</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset"
                                    :class="
                                        user.guru?.status == 'aktif'
                                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                            : 'bg-red-50 text-red-700 ring-red-600/10'
                                    "
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full"
                                        :class="user.guru?.status === 'aktif' ? 'bg-emerald-500' : 'bg-red-500'"
                                    ></span>
                                    {{ user.guru?.status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1 opacity-60 transition-opacity group-hover:opacity-100">
                                    <button
                                        @click="router.visit(UserManagementController.updateGuru({ id: user.guru.nip }).url)"
                                        class="rounded-lg p-2 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </button>
                                    <button class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="userList.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada data guru ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                <Paging :links="links" />
            </div>
        </div>

        <Modal :isOpen="showModal" @close="closeModal">
            <PenugasanGuru @closeModal="closeModal" :selectedUser="selectedUser" />
        </Modal>

        <Modal :isOpen="showImportModal" @close="showImportModal = false">
            <ImportGuruModal @close="showImportModal = false" />
        </Modal>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #e5e7eb;
    border-radius: 20px;
}
</style>
