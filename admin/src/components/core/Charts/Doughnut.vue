<template>
  <div>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { Chart, DoughnutController, ArcElement, Tooltip, Legend } from 'chart.js'

// Register Chart.js components
Chart.register(DoughnutController, ArcElement, Tooltip, Legend)

// ✅ Accept dynamic data as a prop
const props = defineProps({
  data: {
    type: Object,
    required: true
  }
})

const canvas = ref(null)
let chartInstance = null

// Function to render or update the chart
const renderChart = () => {
  if (chartInstance) chartInstance.destroy() // Destroy old chart before re-rendering

  chartInstance = new Chart(canvas.value, {
    type: 'doughnut',
    data: props.data,
    options: {
      responsive: true,
      maintainAspectRatio: false
    }
  })
}

onMounted(() => {
  renderChart()
})

// ✅ Watch for changes in data (reactive updates)
watch(() => props.data, renderChart, { deep: true })
</script>

<style scoped>
canvas {
  width: 300px;
  height: 300px;
}
</style>
