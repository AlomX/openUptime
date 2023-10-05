<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import moment from 'moment';
import Popper from "vue3-popper";

import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const emit = defineEmits(['edit', 'delete', 'stats', 'ping', 'moveBack', 'moveForward']);

const lastIcon = ref(null);
const props = defineProps({
    monitor: {
        type: Object,
    }
});

let statusColor = ref("dark:text-slate-200");
let favicon = ref(null);
let pings = ref([]);
let last_response = ref(null);

let loop = ref(null);

onMounted(() => {
    loadLatestPings();
    
    loop.value = setInterval(() => {
        loadLatestPings();
    }, props.monitor.interval / 3);

    if( props.monitor.icon == "favicon" ){
        if( !props.monitor.url ) {
            loadFavicon(props.monitor.address);
        }else{
            loadFavicon(props.monitor.url);
        }
    }else{
        favicon.value = 'icon';
        statusToColor(props.monitor.status);
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
            alert();
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
        if ((!lastChange.first && lastChange.response_time == 0) || (lastChange.first == 1 && lastChange.response_time > 0)) {
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

const alert = () => {
    // get the last two ping and if the last indicates a failure and the previous one was a success then make a sound
    if (pings.value.length > 1) {
        if (pings.value[0].response_time == 0 && pings.value[1].response_time > 0) {
            let audio = new Audio('/audio/alert.mp3');
            audio.play();
        }
    }
}

const statusToColor = (status) => {
    switch (status) {
        case 3:
            statusColor.value = 'text-green-500 drop-shadow-green-500';
            return 'text-green-500';
        case 2:
            statusColor.value = 'text-red-600 drop-shadow-red-600 ';
            return 'text-red-600';
        case 1:
            statusColor.value = 'text-orange-400 drop-shadow-orange-400';
            return 'text-orange-400';
        default:
            statusColor.value = 'dark:text-slate-200';
            return 'dark:text-slate-200';
    }
}

const switchStatus = () => {
    props.monitor.status++;
    if (props.monitor.status > 3) {
        props.monitor.status = 0;
    }
    statusColor.value = 'animate-pulse';
    axios
        .put(route('monitors.update', props.monitor.id), props.monitor)
        .then((response) => {
            statusToColor(props.monitor.status);
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>

<template>
    <div class="group relative">
        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-cyan-600 rounded-lg blur opacity-0 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
        <div class="relative p-4 sm:p-2 bg-white dark:bg-slate-800 dark:text-slate-200 shadow sm:rounded-lg auto-cols-max items-center flex flex-wrap">
            <div class="flex flex-wrap w-8/12">
                <Popper class="w-full group-hover:pl-14 transition-all" style="border: 0!important; margin: 0!important; display:block!important;" arrow hover :content="monitor.name">
                    <div class="text-lg truncate cursor-help">{{ monitor.name }}</div>
                </Popper>
                <div class="items-center flex">
                    <Popper class="cursor-help pr-2 shrink-0" @click="switchStatus" style="border: 0!important; margin: 0!important; display:block!important;" arrow hover>
                        <img class="h-auto w-7 group-hover:w-12 group-hover:-mt-7 transition-all" :src="favicon" :alt="monitor.address + ' Logo'" v-if="favicon && favicon != 'icon'">
                        <div class="animate w-7 h-7 flex items-center justify-center group-hover:w-12 group-hover:-mt-7 transition-all" :class="statusColor" v-else-if="favicon == 'icon'">
                            <i :class="'bi bi-' + monitor.icon + ' text-xl group-hover:text-4xl transition-all'"></i>
                        </div>
                        <div class="animate animate-pulse w-7 h-7 flex items-center justify-center group-hover:w-12 group-hover:-mt-7 transition-all" v-else>
                            <i class="bi bi-globe text-xl group-hover:text-4xl transition-all"></i>
                        </div>
                        <template #content v-if="monitor.note">
                            <div v-html="monitor.note.replace(/\r\n|\r|\n/g,'<br />')"></div>
                        </template>
                    </Popper>
                    <p class="truncate h-7">
                        <a :href="monitor.url?monitor.url:'http://'+monitor.address" target="_blank" class="text-sm text-blue-500/[0.8] hover:text-blue-300 truncate"> ( {{ monitor.address }} ) </a>
                    </p>
                </div>
            </div>
            <span class="w-4/12 text-right text-slate-500 shrink pt-1" v-if="pings">
                <!-- options buttons -->
                <div class="flex align-middle justify-end">
                    <Dropdown align="right" width="48" v-if="monitor.links">
                        <template #trigger>
                            <div class="btn btn-sm hover:text-blue-500 mr-2">
                                <i class="bi bi-bookmarks-fill"></i>
                            </div>
                        </template>

                        <template #content>
                            <a
                                v-for="link in JSON.parse(monitor.links)" :key="link.id"
                                :href="link.url"
                                target="_blank"
                                class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                            >
                                {{ link.name }}
                            </a>
                        </template>
                    </Dropdown>
                    <button class="btn btn-sm hover:text-blue-500 mr-2" @click="emit('edit')">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <div class="btn btn-sm hover:text-blue-500">
                                <i class="bi bi-gear-fill"></i>
                            </div>
                        </template>

                        <template #content>
                            <button class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out" @click="emit('ping')">
                                <i class="bi bi-arrow-repeat"></i> Forcer le ping
                            </button>
                            <button class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out" @click="emit('moveBack')">
                                <i class="bi bi-arrow-left"></i> Déplacer en arrière
                            </button>
                            <button class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out" @click="emit('moveForward')">
                                <i class="bi bi-arrow-right"></i> Déplacer en avant
                            </button>
                            <hr class="my-2">
                            <button class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out hover:text-red-500 dark:hover:text-red-500" @click="emit('delete')">
                                <i class="bi bi-trash-fill"></i> Supprimer
                            </button>
                        </template>
                    </Dropdown>
                    <!--button class="btn btn-sm hover:text-red-500" @click="emit('delete')">
                        <i class="bi bi-trash-fill"></i>
                    </button-->
                </div>
                <i ref="lastIcon" class="bi bi-clock"></i><span class="truncate text-sm"> {{ last_response }}</span>
            </span>
            <div class="w-full left-0 right-0 mt-1" v-if="pings" @click="emit('stats')">
                <div class="object-fill max-w-full flex gap-x-0.5 flex-nowrap flex-row-reverse overflow-x-hidden">
                    <div class="flex-auto text-sm" v-for="ping in pings.slice()" :key="ping.created_at">
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