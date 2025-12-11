<script setup lang="ts">
import FamiconsPeopleSharp from '@/icons/FamiconsPeopleSharp.vue';
import IcBaselineClass from '@/icons/IcBaselineClass.vue';
import PhChalkboardTeacher from '@/icons/PhChalkboardTeacher.vue';
import { 
    Contact, 
    Users, 
    BookMarked 
} from 'lucide-vue-next';

// Data statistik tetap sama
const stats = [
    { 
        label: 'Guru', 
        status: 'Aktif', 
        count: 70, 
        icon: PhChalkboardTeacher, 
        iconColor: 'text-orange-500' 
    },
    { 
        label: 'Siswa', 
        status: 'Aktif', 
        count: 1900, 
        icon: FamiconsPeopleSharp, 
        iconColor: 'text-green-600' 
    },
    { 
        label: 'Kelas', 
        status: 'Aktif', 
        count: 45, 
        icon: IcBaselineClass, 
        iconColor: 'text-blue-600' 
    },
];
</script>

<template>
    <div class="flex-1 px-4 mt-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div 
                v-for="(stat, index) in stats" 
                :key="index"
                class="rounded-xl bg-gray-50/80 p-5 shadow-sm hover:shadow-md transition-shadow duration-300"
            >
                <div class="mb-4 flex items-center gap-2">
                    <span class="text-base font-bold text-indigo-950">
                        {{ stat.label }}
                    </span>
                    <div class="h-3 w-[1px] bg-gray-300"></div>
                    <span class="text-xs font-medium text-gray-400">
                        {{ stat.status }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-center">
                        <component 
                            :is="stat.icon" 
                            :class="[stat.iconColor, 'h-[50px] w-[50px] stroke-[1.5]']" 
                        />
                    </div>
                    
                    <span class="text-3xl font-bold text-indigo-950 tracking-tight">
                        {{ stat.count }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>