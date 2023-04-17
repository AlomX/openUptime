<script setup>
import Modal from '@/Components/Modal.vue';

import { onMounted, ref } from 'vue';

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
        stacked: false,
        type: 'line',
        tickPlacement: 'on',
        toolbar: {
            show: true,
            offsetX: 0,
            offsetY: 0,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
            },
            autoSelected: 'zoom' 
        },
        zoom: {
            enabled: true,
            type: 'x',
            autoScaleYaxis: true,
            zoomedArea: {
                fill: {
                    color: '#90CAF9',
                    opacity: 0.4
                },
                stroke: {
                    color: '#0D47A1',
                    opacity: 0.4,
                    width: 1
                }
            }
        },
    },
    stroke: {
        curve: 'stepline',
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
        tickAmount: 1,
        labels: {
            // arround the response time to 2 decimals
            formatter: function (val) {
                return Math.round(val * 100) / 100
            }
        }
    },
    dataLabels: {
        enabled: true,
    },
    title: {
        text: 'Temps de réponse en millisecondes par date',
        align: 'left',
    },
    grid: {
        row: {
            colors: ['#f3f4f5', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5,
        },
    },
    theme: {
        monochrome: {
            enabled: true,
            color: '#000',
            shadeTo: 'light',
            shadeIntensity: 0.65,
        },
    },
};

let series= ref([{
    name : 'Temps de réponse',
    data: []
}]);

onMounted(() => {
    loadPings();
});

// get all pings for the monitor
const loadPings = async () => {
    if (!props.monitor) {
        return;
    }
    await axios
        .get(route('monitors.pings', props.monitor.id))
        .then((response) => {
            pings.value = response.data.pings;

            // format the data to be displayed in the chart with ApexCharts
            series.value[0].data = pings.value.map((ping) => {
                return {
                    x: ping.created_at,
                    y: ping.response_time,
                }
            });

        })
        .catch((error) => {
            console.log(error);
        });
}

</script>

<template>
    <!-- modal to add a new monitor -->
    <Modal :show="showDetailsMonitorModal" @close="showDetailsMonitorModal = false;">
        <div class="flex flex-col justify-center items-center h-screen">
            <div class="bg-white rounded-lg shadow-xl px-6">
                <div class="pt-8">
                    <VueApexCharts width="600" :options="options" :series="series"></VueApexCharts>
                </div>
            </div>
        </div>
    </Modal>
</template>
