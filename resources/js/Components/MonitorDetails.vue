<script setup>
import Modal from '@/Components/Modal.vue';

import { onMounted, onUnmounted, ref } from 'vue';

import VueApexCharts from "vue3-apexcharts";
import axios from 'axios';
import moment from 'moment';

const props = defineProps({
    monitor: {
        type: Object,
    },
    showDetailsMonitorModal: {
        type: Boolean,
    },
});

let pings = ref([]);

// chart options with ApexCharts response_time by date
const options = {
    chart: {
        height: 350,
        type: 'scatter',
        zoom: {
            enabled: false,
        }
    },
    xaxis: {
        tickAmount: 10,
        labels: {
            formatter: function (val) {
                return moment(val).format('DD/MM/YYYY HH:mm')
            }
        }
    },
    yaxis: {
        tickAmount: 10
    }
};

let series= ref([{data: []}]);

onMounted(() => {
    loadPings();
});

// get all pings for the monitor
const loadPings = async () => {
    await axios
        .get(route('monitors.pings', props.monitor.id))
        .then((response) => {
            pings.value = response.data.pings;

            console.log(pings.value);
            // format the data to be displayed in the chart with ApexCharts
            series.value[0].data = pings.value.map((ping) => {
                return {
                    x: moment(ping.created_at).format('DD/MM/YYYY HH:mm'),
                    y: ping.response_time,
                }
            });
            console.log(series.value[0]);

        })
        .catch((error) => {
            console.log(error);
        });
}

</script>

<template>
    <!-- modal to add a new monitor -->
    <Modal :show="showDetailsMonitorModal" @close="showDetailsMonitorModal = false">
        <div class="flex flex-col justify-center items-center h-screen">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl px-6">
                <div class="flex flex-col justify-center items-center pt-8">
                    <h2 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold mt-2">Informations</h2>
                </div>
                <div>
                    <VueApexCharts width="600" :options="options" :series="series"></VueApexCharts>
                </div>
            </div>
        </div>
    </Modal>
</template>
