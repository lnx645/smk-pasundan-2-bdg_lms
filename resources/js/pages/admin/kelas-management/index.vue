<script setup lang="ts">
import ManagementKelasController from '@/actions/App/Http/Controllers/Admin/ManagementKelasController';
import Input from '@/components/input.vue';
import Breadcrumb from '@/features/dashboard-admin/breadcrumb.vue';
import { router, useForm } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { BookOpen, CheckCircle, Edit, GraduationCap, Plus, Search, Trash2, UserMinus, UserPlus, Users, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    kelas: {
        id: number;
        nama: string;
        tingkat: string;
        siswa_count: number;
        matpel_count: number;
        siswa: {
            id: number;
            nis: string;
            user: {
                name: string;
                email: string;
            };
        }[];
    }[];
    available_students: {
        id: number;
        nama: string;
        nis: string;
        gender: string;
        user: { name: string };
    }[];
    filters: {
        search: string;
        tingkat: string;
    };
}>();

const breadcrumbs = [{ label: 'Dashboard', href: '/dashboard' }, { label: 'Manajemen Kelas' }];

// --- STATE ---
const search = ref(props.filters.search || '');
const selectedTingkat = ref(props.filters.tingkat || 'Semua');
const tingkatOptions = ['Semua', '10', '11', '12'];

// Modal States
const showModal = ref(false);
const showStudentModal = ref(false);
const showListModal = ref(false);

const isEditing = ref(false);
const editingId = ref<number | null>(null);

const selectedClassForStudent = ref<any>(null);
const selectedClassList = ref<any>(null);

const studentSearch = ref('');

const form = useForm({ nama: '', tingkat: '10' });
const studentForm = useForm({ kelas_id: null as number | null, siswa_id: null as number | null }); // pakai NIS untuk select

const reloadData = () => {
    router.get(
        ManagementKelasController.index().url,
        { search: search.value, tingkat: selectedTingkat.value },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

watch(
    search,
    debounce(() => reloadData(), 300),
);
const changeTingkat = (tingkat: string) => {
    selectedTingkat.value = tingkat;
    reloadData();
};

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};
const openEditModal = (item: any) => {
    isEditing.value = true;
    editingId.value = item.id;
    form.nama = item.nama;
    form.tingkat = item.tingkat;
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    //@ts-ignore
    const action =
        isEditing.value && editingId.value
            ? ManagementKelasController.update['/admin/kelas-management/edit/{id}']({ id: editingId.value as number })
            : ManagementKelasController.store();

    // @ts-ignore
    form[isEditing.value ? 'put' : 'post'](action.url, {
        onSuccess: () => {
            showModal.value = false;
            toast.success(isEditing.value ? 'Kelas diperbarui' : 'Kelas dibuat');
        },
        onError: () => toast.error('Cek inputan Anda'),
    });
};

const deleteItem = (item: any) => {
    if (item.siswa_count > 0 || item.matpel_count > 0) return toast.error('Gagal', { description: 'Kelas masih memiliki Siswa/Mapel.' });
    if (confirm(`Hapus kelas ${item.nama}?`)) {
        router.delete(ManagementKelasController.destroy['/admin/kelas-management/delete/{id}']({ id: item.id }).url, {
            onSuccess: () => toast.success('Kelas dihapus'),
            onError: (err) => toast.error(err.message || 'Gagal menghapus'),
        });
    }
};

// Modal Add Student
const openStudentModal = (item: any) => {
    studentForm.reset();
    studentSearch.value = '';
    selectedClassForStudent.value = item;
    studentForm.kelas_id = item.id;
    showStudentModal.value = true;
};
const filteredStudents = computed(() => {
    if (!studentSearch.value) return props.available_students;
    const lower = studentSearch.value.toLowerCase();
    return props.available_students.filter((s: any) => s.user.name.toLowerCase().includes(lower) || s.nis.toLowerCase().includes(lower));
});
const selectStudent = (nis: any) => {
    studentForm.siswa_id = nis;
}; // Asumsi ID yang dikirim NIS, sesuaikan jika ID primary key
const submitStudent = () => {
    if (!studentForm.siswa_id) return toast.error('Pilih siswa dulu');
    studentForm.post(ManagementKelasController.storeSiswa().url, {
        onSuccess: () => {
            showStudentModal.value = false;
            toast.success('Siswa ditambahkan');
            reloadData();
        },
        onError: () => toast.error('Gagal menambahkan siswa'),
    });
};

const openListModal = (item: any) => {
    selectedClassList.value = item;
    showListModal.value = true;
};

const removeStudentFromClass = (siswa: any) => {
    if (confirm(`Keluarkan ${siswa.user.name} dari kelas ini?`)) {
        router.post(
            ManagementKelasController.removeStudent().url,
            { siswa_id: siswa.nis },
            {
                // Pastikan controller ada
                onSuccess: () => {
                    toast.success('Siswa dikeluarkan');
                    // Update local state (optional, or rely on inertia reload)
                    showListModal.value = false;
                },
                preserveScroll: true,
            },
        );
    }
};

const getLevelColor = (tingkat: string) => {
    const map: any = { '10': 'emerald', '11': 'amber', '12': 'rose' };
    const color = map[String(tingkat)] || 'gray';
    return `bg-${color}-100 text-${color}-700 border-${color}-200`;
};
</script>

<template>
    <div class="min-h-screen w-full bg-gray-50/50 pb-20 font-sans">
        <div class="mb-8 flex flex-col justify-between gap-4 px-1 sm:flex-row sm:items-end">
            <Breadcrumb :items="breadcrumbs" />
            <button
                @click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 active:scale-95"
            >
                <Plus class="h-4 w-4" /> <span>Buat Kelas Baru</span>
            </button>
        </div>

        <div class="mb-8 flex flex-col gap-4 px-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex overflow-x-auto rounded-lg bg-white p-1 shadow-sm ring-1 ring-gray-200 lg:w-fit">
                <button
                    v-for="item in tingkatOptions"
                    :key="item"
                    @click="changeTingkat(item)"
                    class="rounded-md px-4 py-1.5 text-sm font-medium transition-all"
                    :class="
                        selectedTingkat === item ? 'bg-indigo-50 text-indigo-700 shadow-sm ring-1 ring-indigo-200' : 'text-gray-500 hover:bg-gray-50'
                    "
                >
                    {{ item === 'Semua' ? 'Semua' : `Kelas ${item}` }}
                </button>
            </div>
            <div class="relative w-full lg:w-72">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama kelas..."
                    class="h-10 w-full rounded-lg border-0 bg-white pr-4 pl-10 text-sm shadow-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-indigo-600"
                />
            </div>
        </div>

        <div v-if="kelas.length > 0" class="grid grid-cols-1 gap-6 px-4 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="item in kelas"
                :key="item.id"
                class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all hover:-translate-y-1 hover:shadow-md"
            >
                <div class="h-1.5 w-full" :class="getLevelColor(item.tingkat).split(' ')[0].replace('bg-', 'bg-')"></div>

                <div class="p-5">
                    <div class="mb-4">
                        <span class="mb-2 inline-flex items-center rounded-md border px-2 py-1 text-xs font-bold" :class="getLevelColor(item.tingkat)"
                            >Tingkat {{ item.tingkat }}</span
                        >
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600">{{ item.nama }}</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div
                            @click="openListModal(item)"
                            class="flex cursor-pointer items-center gap-3 rounded-xl bg-gray-50 p-3 transition-colors hover:bg-indigo-50 hover:ring-1 hover:ring-indigo-200"
                        >
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-indigo-600 shadow-sm">
                                <Users class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">Siswa</p>
                                <p class="font-bold text-gray-900">{{ item.siswa_count }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-orange-600 shadow-sm">
                                <BookOpen class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">Mapel</p>
                                <p class="font-bold text-gray-900">{{ item.matpel_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto border-t border-gray-100 bg-gray-50/50 px-5 py-3">
                    <div class="flex gap-2">
                        <button
                            @click="openStudentModal(item)"
                            class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-indigo-600 shadow-sm hover:bg-indigo-50"
                            title="Tambah Siswa"
                        >
                            <UserPlus class="h-3.5 w-3.5" />
                        </button>
                        <button
                            @click="openEditModal(item)"
                            class="flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white py-2 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:text-indigo-600"
                        >
                            <Edit class="h-3.5 w-3.5" /> Edit
                        </button>
                        <button
                            @click="deleteItem(item)"
                            class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-gray-400 shadow-sm hover:bg-red-50 hover:text-red-600"
                            :class="{ 'cursor-not-allowed opacity-50': item.siswa_count > 0 || item.matpel_count > 0 }"
                        >
                            <Trash2 class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 px-4 py-20 text-center"
        >
            <GraduationCap class="mb-4 h-8 w-8 text-gray-400" />
            <h3 class="text-lg font-semibold text-gray-900">Tidak ada kelas</h3>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
                <div class="flex justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="font-bold text-gray-900">{{ isEditing ? 'Edit Kelas' : 'Buat Kelas' }}</h3>
                    <button @click="showModal = false"><X class="h-5 w-5 text-gray-400" /></button>
                </div>
                <form @submit.prevent="submit" class="space-y-4 p-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tingkat</label>
                        <select
                            v-model="form.tingkat"
                            class="w-full rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-2 text-sm transition-all outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Kelas</label>
                        <Input v-model="form.nama" placeholder="Contoh: X RPL 1" />
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showModal = false" class="rounded-lg border border-gray-100 px-4 py-2 text-sm hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showStudentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex justify-between border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <div>
                        <h3 class="font-bold">Pilih Siswa</h3>
                        <p class="text-xs text-gray-500">Ke: {{ selectedClassForStudent?.nama }}</p>
                    </div>
                    <button @click="showStudentModal = false"><X class="h-5 w-5 text-gray-400" /></button>
                </div>
                <form @submit.prevent="submitStudent" class="flex h-[500px] flex-col">
                    <div class="border-b border-gray-100 p-4">
                        <input
                            v-model="studentSearch"
                            type="text"
                            placeholder="Cari Nama/NIS..."
                            class="w-full rounded-lg border-gray-200 bg-gray-50 px-4 py-2 text-sm"
                        />
                    </div>
                    <div class="flex-1 overflow-y-auto p-2">
                        <div v-if="filteredStudents.length === 0" class="py-10 text-center text-sm text-gray-400">Tidak ada siswa</div>
                        <div
                            v-for="s in filteredStudents"
                            :key="s.nis"
                            @click="selectStudent(s.nis)"
                            class="mb-2 flex cursor-pointer items-center justify-between rounded-lg p-3 hover:bg-indigo-50"
                            :class="(studentForm as any).siswa_id === s.nis ? 'bg-indigo-50 ring-1 ring-indigo-500' : ''"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 text-xs font-bold">
                                    {{ s.user.name[0] }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ s.user.name }}</p>
                                    <p class="text-xs text-gray-500">{{ s.nis }}</p>
                                </div>
                            </div>
                            <CheckCircle v-if="(studentForm as any).siswa_id === s.nis" class="h-5 w-5 text-indigo-600" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 p-4">
                        <button type="button" @click="showStudentModal = false" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm">
                            Batal
                        </button>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showListModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Daftar Siswa</h3>
                        <p class="text-xs text-gray-500">Kelas: {{ selectedClassList?.nama }} ({{ selectedClassList?.siswa_count }} Siswa)</p>
                    </div>
                    <button @click="showListModal = false" class="rounded-lg p-1 text-gray-400 hover:bg-gray-200">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="flex h-[500px] flex-col">
                    <div class="flex-1 overflow-y-auto p-4">
                        <div
                            v-if="!selectedClassList?.siswa || selectedClassList.siswa.length === 0"
                            class="flex h-full flex-col items-center justify-center text-gray-400"
                        >
                            <Users class="mb-2 h-10 w-10 opacity-50" />
                            <p class="text-sm">Belum ada siswa di kelas ini.</p>
                        </div>

                        <div v-else class="space-y-2">
                            <div
                                v-for="siswa in selectedClassList.siswa"
                                :key="siswa.id"
                                class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-3 shadow-sm"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700"
                                    >
                                        {{ siswa.user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ siswa.user.name }}</p>
                                        <p class="font-mono text-xs text-gray-500">NIS: {{ siswa.nis }}</p>
                                    </div>
                                </div>
                                <button
                                    @click="removeStudentFromClass(siswa)"
                                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                    title="Keluarkan dari kelas"
                                >
                                    <UserMinus class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
                        <button
                            @click="showListModal = false"
                            class="w-full rounded-lg border border-gray-100 bg-white py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
