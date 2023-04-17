<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import moment from 'moment';
import Popper from "vue3-popper";

const props = defineProps({
    monitor: {
        type: Object,
    }
});

let favicon = ref(null);
let pings = ref([]);
let last_response = ref(null);

let loop = ref(null);

onMounted(() => {
    loadLatestPings();
    loop.value = setInterval(() => {
        loadLatestPings();
    }, 10000);
    loadFavicon(props.monitor.address);
});

onUnmounted(() => {
    clearInterval(loop.value);
});

const loadLatestPings = async () => {
    await axios
        .get(route('monitors.latestPings', props.monitor.id))
        .then((response) => {
            if( response.data.pings.length == 0 ) {
                return;
            }
            pings.value = response.data.pings;
            last_response.value = moment(String(pings.value[0].created_at)).format('hh:mm:ss');
        })
        .catch((error) => {
            console.log(error);
        });
}

const pingColor = (ping) => {
    if (ping.response_time == 0) {
        return 'bg-red-600';
    } else if (ping.response_time > 22) {
        return 'bg-orange-600';
    } else {
        return 'bg-green-500';
    }
}

const loadFavicon = (address) => {
    // test google icon
    let icon = 'http://www.google.com/s2/favicons?domain=' + address + '&sz=128';
    let img = new Image();
    img.src = icon;
    img.onload = function() {
        if( img.width != 16 ) {
            favicon.value = icon;
            return;
        }else{
            favicon.value = 'icon';
        }
    }
}
</script>

<template>
    <div class="group relative">
        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-cyan-600 rounded-lg blur opacity-0 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
        <div class="relative p-4 sm:p-8 bg-white dark:bg-slate-800 dark:text-slate-200 shadow sm:rounded-lg auto-cols-max items-center flex flex-wrap">
            <div class="flex w-9/12">
                <div class="shrink-0">
                    <img class="sm:block hidden h-auto w-12" :src="favicon" :alt="monitor.address + ' Logo'" v-if="favicon && favicon != 'icon'">
                    <div class="animate w-10 h-10 flex items-center justify-center" v-else-if="favicon == 'icon'">
                        <i class="bi bi-hdd-network text-4xl"></i>
                    </div>
                    <div class="animate animate-pulse w-10 h-10 flex items-center justify-center text" v-else>
                        <i class="bi bi-globe text-4xl"></i>
                    </div>
                </div>
                <div class="items-center flex pl-3">
                    <a :href="'https://' + monitor.address" target="_blank">
                        <span class="text-xl">{{ monitor.name }}</span>
                        <span class="text-blue-500/[0.8] hover:text-blue-300"> ( {{ monitor.address }} ) </span>
                    </a>
                </div>
            </div>
            <span class="w-3/12 text-right text-slate-500 shrink pt-1" v-if="pings">
                Dernier appel : 
                {{ last_response }}
            </span>
            <div class="w-full left-0 right-0 mt-3" v-if="pings">
                <div class="object-fill max-w-full flex gap-x-0.5 flex-nowrap flex-row-reverse">
                    <div class="flex-auto" v-for="ping in pings.slice()" :key="ping.created_at">
                        <Popper style="border: 0!important; margin: 0!important; display:block!important;" arrow placement="bottom" :content="ping.response_time + ' ms'">
                            <div class="rounded cursor-help" 
                                v-bind:class="pingColor(ping)"
                                v-bind:name="'Temps de réponse : ' + ping.response_time + ' ms'">
                                &nbsp;
                            </div>
                        </Popper>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>