<script setup lang="ts">
import { logout } from '@/actions/App/Http/Controllers/LoginController';
import Avatar from '@/components/avatar.vue';
import { getInitials } from '@/lib/utils';
import { Link, usePage } from '@inertiajs/vue3'; // Sesuaikan dengan router Anda (misal vue-router)
import { onClickOutside } from '@vueuse/core';
import { ChevronDown, LogOut } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const isDropdownOpen = ref(false);
const dropdownRef = ref(null);

onClickOutside(dropdownRef, () => {
    isDropdownOpen.value = false;
});

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const page = usePage();
const user = computed(() => {
    const data = page.props.auth.user;
    return {
        ...data,
        initial: getInitials(data.name),
    };
});
</script>

<template>
    <div class="ml-auto" ref="dropdownRef">
        <div class="relative">
            <button
                @click="toggleDropdown"
                class="group flex items-center gap-2 rounded-full border border-transparent bg-white py-1 pr-2 pl-1 transition-all hover:border-gray-200 hover:bg-gray-50 focus:ring-2 focus:ring-indigo-100 focus:outline-none"
                :class="{ 'border-gray-200 bg-gray-50': isDropdownOpen }"
            >
                <Avatar :fallback="user.initial" class="h-8 w-8 transition-transform group-hover:scale-105" />

                <span class="hidden text-sm font-medium text-gray-700 md:block">{{ user.name }}</span>

                <ChevronDown :size="16" class="text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': isDropdownOpen }" />
            </button>

            <Transition
                enter-active-class="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
                enter-from-class="transform opacity-0 scale-95 -translate-y-2"
                enter-to-class="transform opacity-100 scale-100 translate-y-0"
                leave-active-class="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-200"
                leave-from-class="transform opacity-100 scale-100 translate-y-0"
                leave-to-class="transform opacity-0 scale-95 -translate-y-2"
            >
                <div
                    v-show="isDropdownOpen"
                    class="absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-xl bg-white p-2 shadow-xl ring-1 ring-black/5 focus:outline-none"
                >
                    <div class="mb-1 border-b border-gray-100 px-3 py-3">
                        <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ user.email }}</p>
                    </div>

                    <!-- <div class="py-1">
                        <Link
                            :href="ProfileController()"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                        >
                            <User :size="18" />
                            Profile Saya
                        </Link>
                        <Link
                            href="/settings"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                        >
                            <Settings :size="18" />
                            Pengaturan Akun
                        </Link>
                    </div> -->

                    <div class="my-1 h-px bg-gray-100"></div>

                    <div class="py-1">
                        <Link
                            :href="logout()"
                            method="post"
                            as="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-red-600 transition-colors hover:bg-red-50"
                        >
                            <LogOut :size="18" />
                            Keluar
                        </Link>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>
