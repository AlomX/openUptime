<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import { Head } from '@inertiajs/vue3';

import { onMounted, onUnmounted, ref } from 'vue';

import MonitorInfo from '@/Components/MonitorInfo.vue';
import MonitorDetails from '@/Components/MonitorDetails.vue';
import axios from 'axios';

const props = defineProps({
    monitors: {
        type: Object,
    }
});

let listMonitors = ref(props.monitors);
let selectedMonitor = ref(null);

const showDetailsMonitorModal = ref(false);
const showNewMonitorModal = ref(false);
const monitorName = ref('');
const monitorAdress = ref('');
const monitorUrl = ref('');
const monitorInterval = ref(60000);
const monitorNote = ref('');
const monitorIcon = ref('');

let openParameters = ref(false);

const createMonitor = async () => {
    await axios 
        .post('/monitors', {
            name: monitorName.value,
            address: monitorAdress.value,
            url: monitorUrl.value,
            interval: monitorInterval.value,
            note: monitorNote.value,
        })
        .then((response) => {
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);

        });
    
    showNewMonitorModal.value = false;
}

const loadMonitors = async () => {
    await axios
        .get('/monitors')
        .then((response) => {
            listMonitors.value = response.data.monitors;
        })
        .catch((error) => {
            console.log(error);
        });
}

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tableau de bord</h2>
                
                <!-- Add button aligned to the right -->
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="showNewMonitorModal = true">
                    Ajouter un appareil
                </button>
            </div>
        </template>

        <!--div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">Bienvenue dans votre gestion des appareils en ligne</div>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click="showNewMonitorModal = true">
                        Ajouter un appareil
                    </button>
                </div>
            </div>
        </div-->

        <!-- foreach monitors -->
        <div class="py-4 grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 min-[2500px]:grid-cols-8 gap-4 auto-cols-[minmax(0,_2fr)] px-5">
            <MonitorInfo :monitor="monitor" v-for="monitor in listMonitors" :key="monitor.id" @click="selectedMonitor = monitor; showDetailsMonitorModal = true" />
        </div>

        <MonitorDetails :monitor="selectedMonitor" :showDetailsMonitorModal="showDetailsMonitorModal" :key="selectedMonitor" />

        <!-- modal to add a new monitor -->
        <Modal :show="showNewMonitorModal" @close="showNewMonitorModal = false">
            <div class="flex flex-col justify-center items-center h-screen">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-96">
                    <div class="flex flex-col justify-center items-center pt-8">
                        <div class="flex justify-center items-center w-16 h-16 rounded-full bg-blue-500">
                            <i class="bi bi-hdd-network text-white text-4xl pt-2"></i>
                        </div>
                        <h2 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold mt-2">Nouvel Appareil</h2>
                    </div>
                    <div class="flex flex-col justify-center items-center pt-4 pb-8">
                        <form class="flex flex-col justify-center items-center">
                            <input type="text" class="w-80 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4" autofocus placeholder="Nom de l'appareil" v-model="monitorName">
                            <!-- input ip or url -->
                            <input type="text" class="w-80 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4 mt-2" placeholder="Adresse IP ou URL" v-model="monitorAdress">
                            
                            <!-- accordeon suplement paramaters -->
                            <div class="w-80 mt-2">
                                <button class="flex justify-between items-center w-full" type="button" @click="openParameters = !openParameters">
                                    <h3 class="text-gray-800 dark:text-gray-200 text-sm font-semibold">Paramètres supplémentaires</h3>
                                    <div class="text-gray-800 dark:text-gray-200 text-sm font-semibold focus:outline-none">
                                        <i class="bi bi-chevron-down" v-if="!openParameters"></i>
                                        <i class="bi bi-chevron-up" v-if="openParameters"></i>
                                    </div>
                                </button>
                                <div class="mt-2" v-if="openParameters">
                                    <div class="flex justify-between items-center">
                                        <input type="text" class="w-80 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4 mt-2" placeholder="URL du lien" v-model="monitorUrl">
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <h3 class="text-gray-800 dark:text-gray-200 text-sm font-semibold">Fréquence</h3>
                                        <select class="w-24 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4" v-model="monitorInterval">
                                            <option value="60000">1 min</option>
                                            <option value="300000">5 min</option>
                                            <option value="600000">10 min</option>
                                            <option value="1800000">30 min</option>
                                            <option value="3600000">1h</option>
                                            <option value="7200000">2h</option>
                                            <option value="14400000">4h</option>
                                            <option value="28800000">8h</option>
                                            <option value="43200000">12h</option>
                                            <option value="86400000">24h</option>
                                        </select>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <h3 class="text-gray-800 dark:text-gray-200 text-sm font-semibold">Icone</h3>
                                        <select class="w-24 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4" v-model="monitorIcon">
                                            <option value="hdd-network"><i style="font-family: bootstrap-icons !important;" class="bi bi-hdd-network"></i></option> /////////
                                        </select>
                                    </div> 
                                    <textarea class="w-80 h-20 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4 mt-2" placeholder="Note" v-model="monitorNote"></textarea>
                                </div>
                            </div>
                            <!-- buttons -->
                            <div class="flex justify-center items-center w-full mt-2 gap-2">
                                <button class="w-1/2 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold focus:outline-none" type="button" @click="showNewMonitorModal = false">Annuler</button>
                                <button class="w-1/2 h-10 rounded-lg bg-blue-500 text-white text-sm font-semibold focus:outline-none" type="submit" @click.prevent="createMonitor()">Créer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
