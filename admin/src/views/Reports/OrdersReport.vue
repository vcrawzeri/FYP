<template>
  <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl p-6">
    <h2 class="text-2xl font-bold text-gray-700 mb-6">📦 Orders Report</h2>

    <Bar
      id="orders-chart"
      :options="chartOptions"
      :data="chartData"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import axiosClient from '../../axios'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const chartData = ref({
  labels: [],
  datasets: []
})

const chartOptions = ref({
  responsive: true,
  plugins: {
    legend: { display: true, position: 'bottom' },
    title: { display: true, text: 'Orders over Time' }
  },
  scales: {
    x: {
      ticks: {
        color: '#4b5563',
        maxRotation: 45,
        minRotation: 45
      }
    },
    y: {
      beginAtZero: true,
      max:10,
      ticks: {
        stepSize: 1,      // 👈 Force whole numbers
        precision: 0,     // 👈 Remove decimals
        color: '#4b5563',
        callback: (value) => Number.isInteger(value) ? value : null
      }
    }
  }
})

onMounted(async () => {
  try {
    const { data } = await axiosClient.get('/report/orders?d=1m')

    chartData.value = {
      labels: data.labels,
      datasets: data.datasets.map(dataset => ({
        ...dataset,
        borderRadius: 6,     // 👈 rounded bars (optional)
        barThickness: 20,    // 👈 consistent bar width (optional)
        backgroundColor: '#f87171'
      }))
    }
  } catch (error) {
    console.error('Error loading report:', error)
  }
})
</script>
