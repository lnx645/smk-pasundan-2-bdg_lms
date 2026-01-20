<script setup lang="ts">
import { cn } from '@/lib/utils';
import { defineEmits, defineProps } from 'vue';

interface Props {
    class?: string | object;
    modelValue?: string;
    id?: string;
    type?: string;
    placeholder?: string;
    error?: boolean;
}

const { class: className = '', modelValue = '', id = '', type = 'text', placeholder = '', error = false } = defineProps<Props>();

const emit = defineEmits(['update:modelValue']);

const updateValue = (e: Event) => {
    emit('update:modelValue', (e.target as HTMLInputElement).value);
};
</script>

<template>
    <input
        :id="id"
        :type="type"
        :placeholder="placeholder"
        :value="modelValue"
        @input="updateValue"
        :class="
            cn(
                'w-full rounded-xl border border-gray-200 bg-gray-50 px-5 py-3 text-gray-700 placeholder-gray-400 transition-all focus:border-blue-900 focus:bg-white focus:ring-2 focus:ring-blue-900/20 focus:outline-none',
                className,
                error && 'bg-red-50 ring-2 ring-red-500',
            )
        "
    />
</template>
