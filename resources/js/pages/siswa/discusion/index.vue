<script setup lang="ts">
import DiscusionController from '@/actions/App/Http/Controllers/DiscusionController';
import PageTitle from '@/layouts/page-title.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const matpels = computed(() => page.props.matpels);
const kelas_id = computed(() => page.props.kelas_id);
</script>

<template>
    <div class="mx-auto px-4">
        <PageTitle title="Diskusi Kelompok" subtitle="Pilih mata pelajaran untuk mulai berinteraksi" />
        <div class="mt-4 space-y-3">
            <div
                v-for="data in matpels"
                :key="data.matpel_kode"
                class="relative flex items-center overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
            >
                <div :class="['absolute top-0 bottom-0 left-0 w-1.5', data.kategori === 'Produktif' ? 'bg-blue-600' : 'bg-amber-500']"></div>

                <div class="flex-grow pl-2">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                            {{ data.matpel_kode }}
                        </span>
                        <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 uppercase">
                            {{ data.kategori }}
                        </span>
                    </div>

                    <h3 class="text-base leading-none font-extrabold tracking-tight text-slate-800 uppercase">
                        {{ data.nama_matpel }}
                    </h3>

                    <div class="mt-2 flex items-center text-xs font-medium text-slate-500">
                        <span class="truncate"
                            >Guru: <span class="text-slate-700">{{ data.nama_guru }}</span></span
                        >
                        <span class="mx-2 text-slate-300">•</span>
                        <span>{{ data.nama_kelas }}</span>
                    </div>
                </div>

                <div class="ml-4 flex-shrink-0">
                    <Link
                        :href="
                            DiscusionController.show({
                                kelas_id: data.kelas_id,
                                matpels_id: data.matpel_kode,
                            })
                        "
                        class="rounded-lg bg-slate-900 px-5 py-2 text-xs font-bold tracking-wide text-white uppercase transition-colors active:bg-slate-700"
                    >
                        Buka
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="matpels.length === 0" class="mt-10 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 py-12 text-center">
            <p class="font-medium text-slate-400 italic">Data mata pelajaran tidak tersedia saat ini.</p>
        </div>
    </div>
</template>
