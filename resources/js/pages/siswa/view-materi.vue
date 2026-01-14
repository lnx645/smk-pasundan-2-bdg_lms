<script setup lang="ts">
import { showMateri } from '@/actions/App/Http/Controllers/MateriController';
import VideoPlayer from '@/components/video-player.vue';
import FormkitDatetime from '@/icons/FormkitDatetime.vue';
import HugeiconsTeaching from '@/icons/HugeiconsTeaching.vue';
import MaterialSymbolsLightBook5Rounded from '@/icons/MaterialSymbolsLightBook5Rounded.vue';
import NotFoundVector from '@/icons/NotFoundVector.vue';
import { Link, usePage } from '@inertiajs/vue3';
const page = usePage().props;
</script>

<template>
    <div v-if="page.materi" class="bg-white px-3 pt-2 lg:px-8 lg:py-5">
        <h1 class="text-lg font-semibold text-neutral-700 capitalize lg:text-xl">
            {{ page.materi.title }}
        </h1>
        <div class="mt-2 flex flex-col space-y-1 text-sm">
            <div class="flex items-center text-xs text-neutral-600">
                <HugeiconsTeaching />
                <span class="ml-1 block">
                    {{ page.materi.nama_guru }}
                </span>
            </div>
            <div class="flex items-center text-xs text-neutral-600">
                <MaterialSymbolsLightBook5Rounded />
                <span class="ml-1 block">
                    {{ page.materi.nama_matpel }}
                </span>
            </div>
            <div class="flex items-center text-xs text-neutral-600">
                <FormkitDatetime />
                <span class="ml-1 block">
                    {{ page.materi.created_at }}
                </span>
            </div>
        </div>
        <hr class="my-3 text-neutral-300" />
        <div class="flex flex-col space-y-4">
            <div class="overflow-hidden rounded">
                <VideoPlayer class="w-[50%]" :yt-id="page.materi.youtube_id" />
            </div>
            <div>
                <h2 class="text-sm font-semibold text-blue-500">Description:</h2>
                <p v-html="page.materi.description" class="lg:prose-xs prose max-w-none text-sm" />
            </div>

            <div class="overflow-x-auto pb-4">
                <h2 class="mb-2 border-b border-neutral-300 pb-1 text-sm font-semibold">Link Materi:</h2>
                <ul class="space-y-2 text-xs text-blue-500 lg:space-y-0 lg:text-sm">
                    <li v-for="(i, num) in page.materi.file_materi" class="flex flex-col lg:flex-row lg:items-center lg:gap-1">
                        <span class="text-neutral-700">
                            {{ i.name || '#' + ((num as number) + 1) }}
                        </span>
                        <a class="transition-all hover:ring-1 lg:px-1 lg:hover:text-red-500" :href="i.links || i">
                            {{ i.links || i }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div v-else class="flex min-h-[400px] flex-col items-center justify-center bg-white p-5 text-center">
        <NotFoundVector />
        <div class="mt-3 text-sm">
            <h1>Opps! Materi tidak ditemukan atau anda tidak ada aksess</h1>
            <Link class="mt-3 block font-bold text-primary hover:text-red-400" :href="showMateri()">Kembali!</Link>
        </div>
    </div>
</template>
