<script setup lang="ts">
import { addMatpelToGuru } from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import { useForm, usePage } from '@inertiajs/vue3';
import { Plus, Save, X } from 'lucide-vue-next'; // Tambah icon ini
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
const props = defineProps<{
    selectedUser: any;
}>();
//@ts-ignore
import Avatar from 'vue3-avatar';
const page = usePage().props as any;
// State untuk form tambah
const isAdding = ref(false);
const emit = defineEmits(['closeModal']);
const newAssignment = useForm({
    matpel_kode: '',
    kelas_id: '',
    guru_nip: props.selectedUser.guru.nip,
});

const availableMatpels = computed(() => page.matpels || []);
const availableKelas = computed(() => page.kelas || []);

const saveAssignment = () => {
    newAssignment.submit(addMatpelToGuru(), {
        onSuccess() {
            isAdding.value = false;
            newAssignment.reset();
            toast.success('Berhasil di tambahkan', {
                position: 'top-center',
            });
            emit('closeModal');
        },
        onError(errors) {
            toast.error(errors.message || 'Terjadi kesalahan saat menyimpan.', {
                position: 'top-center',
            });
        },
    });
};
</script>
<template>
    <div class="px-6 py-6">
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-indigo-100 bg-indigo-50 p-3">
            <Avatar :name="selectedUser?.nama" class="h-10 w-10 rounded-full bg-white shadow-sm" />
            <div>
                <p class="text-sm font-bold text-gray-900">{{ selectedUser?.nama }}</p>
                <p class="font-mono text-xs text-indigo-600">{{ selectedUser?.guru?.nip }}</p>
            </div>
        </div>

        <div class="mb-4">
            <button
                v-if="!isAdding"
                @click="isAdding = true"
                class="flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 bg-gray-50 py-2.5 text-sm font-medium text-gray-500 transition-all hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600"
            >
                <Plus class="h-4 w-4" />
                Tambah Mata Pelajaran & Kelas
            </button>

            <div v-else class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm ring-1 ring-indigo-100">
                <div class="mb-3 flex items-center justify-between">
                    <h4 class="text-xs font-semibold tracking-wider text-gray-500 uppercase">Penugasan Baru</h4>
                    <button @click="isAdding = false" class="text-gray-400 hover:text-gray-600">
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-700">Mata Pelajaran</label>
                        <select
                            v-model="newAssignment.matpel_kode"
                            class="block w-full rounded-lg border border-gray-200 px-2 py-2 text-xs outline-none focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="" disabled>Pilih Matpel...</option>
                            <option v-for="mp in availableMatpels" :key="mp.kode" :value="mp.kode">
                                {{ mp.nama }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-medium text-gray-700">Kelas</label>
                        <select
                            v-model="newAssignment.kelas_id"
                            class="block w-full rounded-lg border border-gray-200 px-2 py-2 text-xs outline-none focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="" disabled>Pilih Kelas...</option>
                            <option v-for="kls in availableKelas" :key="kls.id" :value="kls.id">
                                {{ kls.nama }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button @click="isAdding = false" class="rounded-lg px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100">Batal</button>
                    <button
                        @click="saveAssignment"
                        class="flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-indigo-700"
                    >
                        <Save class="h-3.5 w-3.5" />
                        Simpan
                    </button>
                </div>
            </div>
        </div>

        <div class="custom-scrollbar max-h-[300px] space-y-2 overflow-y-auto pr-2">
            <div class="mb-2 flex items-center justify-between px-1">
                <span class="text-xs font-medium text-gray-500">Daftar Saat Ini</span>
                <span class="text-xs text-gray-400">{{ selectedUser?.guru?.pengajarans?.length || 0 }} Item</span>
            </div>

            <div
                v-for="(ajar, index) in selectedUser?.guru?.pengajarans"
                :key="index"
                class="group flex items-center justify-between rounded-lg border border-gray-100 p-3 transition-colors hover:bg-gray-50"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-xs font-bold text-orange-600">
                        {{ (index as number) + 1 }}
                    </div>
                    <div>
                        <p class="line-clamp-1 text-sm font-medium text-gray-900">
                            {{ ajar.matpel?.nama || ajar.matpel?.nama_pelajaran || 'Matpel Tidak Ada' }}
                        </p>
                        <p class="flex items-center gap-1 text-xs text-gray-500">
                            <span class="h-1.5 w-1.5 rounded-full bg-gray-300"></span>
                            Kode: {{ ajar.matpel_kode }}
                        </p>
                    </div>
                </div>

                <span
                    class="inline-flex items-center rounded-md bg-white px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm ring-1 ring-gray-200 transition-all ring-inset group-hover:text-indigo-700 group-hover:ring-indigo-200"
                >
                    {{ ajar.kelas?.nama || ajar.kelas?.nama_kelas || '?' }}
                </span>
            </div>

            <div v-if="!selectedUser?.guru?.pengajarans?.length" class="py-8 text-center text-xs text-gray-400 italic">
                Belum ada penugasan mata pelajaran.
            </div>
        </div>
    </div>
</template>
