<script setup lang="ts">
import { showMateri } from '@/actions/App/Http/Controllers/MateriController';
import HugeiconsTaskDaily01 from '@/icons/HugeiconsTaskDaily01.vue';
import HugeiconsTeaching from '@/icons/HugeiconsTeaching.vue';
import IcBaselineRemoveRedEye from '@/icons/IcBaselineRemoveRedEye.vue';
import MaterialSymbolsCheckCircleUnreadOutline from '@/icons/MaterialSymbolsCheckCircleUnreadOutline.vue';
import MaterialSymbolsLightBook5Rounded from '@/icons/MaterialSymbolsLightBook5Rounded.vue';
import MdiFileWord from '@/icons/MdiFileWord.vue';
import NotFoundVector from '@/icons/NotFoundVector.vue';
import { view } from '@/routes/siswa/materi';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import vSelect from 'vue-select';
const page = usePage();
const matpelCurrent = ref<any>(page.props.matpels.find((e) => e.matpel_kode === page.props.current_matpel));

watch(
    () => matpelCurrent.value,
    function (e: any) {
        if (e && e.matpel_kode) {
            router.visit(
                showMateri({
                    query: {
                        matpel_id: e.matpel_kode,
                    },
                }).url,
            );
        }
    },
);
</script>

<template>
    <div class="py-4">
        <h1 class="text-xl font-bold text-neutral-700">Materi</h1>
        <p class="text-sm">Halaman ini menyajikan materi pada masing masing mata pelajaran</p>
    </div>
    <!-- Select Matpel -->
    <vSelect
        placeholder="Pilih Kelas"
        class="mb-3 lg:max-w-sm"
        v-model="matpelCurrent"
        :selected="$page.props.matpels.filter((e) => e.kode_matpel == $page.props.current_matpel)"
        :options="$page.props.matpels"
        label="nama_matpel"
        index="kode_matpel"
    />
    <!-- List Materi By Matpel -->
    <div v-if="$page.props.materials.length > 0" class="grid grid-cols-1 gap-3 lg:grid-cols-3">
        <div v-for="i in $page.props.materials">
            <div
                class="cursor-pointer rounded-lg bg-white p-4 shadow transition-all hover:translate-y-1 hover:bg-neutral-50 hover:ring-1 hover:ring-neutral-400"
            >
                <h1>
                    <div class="mb-2 inline-flex items-center space-x-1 rounded text-sm text-primary">
                        <MdiFileWord />
                        <span class="text-xs">Materi #{{ i.nomor_materi }}</span>
                    </div>
                    <p class="line-clamp-2 font-semibold">
                        {{ i.title }}
                    </p>
                </h1>
                <div class="mt-2 flex flex-col space-y-1 text-sm">
                    <div class="flex items-center text-xs text-neutral-500">
                        <HugeiconsTeaching />
                        <span class="ml-1 block font-semibold">
                            {{ i.nama_guru }}
                        </span>
                    </div>
                    <div class="flex items-center text-xs text-neutral-500">
                        <MaterialSymbolsLightBook5Rounded />
                        <span class="ml-1 block font-semibold">{{ i.nama_matpel }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center text-xs text-neutral-500">
                            <HugeiconsTaskDaily01 />
                            <span class="ml-1 block font-semibold">{{ i.nama_kelas }}</span>
                        </div>
                        <MaterialSymbolsCheckCircleUnreadOutline class="text-green-600" />
                    </div>
                </div>
                <Link
                    :href="view({ id: i.materi_id })"
                    class="mt-3 flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-orange-500 px-1 py-2 text-sm text-neutral-800"
                >
                    <IcBaselineRemoveRedEye />
                    <span>Lihat Materi</span>
                </Link>
            </div>
        </div>
    </div>
    <div v-else class="flex min-h-[400px] w-full flex-col items-center justify-center rounded-lg border border-neutral-200 bg-white py-5">
        <NotFoundVector />
        <h1 class="mt-4 text-sm">Whoops!Belum Ada Materi! Untuk saat ini.</h1>
    </div>
</template>
