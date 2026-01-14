<script lang="ts" setup>
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { FileSpreadsheet, Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
//@ts-ignore
import Modal from '@/components/ui/modal.vue';
import { debounce } from 'lodash';
//@ts-ignore
import Avatar from 'vue3-avatar';
import Paging from '../paging.vue';
import ImportSiswaModal from './import-modal-siswa.vue';

const props = defineProps<{
    users: any;
    filters: { search?: string };
}>();

// 3. Update computed untuk mengambil dari 'props', bukan 'page'
const userList = computed(() => {
    return props.users?.data || [];
});

const links = computed(() => {
    return props.users?.links || [];
});

const breadcrumbs = [{ label: 'Dashboard' }, { label: 'User Management' }];

const search = ref(props.filters?.search || '');
const showImportModal = ref(false);
const statusMap: Record<string, string> = {
    aktif: 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-600/20',
    lulus: 'bg-blue-100 text-blue-700 ring-1 ring-blue-600/20',
    keluar: 'bg-rose-100 text-rose-700 ring-1 ring-rose-600/20',
    tinggal_kelas: 'bg-amber-100 text-amber-700 ring-1 ring-amber-600/20',
};

const formatStatus = (status: string) => {
    return status.replace('_', ' ').replace(/\b\w/g, (l) => l.toUpperCase());
};
watch(
    search,
    debounce((value: string) => {
        router.get(
            UserManagementController.siswa().url,
            { search: value },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 300),
);

// Logic Delete
const handleDelete = (user: any) => {
    if (!confirm(`Apakah Anda yakin ingin menghapus siswa ${user.name}?`)) {
        return;
    }

    // Gunakan router.delete biasa. Inertia otomatis refresh props setelah sukses.
    router.delete(UserManagementController.destroySiswa({ id: user.siswa.nis }).url, {
        preserveScroll: true,
        // preserveState: false, // Opsional: set false jika ingin memaksa state refresh total, tapi biasanya true sudah cukup jika pakai defineProps
        onSuccess: () => {
            toast.success('Data siswa berhasil dihapus');
        },
        onError: (errors: any) => {
            // Ambil flash error dari usePage() untuk notifikasi (ini aman pakai usePage)
            const page = usePage();
            if ((page.props.flash as any)?.error) {
                toast.error((page.props.flash as any).error);
            } else {
                toast.error('Gagal menghapus data. Siswa mungkin memiliki data terkait.');
            }
        },
    });
};
</script>

<template>
    <div class="w-full pb-10">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <Breadcrumb :items="breadcrumbs" />
            <div class="flex flex-col gap-3 px-4 sm:flex-row">
                <div class="relative">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari siswa (Nama/NIS)..."
                        class="h-10 w-full rounded-lg border border-gray-200 bg-white pr-4 pl-10 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 sm:w-64"
                    />
                </div>

                <button
                    @click="showImportModal = true"
                    class="flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                >
                    <FileSpreadsheet class="h-4 w-4 text-green-600" />
                    <span>Import Excel</span>
                </button>

                <Link
                    :href="UserManagementController.tambahSiswa()"
                    class="flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
                >
                    <Plus class="h-4 w-4" />
                    <span>Tambah Siswa</span>
                </Link>
            </div>
        </div>

        <div class="mx-4 rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm text-gray-500">
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
                                <div
                                    class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-indigo-100 text-xs font-bold text-indigo-700"
                                >
                                    <Avatar v-if="!user.siswa.pas_photo" :name="user.name" alt="" />
                                    <img :alt="user.name" v-else :src="`/storage/${user.siswa.pas_photo}`" />
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
                                {{ user.kelas?.nama || '-' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold shadow-sm transition-all"
                                :class="statusMap[user.siswa.status.toLowerCase()] || 'bg-gray-100 text-gray-600'"
                            >
                                {{ formatStatus(user.siswa.status) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button
                                    @click="
                                        router.visit(
                                            UserManagementController.editSiswa({
                                                id: user.siswa.nis,
                                            }).url,
                                        )
                                    "
                                    class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-indigo-600"
                                >
                                    <Pencil class="h-4 w-4" />
                                </button>

                                <button
                                    @click="handleDelete(user)"
                                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                    title="Hapus Siswa"
                                >
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

        <Paging class="px-4" :links="links" />

        <Modal :isOpen="showImportModal" @close="showImportModal = false">
            <ImportSiswaModal @close="showImportModal = false" />
        </Modal>
    </div>
</template>
