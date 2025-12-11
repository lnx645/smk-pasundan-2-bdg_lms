<script setup lang="ts">
import Avatar from '@/components/avatar.vue';
import { useAdminSidebarStore } from '@/features/store/admin-sidebar.store';
import IcOutlineMenu from '@/icons/IcOutlineMenu.vue';
import { useMediaQuery, useToggle } from '@vueuse/core';
import { onMounted, watch } from 'vue';
import Logo from '../../../img/logo.png';
import UserAccountDropdown from './user-account-dropdown.vue';
const [value, toggle] = useToggle();
const store = useAdminSidebarStore();

const mk = useMediaQuery('(min-width: 1024px)');
onMounted(() => {
    if (mk.value && !store.isOpen) {
        store.setOpen(true);
    }
});
watch(mk, () => {
    if (mk.value) {
        store.setOpen(true);
    } else {
        store.setOpen(false);
    }
});
watch(value, (toggle) => {
    if (mk.value) {
        return store.setOpen(true);
    } else if (!store.isOpen && !mk.value) {
        store.setOpen(true);
    } else {
        store.setOpen(false);
    }
});
</script>

<template>
    <header class="sticky top-0 z-50 h-[50px] bg-[#F7F7F7] shadow-md">
        <div class="container mx-auto px-3">
            <div class="flex h-[50px] items-center">
                <div class="mr-auto flex items-center">
                    <img class="mr-1 aspect-square w-8 lg:mr-3" :src="Logo" alt="" />
                    <button v-if="!mk" id="sidebar-trigger" @click="toggle()" class="cursor-pointer text-2xl text-black">
                        <IcOutlineMenu />
                    </button>
                    <div class="thruncate text-sm" v-else>SMK PASUNDAN 2 BDG</div>
                </div>
                <UserAccountDropdown/>
            </div>
        </div>
    </header>
</template>
