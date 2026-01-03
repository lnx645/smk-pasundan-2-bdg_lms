<script setup>
import ProfileController from '@/actions/App/Http/Controllers/ProfileController';
import Input from '@/components/input.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
});

// --- FORM 1: INFO PROFILE (Name, Email, Photo) ---
const formInfo = useForm({
    _method: 'PATCH', // Spoofing method agar bisa upload file
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const photoPreview = ref(null);
const photoInput = ref(null);

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    formInfo.photo = photo;
    const reader = new FileReader();
    reader.onload = (e) => (photoPreview.value = e.target.result);
    reader.readAsDataURL(photo);
};

const updateProfileInformation = () => {
    formInfo.post(ProfileController.update().url, {
        preserveScroll: true,
        onSuccess: () => (formInfo.photo = null),
    });
};

// --- FORM 2: PASSWORD ---
const formPassword = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    formPassword.put(ProfileController.updatePassword().url, {
        preserveScroll: true,
        onSuccess: () => formPassword.reset(),
        onError: () => {
            if (formPassword.errors.password) {
                formPassword.reset('password', 'password_confirmation');
            }
            if (formPassword.errors.current_password) {
                formPassword.reset('current_password');
            }
        },
    });
};

// Helper URL Foto
const getUserPhoto = () => {
    if (photoPreview.value) return photoPreview.value;
    return props.user.siswa?.pas_photo
        ? `/storage/${props.user.siswa.pas_photo}`
        : `https://ui-avatars.com/api/?name=${props.user.name}&background=EBF4FF&color=7F9CF5`;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <h2 class="mb-8 text-2xl leading-tight font-bold text-gray-800">Profile Settings</h2>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="sticky top-8 rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm">
                        <div class="relative mb-4 inline-block">
                            <img :src="getUserPhoto()" class="mx-auto h-32 w-32 rounded-full border-4 border-white object-cover shadow-lg" />
                            <button
                                @click="$refs.photoInput.click()"
                                class="absolute right-0 bottom-0 rounded-full bg-gray-800 p-2 text-white shadow transition hover:bg-gray-700"
                                title="Change Photo"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                                    />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900">{{ user.name }}</h3>
                        <p class="mb-4 text-sm text-gray-500">{{ user.email }}</p>

                        <input ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview" />
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                        <header class="mb-6">
                            <h2 class="text-lg font-bold text-gray-900">General Information</h2>
                            <p class="text-sm text-gray-500">Update your account's profile information.</p>
                        </header>

                        <form @submit.prevent="updateProfileInformation" class="space-y-6">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Full Name</label>
                                <Input v-model="formInfo.name" type="text" />
                                <p v-if="formInfo.errors.name" class="mt-1 text-sm text-red-500">{{ formInfo.errors.name }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Email Address</label>
                                <Input v-model="formInfo.email" type="email" />
                                <p v-if="formInfo.errors.email" class="mt-1 text-sm text-red-500">{{ formInfo.errors.email }}</p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button
                                    :disabled="formInfo.processing"
                                    type="submit"
                                    class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 focus:ring-4 focus:ring-gray-200 disabled:opacity-50"
                                >
                                    Save Information
                                </button>
                                <span v-if="formInfo.recentlySuccessful" class="animate-pulse text-sm text-green-600">Saved!</span>
                            </div>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                        <header class="mb-6">
                            <h2 class="text-lg font-bold text-gray-900">Security</h2>
                            <p class="text-sm text-gray-500">Ensure your account is using a long, random password to stay secure.</p>
                        </header>

                        <form @submit.prevent="updatePassword" class="space-y-6">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Current Password</label>
                                <Input v-model="formPassword.current_password" type="password" autocomplete="current-password" />
                                <p v-if="formPassword.errors.current_password" class="mt-1 text-sm text-red-500">
                                    {{ formPassword.errors.current_password }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">New Password</label>
                                <Input v-model="formPassword.password" type="password" autocomplete="new-password" />
                                <p v-if="formPassword.errors.password" class="mt-1 text-sm text-red-500">{{ formPassword.errors.password }}</p>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Confirm Password</label>
                                <Input v-model="formPassword.password_confirmation" type="password" autocomplete="new-password" />
                                <p v-if="formPassword.errors.password_confirmation" class="mt-1 text-sm text-red-500">
                                    {{ formPassword.errors.password_confirmation }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <button
                                    :disabled="formPassword.processing"
                                    type="submit"
                                    class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-red-700 focus:ring-4 focus:ring-red-100 disabled:opacity-50"
                                >
                                    Update Password
                                </button>
                                <span v-if="formPassword.recentlySuccessful" class="animate-pulse text-sm text-green-600">Password Updated!</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
