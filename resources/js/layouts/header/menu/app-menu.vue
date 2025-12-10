<script setup lang="ts">
import { useNavigationMenu } from '@/features/store/navStore';
import CiLogOut from '@/icons/CiLogOut.vue';
import MingcuteHome5Line from '@/icons/MingcuteHome5Line.vue';
import { home } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MenuGuru from './menu-guru.vue';
import MenuItem from './menu-item.vue';
import MenuSiswa from './menu-siswa.vue';
import { logout } from '@/actions/App/Http/Controllers/LoginController';
const role = computed(() => {
    return usePage().props.auth.user.role;
});
const { toggleMenu } = useNavigationMenu();

</script>

<template>
    <div class="container mx-auto h-nav list-none px-3 lg:px-0">
        <div class="flex h-nav items-center">
            <MenuItem label="Home" :href="home()" :icon="MingcuteHome5Line" />
            <MenuSiswa v-if="role === 'siswa'" />
            <MenuGuru v-else-if="role === 'guru'" />
            <div class="ml-auto hidden lg:block">
                <MenuItem label="Logout" class="text-red-500" :href="logout()" :icon="CiLogOut" />
            </div>
            <div class="ml-auto lg:hidden">
                <button class="cursor-pointer" @click="toggleMenu">
                    <CiLogOut class="block text-xl text-red-700 active:text-red-800 lg:hidden" />
                </button>
                <div class="hidden h-8 w-8 items-center justify-center rounded-full bg-neutral-200 text-xs font-semibold text-neutral-700">D</div>
            </div>
        </div>
    </div>
</template>
