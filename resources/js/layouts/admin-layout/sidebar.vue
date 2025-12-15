<script setup lang="ts">
import { create } from '@/actions/App/Http/Controllers/Admin/AkademikController';
import CetakLaporanController from '@/actions/App/Http/Controllers/Admin/CetakLaporanController';
import HomeController from '@/actions/App/Http/Controllers/Admin/HomeController';
import ManagementKelasController from '@/actions/App/Http/Controllers/Admin/ManagementKelasController';
import UserManagementController from '@/actions/App/Http/Controllers/Admin/UserManagementController';
import { usePage } from '@inertiajs/vue3';
import { BookOpen, FileText, LayoutDashboard, MonitorPlay, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import SidebarMenu from './sidebar-menu.vue';
const page = usePage();
// Contoh logika untuk menentukan menu aktif (biasanya pakai usePage().url di Inertia)
const currentPath = computed(() => page.url);

const menus = [
    { label: 'Dashboard', href: HomeController.index().url, icon: LayoutDashboard },
    { label: 'Management User', href: UserManagementController.index().url, icon: Users },
    { label: 'Cetak Laporan', href: CetakLaporanController().url, icon: FileText },
    { label: 'Manajemen Akademik', href: create().url, icon: BookOpen },
    { label: 'Manajemen Kelas', href: ManagementKelasController.index().url, icon: MonitorPlay },
];
</script>

<template>
    <aside
        class="sticky top-[50px] flex max-h-[calc(100vh-50px)] min-h-[calc(100vh-50px)] flex-col border-r border-neutral-200 bg-[#F8F9FA] transition-all"
    >
        <div class="flex items-center px-6 pt-4 font-bold text-neutral-800">App Name</div>
        <div class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            <SidebarMenu v-for="(menu, index) in menus" :key="index" :href="menu.href" :active="currentPath === menu.href">
                <template #icon>
                    <component :is="menu.icon" :size="20" />
                </template>
                {{ menu.label }}
            </SidebarMenu>
        </div>
        <div class="border-t border-neutral-200 p-4 text-xs text-neutral-400">© 2024 Your Company</div>
    </aside>
</template>
