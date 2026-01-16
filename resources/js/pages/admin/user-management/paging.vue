<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

defineProps<{
    links: any[];
}>();
</script>

<template>
    <div v-if="links.length > 3" class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-gray-200 pt-4 sm:flex-row">
        <div class="text-xs text-gray-500">Menampilkan data halaman</div>

        <div class="flex items-center gap-1">
            <template v-for="(link, index) in links" :key="index">
                <Component
                    :is="link.url ? Link : 'span'"
                    v-if="index === 0"
                    :href="link.url"
                    class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 transition-colors"
                    :class="[link.url ? 'cursor-pointer text-gray-600 hover:bg-gray-50' : 'cursor-not-allowed bg-gray-50 text-gray-300']"
                >
                    <ChevronLeft class="h-4 w-4" />
                </Component>

                <Component
                    :is="link.url ? Link : 'span'"
                    v-else-if="index === links.length - 1"
                    :href="link.url"
                    class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 transition-colors"
                    :class="[link.url ? 'cursor-pointer text-gray-600 hover:bg-gray-50' : 'cursor-not-allowed bg-gray-50 text-gray-300']"
                >
                    <ChevronRight class="h-4 w-4" />
                </Component>

                <Component
                    :is="link.url ? Link : 'span'"
                    v-else
                    :href="link.url"
                    class="flex h-8 min-w-[2rem] items-center justify-center rounded-md border px-3 text-xs font-medium transition-colors"
                    :class="[
<<<<<<< HEAD
                        link.active ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm' : 'border-gray-200 bg-white text-gray-600',
                        !link.url ? 'cursor-default' : 'hover:bg-gray-50', // Tambahan style untuk separator
=======
                        link.active
                            ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm' // Style Aktif
                            : link.url 
                                ? 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 cursor-pointer' // Style Link Biasa
                                : 'border-transparent text-gray-500 cursor-default' // Style untuk '...' (titik tiga)
>>>>>>> 76946a030ba0d6b6fc35535b3510527be5e4305a
                    ]"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
