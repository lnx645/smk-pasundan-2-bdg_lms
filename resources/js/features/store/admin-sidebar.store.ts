import { defineStore } from 'pinia';

export const useAdminSidebarStore = defineStore('admin-sidebar.store', {
    state() {
        return {
            isOpen: false,
        };
    },
    actions: {
        setOpen(open: boolean) {
            this.isOpen = open;
        },
    },
});
