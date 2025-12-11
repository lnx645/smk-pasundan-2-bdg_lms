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
    return dayjs(date).format('DD MMM YYYY HH:mm');
};
export { formatTanggal, getYouTubeVideoId };
export default define_layout;
