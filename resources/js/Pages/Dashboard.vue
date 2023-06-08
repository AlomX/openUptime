<script setup>
import VueMultiselect from 'vue-multiselect'

import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

import MonitorInfo from '@/Components/MonitorInfo.vue';
import MonitorDetails from '@/Components/MonitorDetails.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

import axios from 'axios';

const props = defineProps({
    monitors: {
        type: Object,
    }
});

let listMonitors = ref(props.monitors);
let selectedMonitor = ref(null);

const showImportMonitorModal = ref(false);
const showDetailsMonitorModal = ref(false);
const showDeleteMonitorModal = ref(false);
const showNewMonitorModal = ref(false);
const monitorName = ref('');
const monitorAdress = ref('');
const monitorUrl = ref('');
const monitorInterval = ref(300000);
const monitorNote = ref('');
const monitorIcon = ref('favicon');
const monitorCommand = ref('');
const monitorLinks = ref([]);

let openParameters = ref(false);
let searchInput = ref(null);

const createMonitor = async () => {
    await axios 
        .post('/monitors', {
            name: monitorName.value,
            address: monitorAdress.value,
            url: monitorUrl.value,
            interval: monitorInterval.value,
            note: monitorNote.value,
            icon: monitorIcon.value,
            command: monitorCommand.value,
            links: monitorLinks.value,
        })
        .then((response) => {
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);
        });
    
    showNewMonitorModal.value = false;
}

const updateMonitor = async () => {
    await axios 
        .put('/monitors/' + selectedMonitor.value.id, {
            name: monitorName.value,
            address: monitorAdress.value,
            url: monitorUrl.value,
            interval: monitorInterval.value,
            note: monitorNote.value,
            icon: monitorIcon.value,
            command: monitorCommand.value,
            links: monitorLinks.value,
        })
        .then((response) => {
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);
        });
    
    showNewMonitorModal.value = false;
}

const deleteMonitor = async () => {
    await axios 
        .delete('/monitors/' + selectedMonitor.value.id)
        .then((response) => {
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);
        });
    
    showDeleteMonitorModal.value = false;
}

const add = () => {
    monitorName.value = '';
    monitorAdress.value = '';
    monitorUrl.value = '';
    monitorInterval.value = 300000;
    monitorNote.value = '';
    monitorIcon.value = 'favicon';
    monitorCommand.value = '';
    monitorLinks.value = [];
    selectedMonitor.value = null;
    showNewMonitorModal.value = true;
}

const edit = (monitor) => {
    monitorName.value = monitor.name;
    monitorAdress.value = monitor.address;
    monitorUrl.value = monitor.url;
    monitorInterval.value = monitor.interval;
    monitorNote.value = monitor.note;
    monitorIcon.value = monitor.icon;
    monitorCommand.value = monitor.command;
    monitorLinks.value = JSON.parse(monitor.links);
    selectedMonitor.value = monitor;
    showNewMonitorModal.value = true;
}

const importMonitor = async () => {
    const formData = new FormData();
    formData.append('file', document.getElementById('csvInput').files[0]);
    await axios 
        .post('/monitors/import', formData)
        .then((response) => {
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);
        });
    
    showImportMonitorModal.value = false;
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

const changeIcon = () => {
    document.getElementById('icon').attributes.removeNamedItem('class');
    if(monitorIcon.value == 'favicon') {
        document.getElementById('icon').setAttribute('class', 'bi bi-question-circle-fill');
    }else{
        document.getElementById('icon').setAttribute('class', 'bi bi-' + monitorIcon.value);
    }
}

const addLink = () => {
    try {
        monitorLinks.value.push({name: '', url: ''});
    } catch (e) {
        monitorLinks.value = [{name: '', url: ''}];
    }
}

const removeLink = (index) => {
    monitorLinks.value.splice(index, 1);
}

const forcePing = async (monitor) => {
    await axios
        .post('/monitors/' + monitor.id + '/ping')
        .then((response) => {
            listMonitors.value = null;
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);
        });
}

const moveOrder = (monitor, newPosition) => {
    axios
        .patch('/monitors/' + monitor.id + '/order', {
            order: newPosition,
        })
        .then((response) => {
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);
        });
}

const moveOrderAlphabetical = () => {
    axios
        .post(route('monitors.orderAlphabetical'))
        .then((response) => {
            loadMonitors();
        })
        .catch((error) => {
            console.log(error);
        });
}

const search = async () => {
    await loadMonitors();

    listMonitors.value = listMonitors.value.filter((monitor) => {
        monitor.url ? monitor.url : monitor.url = '';
        monitor.note ? monitor.note : monitor.note = '';
        monitor.icon ? monitor.icon : monitor.icon = 'favicon';

        return monitor.name.toLowerCase().includes(searchInput.value.toLowerCase()) || 
            monitor.address.toLowerCase().includes(searchInput.value.toLowerCase()) ||
            monitor.url.toLowerCase().includes(searchInput.value.toLowerCase())     ||
            monitor.note.toLowerCase().includes(searchInput.value.toLowerCase())    ||
            monitor.icon == convertWordToIcon(searchInput.value.toLowerCase());
    });
}

const convertWordToIcon = (word) => {
    switch (word) {
        case 'server': 
        case 'serveur':
            return 'hdd-network';
        case 'website': 
        case 'site': 
        case 'internet':
            return 'favicon';
        case 'database': 
        case 'bdd': 
        case 'base de données':
            return 'database';
        case 'email': 
        case 'mail': 
        case 'courriel': 
        case 'e-mail': 
        case 'e-mail':
            return 'envelope';
        case 'tel': 
        case 'telephone': 
        case 'téléphone': 
        case 'phone': 
        case 'téléphone':
            return 'phone';
        case 'tv': 
        case 'télévision': 
        case 'television':
            return 'tv';
        case 'camera': 
        case 'caméra': 
        case 'caméra':
            return 'camera-video';
        case 'router': 
        case 'routeur':
            return 'router';
        case 'modem': 
        case 'modeme':
            return 'modem';
        case 'printer': 
        case 'imprimante':
            return 'printer';
        case 'pc': 
        case 'ordinateur': 
        case 'computer': 
        case 'ordinateur':
            return 'pc-display';
        case 'laptop': 
        case 'portable': 
        case 'ordinateur portable': 
        case 'computer portable':
            return 'laptop';
        case 'tablet': 
        case 'tablette':
            return 'tablet';
        case 'rack': 
        case 'rack serveur': 
        case 'rack serveur':
            return 'hdd-rack';
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mr-4 shrink-0">Tableau de bord</h2>
                
                <!-- Add button aligned to the right -->
                <div class="flex justify-end items-center flex-wrap">
                    <!-- Search input -->
                    <div class="relative float-left text-gray-600 dark:text-gray-400 mb-2 sm:mb-0">
                        <input type="search" placeholder="Rechercher" class="w-full bg-white dark:bg-gray-800 h-10 px-5 pr-10 rounded text-sm focus:outline-none" v-model="searchInput" @keyup.enter="search">
                        <button type="button" class="absolute right-2 top-2" @click="search">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2" @click="add">
                        Ajouter un appareil
                    </button>
                    <button class="bg-blue-300 dark:bg-blue-800 hover:bg-blue-700 text-white py-2 px-3 rounded ml-2" @click="showImportMonitorModal = true">
                        <i class="bi bi-download"></i>
                    </button>
                    <button class="bg-blue-300 dark:bg-blue-800 hover:bg-blue-700 text-white py-2 px-3 rounded ml-2" @click="moveOrderAlphabetical">
                        <i class="bi bi-type"></i>
                    </button>
                </div>
            </div>
        </template>

        <!-- foreach monitors -->
        <div class="px-3 py-3 grid grid-cols-1 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 min-[2500px]:grid-cols-8 gap-2 auto-cols-[minmax(0,_2fr)]">
            <MonitorInfo 
                :monitor="monitor" v-for="monitor in listMonitors" :key="monitor.id" 
                @stats="selectedMonitor = monitor; showDetailsMonitorModal = true;" 
                @edit="edit(monitor)" 
                @delete="selectedMonitor = monitor; showDeleteMonitorModal = true;" 
                @ping="forcePing(monitor)"
                @moveBack="moveOrder(monitor, monitor.order - 1)"
                @moveForward="moveOrder(monitor, monitor.order + 1)"
            />
        </div>
        
        <MonitorDetails :monitor="selectedMonitor" :showDetailsMonitorModal="showDetailsMonitorModal" :key="selectedMonitor" @close="showDetailsMonitorModal = false; selectedMonitor = null;" />

        <!-- modal to add a new monitor -->
        <Modal :show="showNewMonitorModal" @close="showNewMonitorModal = false;">
            <div class="flex flex-col justify-center items-center h-screen">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-96  overflow-y-auto">
                    <div class="flex flex-col justify-center items-center pt-8">
                        <div class="flex justify-center items-center w-16 h-16 rounded-full bg-blue-500">
                            <i class="bi bi-hdd-network text-white text-4xl pt-2"></i>
                        </div>
                        <h2 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold mt-2" v-if="!selectedMonitor">Nouvel Appareil</h2>
                        <h2 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold mt-2" v-else>Editer un Appareil</h2>
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
                                        <select class="w-48 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4" v-model="monitorInterval">
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
                                        <h3 class="text-gray-800 dark:text-gray-200 text-sm font-semibold">Icone <i id="icon" class="bi bi-question-circle-fill"></i></h3>
                                        <!-- icons select using bi icons -->
                                        <select class="w-48 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4" v-model="monitorIcon" @change="changeIcon">
                                            <option value="favicon">Icone de l'URL</option>
                                            <option value="hdd-network">Serveur</option>
                                            <option value="hdd-rack">Serveur Rack</option>
                                            <option value="pc-display">Ordinateur</option>
                                            <option value="laptop">Ordinateur Portable</option>
                                            <option value="tablet">Tablette</option>
                                            <option value="telephone">Téléphone Fixe</option>
                                            <option value="phone">Téléphone Portable</option>
                                            <option value="tv">Télévision</option>
                                            <option value="camera-video">Caméra</option>
                                            <option value="router">Router</option>
                                            <option value="modem">Modeme</option>
                                            <option value="printer">Imprimante</option>
                                            <option value="motherboard">Autre</option>
                                        </select>
                                    </div> 
                                    <textarea class="w-80 h-20 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4 mt-2" placeholder="Note" v-model="monitorNote"></textarea>
                                    <div class="flex justify-between items-center">
                                        <input type="text" class="w-80 h-10 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-4" placeholder="Commande a éxécuter avec le ping" v-model="monitorCommand">
                                    </div>
                                    <!-- multi select for categories -->
                                    <div class="flex justify-between items-center mt-2">
                                        <VueMultiselect v-model="monitorLinks"
                                            :options="[
                                                { name: 'Vue.js', language: 'JavaScript' },
                                                { name: 'Adonis', language: 'JavaScript' },
                                                { name: 'Rails', language: 'Ruby' },
                                                { name: 'Sinatra', language: 'Ruby' },
                                                { name: 'Laravel', language: 'PHP' },
                                                { name: 'Phoenix', language: 'Elixir' }
                                            ]"
                                            :multiple="true"
                                            :searchable="false"
                                            :close-on-select="false"
                                            :clear-on-select="false"
                                            :allow-empty="true"
                                            :show-no-results="true"
                                            :max-height="200" 
                                            :select-label="'Ajouter'"
                                            :selected-label="'Séléctionner'" 
                                            :deselect-label="'Retirer'"
                                            placeholder="Catégories" 
                                            label="name" 
                                            track-by="name" 
                                        /> 
                                    </div>
                                </div>
                                <!-- Multiple link bookmarker with a form repeater ( Name and URL ) -->
                                <div class="flex justify-between items-center mt-2">
                                    <h3 class="text-gray-800 dark:text-gray-200 text-sm font-semibold">Liens</h3>
                                    <button class="w-8 h-8 rounded-full bg-blue-500 text-white text-lg font-semibold focus:outline-none" type="button" @click="addLink()">+</button>
                                </div>
                                <div class="flex flex-col justify-center items-center my-2" v-for="(link, index) in monitorLinks" :key="index">
                                    <div class="flex justify-between items-center w-full gap-1">
                                        <input type="text" class="w-5/12 h-8 rounded-md border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-2" placeholder="Nom du lien" v-model="link.name">
                                        <input type="text" class="w-6/12 h-8 rounded-md border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none px-2" placeholder="URL du lien" v-model="link.url">
                                        <button class="w-1/12 h-8 rounded-md bg-red-500 text-white text-xl font-semibold focus:outline-none" type="button" @click="removeLink(index)">-</button>
                                    </div>
                                </div>
                            </div>
                            <!-- buttons -->
                            <div class="flex justify-center items-center w-full mt-2 gap-2">
                                <button class="w-1/2 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold focus:outline-none" type="button" @click="showNewMonitorModal = false">Annuler</button>
                                <button class="w-1/2 h-10 rounded-lg bg-blue-500 text-white text-sm font-semibold focus:outline-none" type="submit" @click.prevent="createMonitor()" v-if="!selectedMonitor">Créer</button>
                                <button class="w-1/2 h-10 rounded-lg bg-blue-500 text-white text-sm font-semibold focus:outline-none" type="submit" @click.prevent="updateMonitor()" v-else>Mettre à jour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Deletion Modal -->
        <Modal :show="showDeleteMonitorModal" @close="showDeleteMonitorModal = false">
            <div class="flex flex-col justify-center items-center h-screen">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-96">
                    <div class="flex flex-col justify-center items-center pt-8">
                        <div class="flex justify-center items-center w-16 h-16 rounded-full bg-red-500">
                            <i class="bi bi-send-x text-white text-4xl pt-2 -ml-1"></i>
                        </div>
                        <h2 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold mt-2">Supprimer un Appareil</h2>
                    </div>
                    <div class="flex flex-col justify-center items-center pt-4 pb-8">
                        <form class="flex flex-col justify-center items-center">
                            <h3 class="text-gray-800 dark:text-gray-200 text-sm font-semibold">Voulez-vous vraiment supprimer cet appareil ?</h3>
                            <div class="flex justify-center items-center w-full mt-2 gap-2">
                                <button class="w-1/2 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold focus:outline-none" type="button" @click="showDeleteMonitorModal = false">Annuler</button>
                                <button class="w-1/2 h-10 rounded-lg bg-red-500 text-white text-sm font-semibold focus:outline-none" type="submit" @click.prevent="deleteMonitor()">Supprimer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>

        <Modal :show="showImportMonitorModal" @close="showImportMonitorModal = false">
            <div class="flex flex-col justify-center items-center h-screen">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-96">
                    <div class="flex flex-col justify-center items-center pt-8">
                        <div class="flex justify-center items-center w-16 h-16 rounded-full bg-blue-500">
                            <i class="bi bi-download text-white text-4xl pt-2"></i>
                        </div>
                        <h2 class="text-gray-800 dark:text-gray-200 text-2xl font-semibold mt-2">Importer un tableau</h2>
                    </div>
                    <div class="flex flex-col justify-center items-center pt-4 pb-8">
                        <form class="flex flex-col justify-center items-center">
                            <h3 class="text-gray-800 dark:text-gray-200 text-sm font-semibold">Votre csv doit être formater dans l'ordre suivant :<br/> Nom de l'appareil, Adresse, URL, Note, Commande</h3>
                            <div class="flex justify-center items-center w-full mt-2 gap-2">
                                <input type="file" accept=".csv" class="w-80 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-primary focus:outline-none" id="csvInput">
                            </div>
                            <div class="flex justify-center items-center w-full mt-2 gap-2">
                                <button class="w-1/2 h-10 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold focus:outline-none" type="button" @click="showImportMonitorModal = false">Annuler</button>
                                <button class="w-1/2 h-10 rounded-lg bg-blue-500 text-white text-sm font-semibold focus:outline-none" type="submit" @click.prevent="importMonitor()">Importer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
