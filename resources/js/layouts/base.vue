<script setup lang="ts">
import { saveFcmToken } from '@/actions/App/Http/Controllers/NotifServiceController';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from 'axios';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, isSupported, onMessage } from 'firebase/messaging'; // Tambah isSupported
import 'sweetalert2/dist/sweetalert2.css';
import { onMounted } from 'vue';
import { ModalsContainer } from 'vue-final-modal';
import 'vue-final-modal/style.css';
import 'vue-select/dist/vue-select.css';
import { toast, Toaster } from 'vue-sonner';
import 'vue-sonner/style.css';
// Firebase config
const firebaseConfig = {
    apiKey: 'AIzaSyBC8OjU6vxFqVHzMsY6Pt3iwFOuUEIhc-E',
    authDomain: 'apiyt-332805.firebaseapp.com',
    projectId: 'apiyt-332805',
    storageBucket: 'apiyt-332805.firebasestorage.app',
    messagingSenderId: '898047629674',
    appId: '1:898047629674:web:6703f1ccad4f02c86ded13',
};

let messaging: any = null;

const initFirebase = async () => {
    try {
        const supported = await isSupported();
        if (supported) {
            const app = initializeApp(firebaseConfig);
            messaging = getMessaging(app);

            setupMessageListener();
        } else {
            console.warn('Firebase Messaging not supported in this browser.');
        }
    } catch (e) {
        console.error('Firebase Initialization Error:', e);
    }
};

async function requestPermission() {
    if (!messaging) return;

    if (!('Notification' in window)) {
        return toast.error('Browser tidak mendukung Notifications');
    }

    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            await getFcmToken();
        } else {
            console.log('Notifikasi permission ditolak/default');
        }
    } catch (err) {
        console.error('Error requesting permission:', err);
    }
}

async function getFcmToken() {
    if (!messaging) return;

    try {
        if (!navigator.serviceWorker) {
            console.error('Service Worker tidak didukung browser ini');
            return;
        }

        const registration = await navigator.serviceWorker.ready;

        const oldToken = localStorage.getItem('fcm_token');
        if (oldToken) {
            return;
        }

        const token = await getToken(messaging, {
            vapidKey: 'BJsJW68L2SBlEQ7OiZprKb8GDx0f9Pz86ad19HyV-uSUnyH30HaKN-pMPbsKrU55O458LXO8vxY1EiU8DhBBtPI',
            serviceWorkerRegistration: registration,
        });

        if (!token) {
            // toast.error('Token kosong'); // Opsional: jangan terlalu spam user
            console.warn('FCM Token kosong');
            return;
        }

        await axios.post(saveFcmToken().url, { token });

        localStorage.setItem('fcm_token', token);
        console.log('FCM Token Saved');
    } catch (err) {
        console.error('FCM Get Token Error:', err);
    }
}

function setupMessageListener() {
    if (messaging) {
        try {
            onMessage(messaging, (payload) => {
                console.log('Foreground message:', payload);
                if (payload.notification) {
                    toast.success(`${payload.notification.title} - ${payload.notification.body}`);
                }
            });
        } catch (e) {
            console.error('Gagal setup onMessage listener:', e);
        }
    }
}

onMounted(async () => {
    await initFirebase();

    try {
        if ('serviceWorker' in navigator) {
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
                scope: '/',
            });
            console.log('SW registered:', registration);

            await requestPermission();
        }
    } catch (e) {
        console.error('Service Worker Registration Failed:', e);
    }
});
</script>

<template>
    <slot />
    <ModalsContainer />
    <Toaster :rich-colors="true" />
</template>
<style lang="css">
body {
    background: #f4f4f4 !important;
}
</style>
