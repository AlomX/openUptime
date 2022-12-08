<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import MonitorChart from '@/Components/MonitorChart.vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Profile</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">

                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">

                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-slate-800 shadow sm:rounded-lg">

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
