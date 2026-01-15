<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

defineProps<{
    links: any[]; 
}>();
</script>

<template>
    <div v-if="links.length > 3" class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-gray-200 pt-4 sm:flex-row">
        
        <div class="text-xs text-gray-500">
            Menampilkan data halaman
        </div>

        <div class="flex items-center gap-1">
            <template v-for="(link, index) in links" :key="index">
                
                <Component
                    :is="link.url ? Link : 'span'"
                    v-if="index === 0"
                    :href="link.url"
                    class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 transition-colors"
                    :class="[
                        link.url 
                            ? 'hover:bg-gray-50 text-gray-600 cursor-pointer' 
                            : 'bg-gray-50 text-gray-300 cursor-not-allowed'
                    ]"
                >
                    <ChevronLeft class="h-4 w-4" />
                </Component>

                <Component
                    :is="link.url ? Link : 'span'"
                    v-else-if="index === links.length - 1"
                    :href="link.url"
                    class="flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 transition-colors"
                    :class="[
                        link.url 
                            ? 'hover:bg-gray-50 text-gray-600 cursor-pointer' 
                            : 'bg-gray-50 text-gray-300 cursor-not-allowed'
                    ]"
                >
                    <ChevronRight class="h-4 w-4" />
                </Component>

                <Component
                    :is="link.url ? Link : 'span'"
                    v-else
                    :href="link.url"
                    class="flex h-8 min-w-[2rem] items-center justify-center px-3 rounded-md border text-xs font-medium transition-colors"
                    :class="[
                        link.active
                            ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm' // Style Aktif
                            : link.url 
                                ? 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 cursor-pointer' // Style Link Biasa
                                : 'border-transparent text-gray-500 cursor-default' // Style untuk '...' (titik tiga)
                    ]"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>