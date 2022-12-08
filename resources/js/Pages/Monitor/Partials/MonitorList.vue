<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    monitors: Object,
});
</script>

<template>
    <div class="grid grid-cols-2 gap-4" v-for="monitor in monitors" :key="monitor.id">
        <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">
            {{ monitor.name }}
        </div>
    </div>
</template>
