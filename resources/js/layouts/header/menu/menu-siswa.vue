<script setup lang="ts">
import { showMateri } from '@/actions/App/Http/Controllers/MateriController';
import QuizSiswaController from '@/actions/App/Http/Controllers/QuizSiswaController';
import { showTugas } from '@/actions/App/Http/Controllers/TugasSiswaController';
import HugeiconsTaskDaily01 from '@/icons/HugeiconsTaskDaily01.vue';
import MaterialSymbolsMarkdownPaste from '@/icons/MaterialSymbolsMarkdownPaste.vue';
import MaterialSymbolsMenuBookOutlineRounded from '@/icons/MaterialSymbolsMenuBookOutlineRounded.vue';
import SolarSquareAcademicCap2Bold from '@/icons/SolarSquareAcademicCap2Bold.vue';
import MenuItem from './menu-item.vue';
function redirectToAbsensi() {
    return 'SistemAbsensi.php';
}
</script>

<template>
    <MenuItem label="Akademik" href="/" :has-dropdown="true" :icon="SolarSquareAcademicCap2Bold">
        <MenuItem label="Materi" :href="showMateri()" :icon="MaterialSymbolsMenuBookOutlineRounded" />
        <MenuItem label="Tugas" :href="showTugas()" :icon="HugeiconsTaskDaily01" />
        <MenuItem label="Quiz" :href="QuizSiswaController.index().url" :icon="MaterialSymbolsMarkdownPaste" />
    </MenuItem>
</template>
