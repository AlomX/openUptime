<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto'

const uptimeData = [0];

onMounted(() => {
    // Create the chart using Chart.js
    const ctx = document.getElementById('uptime-chart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
                labels: uptimeData.map(data => data.time),
                datasets: [{
                label: 'Website Uptime',
                data: uptimeData.map(data => data.status),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        }
    });

    // Ping the website every hour
    setInterval(() => {
        axios.get('http://openuptime.local')
            .then(response => {
                const time = new Date();
                const status = Math.floor(Math.random() * 50);
                //const status = response.status === 200 ? 1 : 0;
                uptimeData.push({ time, status });
                addPoint(status);
            })
            .catch(error => {
                const time = new Date();
                const status = 0;
                uptimeData.push({ time, status });
                addPoint(status);
            });
    }, 10000); // 3600000 = 1 hour

    function addPoint(status) {
        chart.data.labels.push(status);
        chart.data.datasets.forEach((dataset) => {
            dataset.data.push(status);
        });
        chart.update();
    }
});
</script>

<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                <canvas id="uptime-chart" width="600" height="400"></canvas>
            </div>
        </div>
    </div>
</template>
