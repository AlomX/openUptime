<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import moment from 'moment';
import Popper from "vue3-popper";

const emit = defineEmits(['edit', 'delete', 'stats']);

const lastIcon = ref(null);
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
    }, props.monitor.interval / 2);

    if( props.monitor.icon == "favicon" ){
        if( !props.monitor.url ) {
            loadFavicon(props.monitor.address);
        }else{
            loadFavicon(props.monitor.url);
        }
    }else{
        favicon.value = 'icon';
    }

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
            //last_response.value = moment(String(pings.value[0].created_at)).format('hh:mm:ss');
        })
        .catch((error) => {
            console.log(error);
        });

    if (pings.value.length) {
        last_response.value = await lastChange();
    }
}

const pingColor = (ping) => {
    if (ping.response_time == 0) {
        return 'bg-red-600';
    } else if (ping.response_time > 40) {
        return 'bg-orange-400';
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
        }
    }
}

const lastChange = async () => {
    let lastChange = null;
    
    await axios.get(route('monitors.lastChange', props.monitor))
        .then((response) => {
            lastChange = response.data.lastChange;
        })
        .catch((error) => {
            console.log(error);
        });

    if (lastChange) {
        let lastChangeDate = moment(String(lastChange.created_at));
        let now = moment();
        let duration = moment.duration(now.diff(lastChangeDate));
        let days = duration.days();
        let hours = duration.hours();
        let minutes = duration.minutes();
        let message = '';
        if (days > 0) {
            message += days + 'j ';
        }
        if (hours > 0) {
            message += hours + 'h ';
        }
        if (minutes > 0) {
            message += minutes + 'm ';
        }
        if (message == '') {
            message = '< 1 min';
        }
        if (lastChange.response_time == 0) {
            lastIcon.value.attributes.removeNamedItem('class');
            lastIcon.value.setAttribute('class', 'bi bi-check-lg text-green-500');
            return message;
        } else {
            lastIcon.value.attributes.removeNamedItem('class');
            lastIcon.value.setAttribute('class', 'bi bi-x-lg text-red-500');
            return message;
        }
    }
    return '';
}
</script>

<template>
    <div class="group relative">
        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-cyan-600 rounded-lg blur opacity-0 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
        <div class="relative p-4 sm:p-4 bg-white dark:bg-slate-800 dark:text-slate-200 shadow sm:rounded-lg auto-cols-max items-center flex flex-wrap">
            <div class="flex w-8/12">
                <div class="shrink-0">
                    <img class="sm:block hidden h-auto w-12" :src="favicon" :alt="monitor.address + ' Logo'" v-if="favicon && favicon != 'icon'">
                    <div class="animate w-10 h-10 flex items-center justify-center" v-else-if="favicon == 'icon'">
                        <i :class="'bi bi-' + monitor.icon + ' text-4xl mt-4'"></i>
                    </div>
                    <div class="animate animate-pulse w-10 h-10 flex items-center justify-center text" v-else>
                        <i class="bi bi-globe text-4xl mt-4"></i>
                    </div>
                </div>
                <div class="items-center pl-2 overflow-hidden">
                    <p class="text-xl truncate">{{ monitor.name }}</p>
                    <p>
                        <a :href="monitor.url?monitor.url:'http://'+monitor.address" target="_blank" class="text-blue-500/[0.8] hover:text-blue-300 truncate"> ( {{ monitor.address }} ) </a>
                    </p>
                </div>
            </div>
            <span class="w-4/12 text-right text-slate-500 shrink pt-1" v-if="pings">
                <!-- options buttons -->
                <div>
                    <button class="btn btn-sm hover:text-blue-500 mr-2" @click="emit('edit')">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-sm hover:text-red-500" @click="emit('delete')">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
                <i ref="lastIcon" class="bi bi-clock"></i> {{ last_response }}
            </span>
            <div class="w-full left-0 right-0 mt-1" v-if="pings" @click="emit('stats')">
                <div class="object-fill max-w-full flex gap-x-0.5 flex-nowrap flex-row-reverse">
                    <div class="flex-auto" v-for="ping in pings.slice()" :key="ping.created_at">
                        <Popper style="border: 0!important; margin: 0!important; display:block!important;" arrow hover placement="bottom" :content="ping.response_time + ' ms'">
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