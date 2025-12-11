<script setup lang="ts">
import { ref } from 'vue';
import { onClickOutside } from '@vueuse/core';
import { Link } from '@inertiajs/vue3'; // Sesuaikan dengan router Anda (misal vue-router)
import { 
    User, 
    Settings, 
    LogOut, 
    ChevronDown 
} from 'lucide-vue-next';
import Avatar from '@/components/avatar.vue';

const isDropdownOpen = ref(false);
const dropdownRef = ref(null); 

onClickOutside(dropdownRef, () => {
    isDropdownOpen.value = false;
});

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const user = {
    name: 'Dimas Adtya',
    email: 'dimas@example.com',
    initial: 'D'
};
</script>

<template>
    <div class="ml-auto" ref="dropdownRef">
        <div class="relative">
            <button 
                @click="toggleDropdown"
                class="flex items-center gap-2 rounded-full bg-white pl-1 pr-2 py-1 border border-transparent hover:border-gray-200 hover:bg-gray-50 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-100 group"
                :class="{ 'bg-gray-50 border-gray-200': isDropdownOpen }"
            >
                <Avatar :fallback="user.initial" class="h-8 w-8 transition-transform group-hover:scale-105" />
                
                <span class="hidden md:block text-sm font-medium text-gray-700">{{ user.name }}</span>

                <ChevronDown 
                    :size="16" 
                    class="text-gray-400 transition-transform duration-300"
                    :class="{ 'rotate-180': isDropdownOpen }"
                />
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
                    class="absolute right-0 mt-2 w-64 origin-top-right rounded-xl bg-white p-2 shadow-xl ring-1 ring-black/5 focus:outline-none z-50"
                >
                    <div class="px-3 py-3 border-b border-gray-100 mb-1">
                        <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                    </div>

                    <div class="py-1">
                        <Link href="/profile" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                            <User :size="18" />
                            Profile Saya
                        </Link>
                        <Link href="/settings" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                            <Settings :size="18" />
                            Pengaturan Akun
                        </Link>
                    </div>

                    <div class="my-1 h-px bg-gray-100"></div>

                    <div class="py-1">
                        <Link href="/logout" method="post" as="button" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <LogOut :size="18" />
                            Keluar
                        </Link>
                    </div>
                </div>
            </Transition>
        </div>
    </div>
</template>