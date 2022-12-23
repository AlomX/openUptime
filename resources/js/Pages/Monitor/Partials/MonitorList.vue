<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import moment from 'moment';

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
    <div class="grid sm:grid-cols-2 gap-4 pt-6">
        <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 dark:text-slate-200 shadow sm:rounded-lg grid grid-cols-7 auto-cols-max items-center overflow-hidden" 
            v-for="monitor in props.monitors" :key="monitor.id">
                <img class="sm:block hidden h-auto w-12" :src="'http://www.google.com/s2/favicons?domain=' + monitor.address + '&sz=128'" :alt="monitor.address + ' Logo'">
                <div class="flex flex-wrap sm:col-start-2 col-start-1 col-end-8">
                    <a class="sm:flex-none flex-wrap" :href="'https://' + monitor.address" target="_blank">
                        <span class="text-xl">{{ monitor.name }}</span>
                        <span class="text-slate-500"> ( {{ monitor.address }} ) </span>
                    </a>
                    <span class="flex-auto grow text-right text-slate-500 shrink pt-1">
                        last response : 
                        {{ moment(String(monitor.pings[monitor.pings.length - 1].created_at)).format('hh:mm:ss') }}
                    </span>
                    <div class="flex-auto overflow-hidden left-0 right-0">
                        <div class="object-fill max-w-full flex gap-x-0.5 flex-nowrap flex-row-reverse">
                            <div class="flex-auto" v-for="ping in monitor.pings.slice().reverse()" :key="ping.created_at">
                                <div class="rounded cursor-help" 
                                    v-bind:class="
                                        (ping.response_code == 200)?'bg-lime-500':'bg-red-700'
                                    "
                                    v-bind:name="'Code : ' + ping.response_code + ' Time : ' + ping.response_time">
                                    &nbsp;
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
    </div>
</template>