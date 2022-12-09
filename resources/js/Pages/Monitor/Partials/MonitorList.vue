<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    monitors: {
        type: Object,
    }
});

onMounted(() => {
    console.log(props.monitors);
});
</script>

<template>
    <div class="grid grid-cols-2 gap-4 pt-6">
        <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 dark:text-slate-200 shadow sm:rounded-lg" 
                v-for="monitor in props.monitors" :key="monitor.id">
            {{ monitor.address }} {{ monitor.name }}
        </div>
    </div>
</template>
