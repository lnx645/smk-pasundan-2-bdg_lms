<script setup lang="ts">
import { formatTanggal } from '@/lib/utils'; // Helper tanggal kamu
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner'; // Asumsi pakai sonner/toast

// ICONS
import { batalkanPengumpulan } from '@/actions/App/Http/Controllers/TugasSiswaController';
import IcBaselineDelete from '@/icons/IcBaselineDelete.vue';
import MaterialSymbolsCheckCircle from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import MaterialSymbolsUpload from '@/icons/MaterialSymbolsUpload.vue';
import MdiFileDocument from '@/icons/SolarDocumentAddBold.vue';
import HugeiconsFileAttachment from '@/icons/StreamlinePlumpEmailAttachmentDocumentSolid.vue';
import { kerjakanSimpan } from '@/routes/siswa/tugas/kerjakan';
import dayjs from 'dayjs';
// PROPS
// Pastikan props dari controller memuat detail tugas & submission (jika ada)
const page = usePage();
const tugas = computed(() => page.props.tugas as any);
const submission = computed(() => page.props.submission as any); // Data jawaban siswa (null jika belum)

const isOverdue = computed(() => {
    return dayjs().isAfter(dayjs(tugas.value.deadline));
});

const isSubmitted = computed(() => !!submission.value);
const isGraded = computed(() => submission.value?.nilai !== null && submission.value?.nilai !== undefined);
// FORM
const form = useForm({
    tugas_id: tugas.value.tugasID,
    jawaban_text: submission.value?.jawaban_text || '',
    file: null as File | null,
});

// FILE HANDLING
const fileInput = ref<HTMLInputElement | null>(null);
const dragging = ref(false);

function handleFileSelect(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.file = target.files[0];
    }
}

function handleDrop(event: DragEvent) {
    dragging.value = false;
    if (event.dataTransfer?.files && event.dataTransfer.files.length > 0) {
        form.file = event.dataTransfer.files[0];
    }
}

function removeFile() {
    form.file = null;
    if (fileInput.value) fileInput.value.value = '';
}

// SUBMIT ACTION
function submitTugas() {
    if (!form.file && !form.jawaban_text && tugas.value.mode_pengumpulan !== 'text') {
        toast.error('Harap lampirkan file jawaban.');
        return;
    }

    form.submit(
        kerjakanSimpan({
            id: tugas.value.tugasID,
        }),
        {
            onSuccess: () => {
                toast.success('Tugas berhasil dikumpulkan!');
            },
            onError: () => toast.error('Gagal mengumpulkan tugas.'),
        },
    );
}

function unsubmit() {
    if (confirm('Batalkan pengumpulan? Anda harus mengupload ulang jawaban.')) {
        router.delete(
            batalkanPengumpulan({
                id: submission.value.jawabanID,
            }).url,
        );
    }
}
</script>

<template>
    <div class="mx-auto mt-4 max-w-7xl pb-20">
        <div class="mb-6 border-b border-neutral-200 pb-6">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm text-neutral-500">
                        <span class="font-medium text-blue-600">{{ tugas.matpel?.nama || 'Matematika' }}</span>
                        <span>•</span>
                        <span>{{ tugas.user.name || 'Pak Guru' }}</span>
                    </div>
                    <h1 class="text-3xl font-bold tracking-tight text-neutral-800">{{ tugas.title }}</h1>
                </div>

                <div class="flex flex-col items-end gap-1">
                    <div
                        v-if="isGraded"
                        class="flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700"
                    >
                        <MaterialSymbolsCheckCircle class="text-xl" />
                        Nilai: {{ submission.nilai.angka }} / 100
                    </div>
                    <div
                        v-else-if="isSubmitted"
                        class="flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700"
                    >
                        <MaterialSymbolsCheckCircle class="text-xl" />
                        Sudah Dikumpulkan
                    </div>
                    <div v-else-if="isOverdue" class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-bold text-red-600">
                        Terlewat Deadline
                    </div>
                    <div v-else class="rounded-lg border border-neutral-200 bg-neutral-100 px-4 py-2 text-sm font-medium text-neutral-600">
                        Ditugaskan
                    </div>

                    <p class="mt-1 text-xs text-neutral-400">Tenggat: {{ formatTanggal(tugas.deadline) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">
                <div class="min-h-[300px] rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 border-b border-neutral-100 pb-2 text-sm font-bold tracking-wider text-neutral-500 uppercase">Instruksi</h3>
                    <div class="prose max-w-none text-sm leading-relaxed text-neutral-700" v-html="tugas.content"></div>
                </div>

                <div v-if="tugas.attachments && tugas.attachments.length > 0">
                    <h3 class="mb-3 text-sm font-bold text-neutral-700">Lampiran Pendukung</h3>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <a
                            v-for="file in tugas.attachments"
                            :key="file.id"
                            :href="file.url"
                            target="_blank"
                            class="group flex items-center gap-3 rounded-lg border border-neutral-200 bg-white p-3 transition-all hover:border-blue-400 hover:bg-blue-50"
                        >
                            <div class="flex h-10 w-10 items-center justify-center rounded bg-neutral-100 text-neutral-500 group-hover:text-blue-600">
                                <HugeiconsFileAttachment />
                            </div>
                            <div class="overflow-hidden">
                                <p class="truncate text-sm font-medium text-neutral-800 group-hover:text-blue-700">{{ file.name }}</p>
                                <p class="text-xs text-neutral-400">Klik untuk unduh</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="sticky top-6 lg:col-span-4">
                <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-lg">
                    <div class="flex items-center justify-between border-b border-neutral-200 bg-neutral-50 px-5 py-4">
                        <h3 class="font-bold text-neutral-700">Lembar Jawab</h3>
                        <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700 uppercase">
                            {{ tugas.mode_pengumpulan === 'text' ? 'Teks' : 'File Upload' }}
                        </span>
                    </div>

                    <div v-if="isGraded" class="p-6 text-center">
                        <div
                            class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-2xl font-bold text-emerald-600"
                        >
                            {{ submission.nilai.angka }}
                        </div>
                        <h4 class="font-bold text-neutral-800">Tugas Selesai Dinilai</h4>
                        <p class="mt-1 text-sm text-neutral-500">"{{ submission.feedback || 'Kerja bagus, pertahankan!' }}"</p>
                        <div class="mt-6 border-t border-neutral-100 pt-4">
                            <p class="mb-2 text-xs text-neutral-400">Jawaban Anda:</p>
                            <a
                                v-if="submission.file_url"
                                :href="submission.file_url"
                                target="_blank"
                                class="flex items-center justify-center gap-1 text-sm text-blue-600 hover:underline"
                            >
                                <MdiFileDocument /> Lihat File Anda
                            </a>
                            <p v-else class="rounded border border-neutral-100 bg-neutral-50 p-3 text-sm text-neutral-700 italic">
                                {{ submission.jawaban_text }}
                            </p>
                        </div>
                    </div>

                    <div v-else-if="isSubmitted" class="p-6 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                            <MaterialSymbolsCheckCircle class="text-2xl" />
                        </div>
                        <h4 class="font-bold text-neutral-800">Berhasil Dikirim</h4>
                        <p class="mt-1 mb-4 text-xs text-neutral-500">Dikirim pada {{ formatTanggal(submission.created_at) }}</p>

                        <div v-if="!isOverdue" class="space-y-3">
                            <p class="text-xs text-neutral-400">Ingin mengubah jawaban?</p>
                            <button
                                @click="unsubmit"
                                class="w-full rounded-lg border border-red-200 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                            >
                                Batalkan Pengumpulan
                            </button>
                        </div>
                        <div v-else class="rounded bg-amber-50 p-2 text-xs text-amber-600">Masa pengumpulan telah berakhir. Tidak dapat diubah.</div>
                    </div>

                    <div v-else class="space-y-4 p-5">
                        <div v-if="isOverdue" class="mb-2 rounded border border-red-100 bg-red-50 p-3 text-xs text-red-600">
                            <span class="font-bold">Terlambat!</span> Anda mengumpulkan melewati tenggat waktu.
                        </div>

                        <div v-if="!isOverdue && ['text', 'mixed'].includes(tugas.mode_pengumpulan)">
                            <label class="mb-1 block text-xs font-semibold text-neutral-500">Jawaban Teks</label>
                            <textarea
                                v-model="form.jawaban_text"
                                rows="6"
                                class="w-full rounded-lg border-neutral-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ketik jawaban Anda di sini..."
                            ></textarea>
                        </div>

                        <div v-if="!isOverdue && ['file', 'foto', 'mixed'].includes(tugas.mode_pengumpulan)">
                            <label class="mb-1 block text-xs font-semibold text-neutral-500">Upload File</label>

                            <div
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="handleDrop"
                                @click="fileInput?.click()"
                                class="group cursor-pointer rounded-xl border-2 border-dashed p-6 text-center transition-all duration-200"
                                :class="dragging ? 'border-blue-500 bg-blue-50' : 'border-neutral-300 hover:border-blue-400 hover:bg-neutral-50'"
                            >
                                <input
                                    type="file"
                                    ref="fileInput"
                                    class="hidden"
                                    @change="handleFileSelect"
                                    :accept="tugas.mode_pengumpulan === 'foto' ? 'image/*' : '*'"
                                />
                                <div v-if="!form.file">
                                    <div
                                        class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-neutral-100 text-neutral-400 transition-colors group-hover:bg-blue-100 group-hover:text-blue-500"
                                    >
                                        <MaterialSymbolsUpload class="text-xl" />
                                    </div>
                                    <p class="text-sm font-medium text-neutral-700">Klik atau Drag File</p>
                                    <p class="mt-1 text-xs text-neutral-400">PDF, Word, JPG (Max 10MB)</p>
                                </div>

                                <div v-else class="flex items-center justify-between rounded border border-blue-100 bg-white p-2 shadow-sm">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-blue-100 text-blue-600">
                                            <MdiFileDocument />
                                        </div>
                                        <p class="max-w-[150px] truncate text-xs font-medium text-neutral-700">{{ form.file.name }}</p>
                                    </div>
                                    <button @click.stop="removeFile" class="p-1 text-red-400 hover:text-red-600">
                                        <IcBaselineDelete />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            v-if="!isOverdue"
                            @click="submitTugas"
                            :disabled="form.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 py-2.5 font-semibold text-white shadow-sm transition-all hover:bg-blue-700 hover:shadow-md active:scale-95 disabled:cursor-not-allowed disabled:opacity-70"
                        >
                            <span v-if="form.processing">Mengirim...</span>
                            <span v-else>Serahkan Tugas</span>
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex items-start gap-3 rounded-lg border border-yellow-100 bg-yellow-50 p-3">
                    <span class="text-lg text-yellow-600">💡</span>
                    <p class="text-xs leading-snug text-yellow-800">
                        Pastikan file yang diunggah sudah benar. Anda tidak bisa mengubah jawaban setelah tenggat waktu berakhir.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
