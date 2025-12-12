<script setup lang="ts">
import { cn } from '@/lib/utils';
import { defineEmits, defineProps } from 'vue';

interface Props {
  class?: string | object 
  modelValue?: string
  id?: string
  type?: string 
  placeholder?: string
  error?: boolean
}

const {
  class: className = '',
  modelValue = '',
  id = '',
  type = 'text',
  placeholder = '',
  error = false
} = defineProps<Props>();

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
                'w-full rounded-lg border border-neutral-200 bg-neutral-50 px-3 py-2 text-sm transition-all outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100',
                className,
                error && 'ring-2 ring-red-500',
            )
        "
    />
</template>


