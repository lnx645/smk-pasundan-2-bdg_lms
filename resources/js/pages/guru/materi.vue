<script setup lang="ts">
import { deleteMateri, publishMateri, tambahMateri } from '@/actions/App/Http/Controllers/GuruMateriController';
import Button from '@/components/button.vue';
import Modal from '@/components/modal.vue';
import CarbonEventSchedule from '@/icons/CarbonEventSchedule.vue';
import IcBaselineDelete from '@/icons/IcBaselineDelete.vue';
import MaterialSymbolsAddCircleOutline from '@/icons/MaterialSymbolsAddCircleOutline.vue';
import MaterialSymbolsCheckCircleUnreadOutline from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import MaterialSymbolsSearch from '@/icons/MaterialSymbolsSearchRounded.vue';
import MaterialSymbolsDescription from '@/icons/MaterialSymbolsDescription.vue'; // Icon baru untuk file
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useVfm } from 'vue-final-modal';
import { toast } from 'vue-sonner';

const page = usePage();
const matpelSelected = ref<string | null>((page.props.kode_matpel as string) ?? '');

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
function close() { vfm.close(modalID); }

async function deleteMateriItem(id: string) {
    if (confirm('Hapus materi ini?')) {
        router.delete(deleteMateri({ materi_id: id }).url, {
            onSuccess: () => toast.success('Terhapus'),
            onError: () => toast.error('Gagal')
        });
    }
}

async function publishMateriItem(id: string) {
    if (confirm('Terbitkan materi?')) {
        router.patch(publishMateri().url, { id: id }, {
            onSuccess: () => toast.success('Terbit'),
            onError: () => toast.error('Gagal')
        });
    }
}

watch(() => page.props.errors, (errors) => {
    if (errors.success) toast.success(errors.success as string);
    else if (errors.gagal) toast.error(errors.gagal as string);
}, { deep: true });
</script>

<template>
    <Modal @close="close" :modalID="modalID" title="Tambah Data">
        <p>Konten...</p>
    </Modal>
    
    <div class="mt-6 flex flex-col rounded-xl border border-neutral-200 bg-white shadow-sm overflow-hidden max-w-full mx-auto">
        
        <div class="flex items-center justify-between border-b border-neutral-100 bg-neutral-50/50 px-4 py-3">
            
            <div class="relative w-48">
                <select 
                    v-model="matpelSelected" 
                    class="w-full cursor-pointer appearance-none rounded-md border border-neutral-200 bg-white py-1.5 pl-3 pr-8 text-xs font-medium text-neutral-600 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
                    <option value="" disabled>Filter Matpel</option>
                    <option v-for="v in $page.props.matpels" :key="v.kode_matpel" :value="v.kode_matpel">{{ v.nama }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-400">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <Link
                v-if="$page.props.kelas_kode"
                :href="tambahMateri({ kelas_kode: $page.props.kelas_kode as string }).url"
                class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-blue-700"
            >
                <MaterialSymbolsAddCircleOutline class="text-sm" />
                <span>Baru</span>
            </Link>
        </div>

        <div v-if="($page.props.matpel_kode || $page.props?.materials) && $page.props?.materials.length > 0">
            <ul class="divide-y divide-neutral-100">
                <li 
                    v-for="(materi, index) in $page.props.materials" 
                    :key="materi.id"
                    class="group flex items-center justify-between gap-3 px-4 py-3 hover:bg-blue-50/40 transition-colors"
                >
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 font-bold text-xs">
                            {{ materi.nomor_materi || index + 1 }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 mb-0.5">
                                <h4 class="truncate text-sm font-semibold text-neutral-800">{{ materi.title }}</h4>
                                <span v-if="materi.jumlahFileMateri > 0" class="inline-flex items-center gap-0.5 rounded px-1.5 py-0.5 text-[10px] bg-neutral-100 text-neutral-500 font-medium border border-neutral-200">
                                    <MaterialSymbolsDescription class="text-[10px]" /> {{ materi.jumlahFileMateri }}
                                </span>
                            </div>
                            <div class="flex items-center text-xs text-neutral-400 gap-3">
                                <span>{{ materi.publish_date }}</span>
                                <span class="h-1 w-1 rounded-full bg-neutral-300"></span>
                                <span :class="materi.is_published ? 'text-green-600 font-medium' : 'text-amber-600 font-medium'">
                                    {{ materi.is_published ? 'Published' : 'Draft / Terjadwal' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                        <Button 
                            v-if="!materi.is_published" 
                            @click="publishMateriItem(materi.id)" 
                            variant="secondary"
                            class="!h-7 !px-2 !text-[10px] bg-white border border-neutral-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200"
                        >
                            Publish
                        </Button>
                        
                        <button 
                            @click="deleteMateriItem(materi.id)" 
                            class="flex h-7 w-7 items-center justify-center rounded text-neutral-400 hover:bg-red-50 hover:text-red-500 transition-colors"
                            title="Hapus"
                        >
                            <IcBaselineDelete class="text-base" />
                        </button>
                    </div>
                </li>
            </ul>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-10 text-center">
            <div class="mb-2 rounded-full bg-neutral-100 p-2 text-neutral-400">
                <MaterialSymbolsSearch v-if="$page.props.kode_matpel" class="text-xl" />
                <MaterialSymbolsAddCircleOutline v-else class="text-xl" />
            </div>
            <p class="text-xs text-neutral-500">
                {{ $page.props.kode_matpel ? 'Belum ada materi.' : 'Pilih Mapel di atas.' }}
            </p>
        </div>
    </div>
</template>