<script setup lang="ts">
import { BookOpen, FileText, LayoutDashboard, MonitorPlay, Users } from 'lucide-vue-next';
import SidebarMenu from './sidebar-menu.vue';

// Contoh logika untuk menentukan menu aktif (biasanya pakai usePage().url di Inertia)
const currentPath = '/dashboard'; // Ganti logika ini sesuai kebutuhan routing Anda

const menus = [
    { label: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
    { label: 'Management User', href: '/users', icon: Users },
    { label: 'Cetak Laporan', href: '/reports', icon: FileText },
    { label: 'Manajemen Akademik', href: '/academic', icon: BookOpen },
    { label: 'Manajemen Kelas', href: '/classes', icon: MonitorPlay },
];
</script>

<template>
    <aside class="flex min-h-[calc(100vh-50px)] max-h-[calc(100vh-50px)] flex-col border-r border-neutral-200 bg-[#F8F9FA] transition-all">
        <div class="flex pt-4 items-center px-6 font-bold text-neutral-800">App Name</div>
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
