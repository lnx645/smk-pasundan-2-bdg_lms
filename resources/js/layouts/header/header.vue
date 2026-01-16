<template>
    <header
        style="background-image: url('https://edlink.id/assets/img/base.png')"
        class="z-40 h-24 bg-primary bg-cover bg-left bg-no-repeat text-primary-foreground lg:h-header"
    >
        <div class="container mx-auto flex h-24 items-center justify-between px-4 lg:px-0">
            <Logo />
            <div ref="profileRef" class="relative z-50 flex items-center justify-center gap-x-6">
                <div class="flex items-center gap-4">
                    <Link
                        :href="NotificationController.index()"
                        class="relative flex items-center justify-center rounded-full p-2 text-gray-600 transition-colors"
                    >
                        <BellIcon class="h-6 w-6" color="white" />

                        <span
                            v-if="notif_count"
                            class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white"
                        >
                            {{ notif_count }}
                        </span>
                    </Link>
                </div>
                <button
                    @click="toggleProfile"
                    class="flex items-center justify-center rounded-full bg-white transition-transform focus:ring-2 focus:ring-white/20 focus:outline-none active:scale-95"
                >
                    <Avatar
                        :avatar_uri="($page.props.auth.user.siswa as any)?.pas_photo"
                        :fallback="$page.props.auth.user.name"
                        class="h-10 w-10 border-2 border-white/20 shadow-sm"
                    />
                </button>

                <AnimatePresence>
                    <motion.div
                        v-if="profileOpen"
                        :initial="{ opacity: 0, scale: 0.95, y: 10 }"
                        :animate="{ opacity: 1, scale: 1, y: 0 }"
                        :exit="{ opacity: 0, scale: 0.95, y: 10 }"
                        :transition="{ duration: 0.15 }"
                        class="absolute top-full right-0 mt-3 w-64 origin-top-right rounded-xl border border-neutral-100 bg-white p-2 shadow-xl ring-1 ring-black/5"
                    >
                        <div class="mb-2 px-3 py-2">
                            <p class="truncate text-sm font-bold text-gray-900">
                                {{ $page.props.auth.user.name }}
                            </p>
                            <p class="truncate text-xs text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </p>
                        </div>

                        <hr class="border-gray-100" />

                        <div class="flex flex-col gap-1 py-2">
                            <Link
                                :href="ProfileController()"
                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-primary"
                            >
                                <UserIcon class="h-4 w-4" />
                                Profil Saya
                            </Link>

                            <Link
                                :href="SiswaSecurityController()"
                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 hover:text-primary"
                            >
                                <ShieldCheckIcon class="h-4 w-4" />
                                Keamanan
                            </Link>
                        </div>

                        <hr class="border-gray-100" />

                        <div class="py-2">
                            <Link
                                href="/logout"
                                method="get"
                                as="button"
                                class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
                            >
                                <ArrowRightOnRectangleIcon class="h-4 w-4" />
                                Keluar
                            </Link>
                        </div>
                    </motion.div>
                </AnimatePresence>
            </div>
        </div>
    </header>

<<<<<<< HEAD
    <!-- DROPDOWN DIPISAH DI LUAR HEADER supaya tidak ketiban -->
    <div ref="profileRef" class="relative w-full">
        <AnimatePresence>
            <motion.div
                v-if="profileOpen"
                :initial="{ opacity: 0, y: -10 }"
                :animate="{ opacity: 1, y: 0 }"
                :exit="{ opacity: 0, y: -10 }"
                :transition="{ duration: 0.15 }"
                class="absolute top-[-80px] right-4 z-[9999] w-56 rounded-lg border border-neutral-200 bg-white p-4 shadow-lg"
            >
                <div class="flex flex-col">
                    <p class="text-sm font-semibold">
                        {{ $page.props.auth.user.name }}
                    </p>
                    <p class="line-clamp-1 text-xs text-neutral-500">
                        {{ $page.props.auth.user.email }}
                    </p>
                    <hr class="my-3 text-neutral-100" />

                    <Link :href="ProfileController()" class="py-1 text-sm hover:text-primary"> Profil Saya </Link>

                    <Link :href="logout()" method="post" class="py-1 text-sm text-red-600 hover:text-red-700"> Logout </Link>
                </div>
            </motion.div>
        </AnimatePresence>
    </div>

    <nav class="sticky top-0 z-50 bg-white shadow">
=======
    <nav class="sticky top-0 z-30 bg-white shadow-sm">
>>>>>>> 76946a030ba0d6b6fc35535b3510527be5e4305a
        <AppMenu />
    </nav>
</template>

<script setup lang="ts">
import { logout } from '@/actions/App/Http/Controllers/LoginController';
import ProfileController from '@/actions/App/Http/Controllers/ProfileController';
import SiswaSecurityController from '@/actions/App/Http/Controllers/SiswaSecurityController';
import Avatar from '@/components/avatar.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { AnimatePresence, motion } from 'motion-v';
import { computed, ref } from 'vue';
import Logo from './logo.vue';
import AppMenu from './menu/app-menu.vue';
const page = usePage();

import NotificationController from '@/actions/App/Http/Controllers/NotificationController';
import { ShieldCheckIcon, UserIcon } from '@heroicons/vue/24/outline';
import { BellIcon } from 'lucide-vue-next';
const notif_count = computed(() => page.props.notif_count);
const profileOpen = ref(false);
const profileRef = ref(null);

const toggleProfile = () => {
    profileOpen.value = !profileOpen.value;
};

// Tutup dropdown jika klik di luar area profileRef (wrapper tombol & dropdown)
onClickOutside(profileRef, () => {
    profileOpen.value = false;
});
</script>
