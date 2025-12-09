import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';

export default function useShowErrorToast(form: any = null) {
    if (!form) {
        const page = usePage();

        if (page.props && page.props.errors) {
            if (!(page.props.errors as any).success) {
                // Ambil error pertama secara otomatis
                const first = Object.values(page.props.errors)[0];
                if (first) toast.error(first as string);
            } else if ((page.props.errors as any).success) {
                toast.success((page.props.errors as any).success);
            }
        }
    } else {
        watch(
            () => form.errors,
            () => {
                if (form.hasErrors && !(form.errors as any).success) {
                    toast.error('Gagal!Tolong periksa inputan anda lagi!');
                } else if ((form.errors as any).success) {
                    toast.success((form.errors as any).success);
                    form.resetAndClearErrors();
                    form.reset();
                }
            },
        );
    }
}
