<script setup lang="ts">
import { formatTanggal } from '@/lib/utils';
import { router, useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';

// ICONS
import { batalkanPengumpulan } from '@/actions/App/Http/Controllers/TugasSiswaController';
import IcBaselineDelete from '@/icons/IcBaselineDelete.vue';
import MaterialSymbolsCheckCircle from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import MaterialSymbolsUpload from '@/icons/MaterialSymbolsUpload.vue';
import MdiFileDocument from '@/icons/SolarDocumentAddBold.vue';
import HugeiconsFileAttachment from '@/icons/StreamlinePlumpEmailAttachmentDocumentSolid.vue';
import { kerjakanSimpan } from '@/routes/siswa/tugas/kerjakan';

const page = usePage();
const tugas = computed(() => page.props.tugas as any);
const submission = computed(() => page.props.submission as any);

// LOGIC STATUS
const isOverdue = computed(() => dayjs().isAfter(dayjs(tugas.value.deadline)));
const isSubmitted = computed(() => !!submission.value);
const isGraded = computed(() => submission.value?.nilai !== null && submission.value?.nilai !== undefined);

// --- LOGIC DINAMIS BERDASARKAN NILAI ---
const gradingStyle = computed(() => {
    const nilai = submission.value?.nilai?.angka || 0;
    if (nilai >= 85)
        return {
            bg: 'bg-emerald-50 border-emerald-200',
            circle: 'bg-emerald-100 text-emerald-600 border-emerald-200',
            text: 'text-emerald-700',
            judul: 'Luar Biasa!',
            feedback: 'Hasil yang sangat memuaskan, pertahankan prestasimu!',
        };
    if (nilai >= 75)
        return {
            bg: 'bg-blue-50 border-blue-200',
            circle: 'bg-blue-100 text-blue-600 border-blue-200',
            text: 'text-blue-700',
            judul: 'Tugas Selesai Dinilai',
            feedback: 'Kerja bagus! Sedikit lagi menuju sempurna.',
        };
    if (nilai >= 60)
        return {
            bg: 'bg-amber-50 border-amber-200',
            circle: 'bg-amber-100 text-amber-600 border-amber-200',
            text: 'text-amber-700',
            judul: 'Cukup Baik',
            feedback: 'Sudah cukup baik, pelajari lagi bagian yang kurang ya.',
        };
    return {
        bg: 'bg-rose-50 border-rose-200',
        circle: 'bg-rose-100 text-rose-600 border-rose-200',
        text: 'text-rose-700',
        judul: 'Perlu Perbaikan',
        feedback: 'Jangan berkecil hati, silakan pelajari kembali materinya.',
    };
});

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
    if (target.files?.length) form.file = target.files[0];
}

function handleDrop(event: DragEvent) {
    dragging.value = false;
    if (event.dataTransfer?.files?.length) form.file = event.dataTransfer.files[0];
}

function removeFile() {
    form.file = null;
    if (fileInput.value) fileInput.value.value = '';
}

function submitTugas() {
    if (!form.file && !form.jawaban_text && tugas.value.mode_pengumpulan !== 'text') {
        toast.error('Harap lampirkan jawaban Anda.');
        return;
    }

    form.put(kerjakanSimpan({ id: tugas.value.tugasID }).url, {
        onSuccess: () => toast.success('Tugas berhasil dikumpulkan!'),
        onError: () => toast.error('Gagal mengumpulkan tugas.'),
    });
}

function unsubmit() {
    if (confirm('Batalkan pengumpulan? Anda harus mengunggah ulang jawaban.')) {
        router.delete(batalkanPengumpulan({ id: submission.value.jawabanID }).url);
    }
}
</script>

<template>
    <div class="mx-auto mt-4 max-w-7xl px-4 pb-20 sm:px-6">
        <div class="mb-6 border-b border-neutral-200 pb-6">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-sm text-neutral-500">
                        <span class="font-semibold tracking-wider text-blue-600 uppercase">{{ tugas.matpel?.nama }}</span>
                        <span>•</span>
                        <span>{{ tugas.user.name }}</span>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-neutral-900">{{ tugas.title }}</h1>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <div
                        v-if="isGraded"
                        :class="['flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-bold', gradingStyle.bg, gradingStyle.text]"
                    >
                        <MaterialSymbolsCheckCircle class="text-lg" />
                        Nilai: {{ submission.nilai.angka }} / 100
                    </div>
                    <div
                        v-else-if="isSubmitted"
                        class="flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-4 py-1.5 text-sm font-bold text-blue-700"
                    >
                        <MaterialSymbolsCheckCircle class="text-lg" />
                        Sudah Dikumpulkan
                    </div>
                    <div v-else-if="isOverdue" class="rounded-full border border-red-200 bg-red-50 px-4 py-1.5 text-sm font-bold text-red-600">
                        Terlewat Deadline
                    </div>

                    <p class="text-xs font-medium text-neutral-400">Tenggat: {{ formatTanggal(tugas.deadline) }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
            <div class="space-y-5 lg:col-span-8">
                <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md">
                    <div class="mb-4 flex items-center justify-between border-b border-neutral-50 pb-3">
                        <h3 class="flex items-center gap-2 text-[11px] font-bold tracking-widest text-neutral-400 uppercase">
                            <MdiFileDocument class="text-lg text-blue-500" /> Instruksi Tugas
                        </h3>
                        <span class="text-[10px] font-medium text-neutral-400 italic">Terakhir diupdate: {{ formatTanggal(tugas.updated_at) }}</span>
                    </div>

                    <div class="prose prose-sm max-w-none leading-relaxed text-neutral-600 prose-blue" v-html="tugas.content"></div>
                </div>

                <div v-if="tugas.attachments?.length">
                    <h3 class="mb-3 px-1 text-xs font-bold tracking-tight text-neutral-500 uppercase">Materi Pendukung</h3>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <a
                            v-for="file in tugas.attachments"
                            :key="file.id"
                            :href="file.url"
                            target="_blank"
                            class="group flex items-center gap-3 rounded-xl border border-neutral-100 bg-white p-3 transition-all hover:border-blue-400 hover:bg-blue-50/50"
                        >
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600 shadow-sm transition-all group-hover:bg-blue-600 group-hover:text-white"
                            >
                                <HugeiconsFileAttachment class="text-lg" />
                            </div>
                            <div class="overflow-hidden">
                                <p class="truncate text-xs font-bold text-neutral-800">{{ file.name }}</p>
                                <p class="text-[10px] font-medium text-neutral-400">Unduh Lampiran</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="sticky top-6 space-y-4 lg:col-span-4">
                <div class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-xl shadow-neutral-200/40">
                    <div class="flex items-center justify-between bg-primary px-5 py-3 text-white">
                        <h3 class="text-sm font-bold tracking-wide">Lembar Jawab</h3>
                        <div class="rounded bg-blue-500/20 px-2 py-0.5 text-[9px] font-black text-blue-300 uppercase ring-1 ring-blue-500/30">
                            {{ tugas.mode_pengumpulan }}
                        </div>
                    </div>

                    <div v-if="isGraded" :class="['p-6 text-center transition-all', gradingStyle.bg]">
                        <div class="mb-4 flex items-center justify-center gap-4">
                            <div
                                :class="[
                                    'flex h-14 w-14 rotate-3 items-center justify-center rounded-2xl border-2 text-2xl font-black shadow-sm',
                                    gradingStyle.circle,
                                ]"
                            >
                                {{ submission.nilai.angka }}
                            </div>
                            <div class="text-left">
                                <h4 :class="['text-sm font-extrabold tracking-tight uppercase', gradingStyle.text]">{{ gradingStyle.judul }}</h4>
                                <p class="text-[11px] leading-tight text-neutral-500 italic">"{{ submission.feedback || gradingStyle.feedback }}"</p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-xl bg-white/60 p-3 ring-1 ring-black/5 backdrop-blur-sm">
                            <p class="mb-2 text-[9px] font-bold tracking-widest text-neutral-400 uppercase">File Jawaban Anda</p>
                            <a
                                v-if="submission.file_url"
                                :href="submission.file_url"
                                target="_blank"
                                class="flex items-center justify-center gap-2 rounded-lg border border-blue-100 bg-white py-2 text-xs font-bold text-blue-600 shadow-sm transition-all hover:bg-blue-600 hover:text-white"
                            >
                                <MdiFileDocument class="text-base" /> Lihat Dokumen
                            </a>
                            <div v-else class="px-2 text-xs text-neutral-700 italic">{{ submission.jawaban_text }}</div>
                        </div>
                    </div>

                    <div v-else-if="isSubmitted" class="p-6 text-center">
                        <div
                            class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-500 ring-4 ring-emerald-50/50"
                        >
                            <MaterialSymbolsCheckCircle class="text-2xl" />
                        </div>
                        <h4 class="text-sm font-bold text-neutral-800">Tugas Terkirim</h4>
                        <p class="mt-1 text-[10px] text-neutral-400 italic">Diterima: {{ dayjs(submission.created_at).format('DD MMM, HH:mm') }}</p>

                        <div v-if="!isOverdue" class="mt-5">
                            <button
                                @click="unsubmit"
                                class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-2.5 text-[11px] font-bold text-neutral-500 transition-all hover:border-red-100 hover:bg-red-50 hover:text-red-600 active:scale-95"
                            >
                                Batalkan Pengiriman
                            </button>
                        </div>
                    </div>

                    <div v-else class="space-y-4 p-5">
                        <div v-if="isOverdue" class="flex items-center gap-3 rounded-xl bg-rose-50 p-3 ring-1 ring-rose-100">
                            <span class="text-lg">⚠️</span>
                            <p class="text-[10px] leading-tight font-semibold text-rose-700">
                                Deadline telah lewat. Pengumpulan Anda akan ditandai terlambat.
                            </p>
                        </div>

                        <div v-if="['text', 'mixed'].includes(tugas.mode_pengumpulan)">
                            <textarea
                                v-model="form.jawaban_text"
                                rows="4"
                                class="w-full rounded-xl border-neutral-100 bg-neutral-50 text-xs placeholder:text-neutral-300 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                                placeholder="Tuliskan jawaban singkat..."
                            ></textarea>
                        </div>

                        <div v-if="['file', 'foto', 'mixed'].includes(tugas.mode_pengumpulan)">
                            <div
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="handleDrop"
                                @click="fileInput?.click()"
                                :class="[
                                    'group cursor-pointer rounded-xl border-2 border-dashed px-4 py-6 text-center transition-all',
                                    dragging ? 'border-blue-500 bg-blue-50' : 'border-neutral-100 hover:border-blue-300 hover:bg-neutral-50',
                                ]"
                            >
                                <input
                                    type="file"
                                    ref="fileInput"
                                    class="hidden"
                                    @change="handleFileSelect"
                                    :accept="tugas.mode_pengumpulan === 'foto' ? 'image/*' : '*'"
                                />

                                <div v-if="!form.file" class="space-y-1">
                                    <MaterialSymbolsUpload class="mx-auto text-xl text-neutral-300 transition-colors group-hover:text-blue-500" />
                                    <p class="text-[11px] font-bold text-neutral-500">Pilih File Jawaban</p>
                                </div>

                                <div v-else class="flex items-center justify-between rounded-lg bg-white p-2 shadow-sm ring-1 ring-black/5">
                                    <div class="flex items-center gap-2 overflow-hidden px-1">
                                        <MdiFileDocument class="shrink-0 text-lg text-blue-500" />
                                        <div class="truncate text-left">
                                            <p class="truncate text-[10px] font-bold text-neutral-800">{{ form.file.name }}</p>
                                            <p class="font-mono text-[9px] text-neutral-400">{{ (form.file.size / 1024 / 1024).toFixed(1) }}MB</p>
                                        </div>
                                    </div>
                                    <button @click.stop="removeFile" class="p-1.5 text-neutral-400 transition-colors hover:text-red-500">
                                        <IcBaselineDelete class="text-base" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            @click="submitTugas"
                            :disabled="form.processing"
                            class="group relative flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-blue-600 py-3 text-xs font-bold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700 active:scale-95 disabled:opacity-50"
                        >
                            <span v-if="form.processing" class="flex items-center gap-2">
                                <svg class="h-3 w-3 animate-spin text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                Memproses...
                            </span>
                            <template v-else>
                                <span>Serahkan Jawaban</span>
                                <MaterialSymbolsUpload class="transition-transform group-hover:-translate-y-0.5" />
                            </template>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3 rounded-xl border border-blue-50 bg-blue-50/30 p-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-[10px]">💡</span>
                    <p class="text-[10px] leading-tight font-medium text-blue-800/70">
                        Double-check jawabanmu. Setelah diserahkan, perubahan hanya bisa dilakukan sebelum deadline.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Tambahan transisi halus */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
