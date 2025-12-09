<template>
    <div class="p-4 rounded-lg bg-neutral-50 border border-neutral-200">
        <h3 class="font-semibold text-neutral-600 mb-1 text-[13px]">
            {{ label }}
        </h3>

        <p class="text-neutral-900 text-[14px]">
            {{ value }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    label: String,
    value: [String, Number]
});
</script>
