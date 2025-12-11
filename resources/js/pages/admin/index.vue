<script setup lang="ts">
import Breadcrumb from '@/features/dashboard-admin/Breadcrumb.vue';
import DashboardStatistic from '@/features/dashboard-admin/dashboard-statistic.vue';
const breadcrumbs = [
    { label: 'Dashboard' } // Item terakhir otomatis jadi teks aktif
];
</script>

<template>
    <Breadcrumb :items="breadcrumbs"/>
    <DashboardStatistic/>
</template>