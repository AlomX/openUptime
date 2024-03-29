<script setup>
import Modal from '@/Components/Modal.vue';
import MonitorDetailsGraph from './MonitorDetailsGraph.vue';

import { onMounted, ref } from 'vue';

import axios from 'axios';
import moment from 'moment';

const emit = defineEmits(['close']);

const props = defineProps({
    monitor: {
        type: Object,
    },
    showDetailsMonitorModal: {
        type: Boolean,
    },
});

let pingsHistory = ref([]);
let message = ref("Chargement ...");

onMounted(() => {
    loadPings();
});

// get all pings for the monitor
const loadPings = async () => {
    if (!props.monitor) {
        return;
    }
    await axios
        .get(route('monitors.history', props.monitor.id))
        .then((response) => {
            pingsHistory.value = response.data.history;
            console.log(pingsHistory.value);
            message.value = "Aucun ping pour ce moniteur dans les 7 derniers jours";
        })
        .catch((error) => {
            console.log(error);
        });
}
</script>

<template>
    <!-- modal to add a new monitor -->
    <Modal :show="showDetailsMonitorModal" @close="emit('close')">
        <div class="flex flex-col justify-center items-center h-screen">
            <div class="bg-white rounded-lg shadow-xl px-6 pt-5">
                <div class="flex flex-row justify-between items-center">
                    <h2 class="text-2xl font-bold">{{ props.monitor.name }} <span class="text-gray-500 text-sm">({{ props.monitor.address }})</span></h2>
                    <button class="text-gray-500 hover:text-gray-700 pl-5" @click="emit('close')">
                        <i class="bi bi-x-circle-fill text-xl"></i>
                    </button>
                </div>
                <div v-if="pingsHistory.length == 0">
                    <p class="text-gray-600 text-center pt-4">{{ message }}</p>
                </div>

                <div class="flex flex-col pt-2 w-full pl-9 max-h-80 overflow-auto">
                    <div class="w-25 flex flex-row justify-between items border-b-2 pb-1" v-for="ping in pingsHistory" :key="ping.start">
                        <span class="text-gray-600">
                            <i class="bi text-xl" :class="ping.status == 'up' ? 'bi-arrow-up-right text-green-500' : 'bi-arrow-down-right text-red-500'" v-if="ping.start"></i>
                            <i class="bi text-xl" :class="ping.status == 'up' ? 'bi-three-dots text-green-500' : 'bi-three-dots text-red-500'" v-else></i>
                            {{ ping.start ? moment(ping.start).format('DD/MM/YYYY HH:mm') : moment(ping.end).format('DD/MM/YYYY HH:mm') }}
                        </span>
                    </div>
                </div>
                <div class="pt-8">
                    <MonitorDetailsGraph :monitor="props.monitor" v-if="pingsHistory.length > 0" />
                </div>
            </div>
        </div>
    </Modal>
</template>
