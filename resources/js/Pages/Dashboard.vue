<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import MonitorChart from '@/Components/MonitorChart.vue';
import AddMonitorForm from '@/Pages/Monitor/Partials/AddMonitorForm.vue';
import MonitorList from '@/Pages/Monitor/Partials/MonitorList.vue';

const props = defineProps({
    monitors: {
        type: Object
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Dashboard</h2>
        </template>

        <div class="pt-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-white">You're logged in!</div>
                </div>
            </div>
        </div>

        <div class="pt-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">
                    <AddMonitorForm
                        class="w-full"
                    />
                </div>
            </div>
        </div>

        <div class="">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <MonitorList
                    :monitors="props.monitors"
                    class="w-full"
                />
            </div>
        </div>

        <!--MonitorChart /-->

    </AuthenticatedLayout>
</template>
