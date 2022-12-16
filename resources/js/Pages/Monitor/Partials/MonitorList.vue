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
        <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 dark:text-slate-200 shadow sm:rounded-lg flex items-center" 
            v-for="monitor in props.monitors" :key="monitor.id">
                <div class="shrink-0 mr-7">
                    <img class="h-12 w-12" :src="'http://www.google.com/s2/favicons?domain=' + monitor.address + '&sz=128'" :alt="monitor.address + ' Logo'">
                </div>
                <div>
                    <div class="text-xl">{{ monitor.name }} <span class="text-base text-slate-500"> ( {{ monitor.address }} ) </span></div>
                    <p class="text-slate-500">{{ monitor.address }}</p>
                </div>
        </div>
    </div>
</template>
