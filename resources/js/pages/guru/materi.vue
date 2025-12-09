<script setup lang="ts">
import { deleteMateri, publishMateri, tambahMateri } from '@/actions/App/Http/Controllers/GuruMateriController';
import Button from '@/components/button.vue';
import Modal from '@/components/modal.vue'; // Asumsi komponen ini ada
import CarbonEventSchedule from '@/icons/CarbonEventSchedule.vue';
import IcBaselineDelete from '@/icons/IcBaselineDelete.vue';
import MaterialSymbolsAddCircleOutline from '@/icons/MaterialSymbolsAddCircleOutline.vue';
import MaterialSymbolsCheckCircleUnreadOutline from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import MaterialSymbolsSearch from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue'; // Icon baru untuk empty state
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useVfm } from 'vue-final-modal';
import { toast } from 'vue-sonner';

const page = usePage();
const matpelSelected = ref<string | null>((page.props.kode_matpel as string) ?? '');

// Watcher untuk Filter Matpel
watch(matpelSelected, (newVal) => {
    if (newVal) {
        router.visit('?kode_matpel=' + newVal, {
            preserveScroll: true,
            preserveState: true,
        });
    }
});

const vfm = useVfm();
const modalID = 'modalViewData';

function close() {
    vfm.close(modalID);
}

// Logic Hapus
async function deleteMateriItem(id: string) {
    if (confirm('Apakah Anda yakin ingin menghapus materi ini? Data yang dihapus tidak dapat dikembalikan.')) {
        router.delete(deleteMateri({ materi_id: id }).url, {
            onSuccess: () => toast.success('Materi berhasil dihapus'),
            onError: () => toast.error('Gagal menghapus materi')
        });
    }
}

// Logic Publish
async function publishMateriItem(id: string) {
    if (confirm('Terbitkan materi ini sekarang? Siswa akan segera melihat materi ini.')) {
        router.patch(publishMateri().url, { id: id }, {
            onSuccess: () => toast.success('Materi berhasil diterbitkan'),
            onError: () => toast.error('Gagal menerbitkan materi')
        });
    }
}

// Watcher Global Error/Success (Optional jika sudah di handle layout)
watch(() => page.props.errors, (errors) => {
    if (errors.success) {
        toast.success(errors.success as string);
    } else if (errors.gagal) {
        toast.error(errors.gagal as string);
    }
}, { deep: true });
</script>

<template>
    <Modal @close="close" :modalID="modalID" title="Tambah Data">
        <p class="text-neutral-600">Konten modal...</p>
    </Modal>

    <div class="mt-6 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        
        <div class="relative w-full sm:w-64">
            <select 
                v-model="matpelSelected" 
                class="w-full appearance-none rounded-lg border border-neutral-300 bg-white px-4 py-2.5 text-sm text-neutral-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-sm transition-all cursor-pointer"
            >
                <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                <option
                    v-for="value in $page.props.matpels"
                    :key="value.kode_matpel"
                    :value="value.kode_matpel"
                >
                    {{ value.nama }}
                </option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-neutral-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>

        <Link
            v-if="$page.props.kelas_kode"
            :href="tambahMateri({ kelas_kode: $page.props.kelas_kode as string }).url"
            class="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 hover:shadow transition-all"
        >
            <MaterialSymbolsAddCircleOutline class="text-lg" />
            <span>Buat Materi Baru</span>
        </Link>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm overflow-hidden">
        
        <div v-if="($page.props.matpel_kode || $page.props?.materials) && $page.props?.materials.length > 0" class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="bg-neutral-50 text-xs uppercase font-semibold text-neutral-500 border-b border-neutral-200">
                    <tr>
                        <th class="px-6 py-4 w-[60px] text-center">No</th>
                        <th class="px-6 py-4">Judul Materi</th>
                        <th class="px-6 py-4">Jadwal / Status</th>
                        <th class="px-6 py-4 text-center">File</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    <tr 
                        v-for="(materi, index) in $page.props.materials" 
                        :key="materi.id"
                        class="hover:bg-neutral-50/80 transition-colors"
                    >
                        <td class="px-6 py-4 text-center font-medium text-neutral-400">
                            {{ materi.nomor_materi || index + 1 }}
                        </td>

                        <td class="px-6 py-4">
                            <p class="font-semibold text-neutral-800 line-clamp-1 mb-1">{{ materi.title }}</p>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div :class="[
                                    'flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border',
                                    materi.is_published 
                                        ? 'bg-green-50 text-green-700 border-green-200' 
                                        : 'bg-amber-50 text-amber-700 border-amber-200'
                                ]">
                                    <component :is="materi.is_published ? MaterialSymbolsCheckCircleUnreadOutline : CarbonEventSchedule" class="text-sm" />
                                    <span>{{ materi.is_published ? 'Published' : 'Terjadwal' }}</span>
                                </div>
                                <span class="text-xs text-neutral-500">{{ materi.publish_date }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-md bg-neutral-100 text-neutral-700 font-medium text-xs border border-neutral-200">
                                {{ materi.jumlahFileMateri }} File
                            </span>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Button 
                                    v-if="!materi.is_published" 
                                    @click="publishMateriItem(materi.id)" 
                                    variant="secondary"
                                    class="!px-3 !py-1.5 !text-xs !font-medium bg-white border border-neutral-300 hover:bg-neutral-50"
                                    title="Terbitkan Sekarang"
                                >
                                    Publish
                                </Button>
                                
                                <button 
                                    @click="deleteMateriItem(materi.id)" 
                                    class="p-2 rounded-lg text-neutral-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    title="Hapus Materi"
                                >
                                    <IcBaselineDelete class="text-lg" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-16 px-4 text-center">
            <div class="w-16 h-16 mb-4 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400">
                <MaterialSymbolsSearch v-if="$page.props.kode_matpel" class="text-3xl" />
                <MaterialSymbolsAddCircleOutline v-else class="text-3xl" />
            </div>
            
            <h3 class="text-lg font-semibold text-neutral-800 mb-1">
                {{ $page.props.kode_matpel ? 'Belum Ada Materi' : 'Pilih Mata Pelajaran' }}
            </h3>
            
            <p class="text-sm text-neutral-500 max-w-sm">
                {{ $page.props.kode_matpel 
                    ? 'Belum ada materi yang ditambahkan untuk mata pelajaran ini. Silakan tambah materi baru.' 
                    : 'Silakan pilih mata pelajaran melalui filter di atas untuk melihat daftar materi.' 
                }}
            </p>
        </div>

    </div>
</template>