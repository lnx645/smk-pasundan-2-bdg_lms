<template>
    <header
        style="background-image: url('https://edlink.id/assets/img/base.png')"
        class="z-40 h-24 bg-primary bg-cover bg-left bg-no-repeat text-primary-foreground lg:h-header"
    >
        <div class="container mx-auto flex h-24 items-center justify-between px-2 lg:px-0">
            <Logo />

            <!-- hanya avatar di header -->
            <button @click="toggleProfile" class="relative z-50">
                <Avatar :avatar_uri="($page.props.auth.user.siswa as any)?.pas_photo" :fallback="$page.props.auth.user.name" />
            </button>
        </div>
    </header>

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
        <AppMenu />
    </nav>
</template>

<script setup lang="ts">
import { logout } from '@/actions/App/Http/Controllers/LoginController';
import ProfileController from '@/actions/App/Http/Controllers/ProfileController';
import Avatar from '@/components/avatar.vue';
import { Link } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { AnimatePresence, motion } from 'motion-v';
import { ref } from 'vue';
import Logo from './logo.vue';
import AppMenu from './menu/app-menu.vue';
const profileOpen = ref(false);
const profileRef = ref(null);

const toggleProfile = () => {
    profileOpen.value = !profileOpen.value;
};

onClickOutside(profileRef, () => {
    profileOpen.value = false;
});
</script>
