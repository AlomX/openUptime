<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import { createPopper } from '@popperjs/core';

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
                <div class="flex-none shrink-0 mr-7">
                    <img class="h-12 w-12" :src="'http://www.google.com/s2/favicons?domain=' + monitor.address + '&sz=128'" :alt="monitor.address + ' Logo'">
                </div>
                <div class="flex-auto flex flex-wrap">
                    <a class="flex-none" :href="'https://' + monitor.address" target="_blank">
                        <span class="text-xl">{{ monitor.name }}</span>
                        <span class="text-slate-500"> ( {{ monitor.address }} ) </span>
                    </a>
                    <span class="flex-auto grow text-right text-slate-500">last response : </span>
                    <div class="flex-none w-full flex gap-x-0.5">
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-red-700 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-amber-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-amber-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-lime-500 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-red-700 rounded cursor-help">&nbsp;</div>
                        <div class="flex-auto bg-red-700 rounded cursor-help">&nbsp;</div>
                    </div>
                </div>

        </div>
    </div>
</template>