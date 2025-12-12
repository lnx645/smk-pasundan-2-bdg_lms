import AdminLayout from '@/layouts/admin-layout/layout.vue';
import Layout from '@/layouts/layout.vue';
import { clsx, type ClassValue } from 'clsx';
import dayjs from 'dayjs';
import { twMerge } from 'tailwind-merge';
import { DefineComponent } from 'vue';
export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

const define_layout = (layout: DefineComponent, name: string) => {
    if (name.includes('auth')) {
        return layout;
    } else if (name.includes('admin')) {
        layout.default.layout = AdminLayout;
    } else {
        layout.default.layout = Layout;
    }
    return layout;
};
function getYouTubeVideoId(url: string): string | null {
    const regExp = /^.*(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^#&?]{11}).*$/;
    const match = url.match(regExp);
    return match && match[1].length === 11 ? match[1] : null;
}
const formatTanggal = (date: string | null = null) => {
    if (!date) {
        return date;
    }
    const dat = new Date(date);
    const waktuIndo = dat.toLocaleString('id-ID', {
        timeZone: 'Asia/Jakarta', 
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
    return waktuIndo;
};
const checkDeadline = (date : any) => {
    return dayjs().isAfter(dayjs(date));
};
const getDeadlineStatus = (deadline: string, isOver: boolean, isDone: boolean) => {
    if (isDone) return { text: 'Selesai', class: 'bg-emerald-100 text-emerald-700 border-emerald-200' };
    if (isOver) return { text: 'Terlewat', class: 'bg-red-100 text-red-700 border-red-200' };
    const now = new Date();
    const dead = new Date(deadline);
    const diffMs = dead.getTime() - now.getTime(); // Selisih dalam milidetik

    if (diffMs <= 0) return { text: 'Waktu Habis', class: 'bg-red-100 text-red-700 border-red-200' };

    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

   
    if (diffDays > 0) {
        if (diffDays <= 3) {
            return { 
                text: `${diffDays} hari ${diffHours} jam lagi`, 
                class: 'bg-amber-100 text-amber-700 border-amber-200' 
            };
        }
        return { 
            text: `${diffDays} hari lagi`, 
            class: 'bg-blue-50 text-blue-700 border-blue-200' 
        };
    }

    if (diffHours > 0) {
        return { 
            text: `${diffHours} jam ${diffMinutes} mnt lagi`, 
            class: 'bg-orange-100 text-orange-700 border-orange-200 animate-pulse' 
        };
    }

    return { 
        text: `${diffMinutes} menit lagi`, 
        class: 'bg-rose-100 text-rose-700 border-rose-200 animate-pulse font-bold' 
    };
};
const getInitials = (name: string) => {
    return name
        ? name
              .split(' ')
              .map((n) => n[0])
              .join('')
              .substring(0, 2)
              .toUpperCase()
        : '??';
};
export {checkDeadline,getInitials,getDeadlineStatus}
export { formatTanggal, getYouTubeVideoId };
export default define_layout;
