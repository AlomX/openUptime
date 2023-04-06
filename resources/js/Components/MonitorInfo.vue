<script setup>
import { onMounted, ref } from 'vue';
import moment from 'moment';

const props = defineProps({
    monitor: {
        type: Object,
    }
});

let pings = ref([]);

onMounted(() => {
    setInterval(() => {
        loadLatestPings();
    }, 10000);
});

const loadLatestPings = async () => {
    await axios
        .get(route('monitors.latestPings', props.monitor.id))
        .then((response) => {
            pings.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>

<template>
    <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 dark:text-slate-200 shadow sm:rounded-lg grid grid-cols-7 auto-cols-max items-center overflow-hidden">
        {{ monitor.id }}
        <img class="sm:block hidden h-auto w-12" :src="'http://www.google.com/s2/favicons?domain=' + monitor.address + '&sz=128'" :alt="monitor.address + ' Logo'">
        <div class="flex flex-wrap sm:col-start-2 col-start-1 col-end-8">
            <a class="sm:flex-none flex-wrap" :href="'https://' + monitor.address" target="_blank">
                <span class="text-xl">{{ monitor.name }}</span>
                <span class="text-slate-500"> ( {{ monitor.address }} ) </span>
            </a>
        </div>

    </div>
</template>