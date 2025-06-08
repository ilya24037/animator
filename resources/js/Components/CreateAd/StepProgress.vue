<template>
  <div class="relative">
    <!-- Линия прогресса -->
    <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200">
      <div 
        class="h-full bg-blue-600 transition-all duration-300"
        :style="{ width: progressWidth + '%' }"
      />
    </div>
    
    <!-- Шаги -->
    <div class="relative flex justify-between">
      <div 
        v-for="(step, index) in steps" 
        :key="step.id"
        class="flex flex-col items-center"
      >
        <!-- Кружок -->
        <div 
          class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300"
          :class="getStepClasses(index + 1)"
        >
          <svg 
            v-if="index + 1 < currentStep" 
            class="w-5 h-5 text-white"
            fill="currentColor" 
            viewBox="0 0 20 20"
          >
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          <span v-else>{{ index + 1 }}</span>
        </div>
        
        <!-- Название шага -->
        <span 
          class="mt-2 text-xs text-center max-w-[100px] transition-colors duration-300"
          :class="index + 1 <= currentStep ? 'text-gray-900' : 'text-gray-400'"
        >
          {{ step.name }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentStep: {
    type: Number,
    required: true
  },
  steps: {
    type: Array,
    required: true
  }
})

const progressWidth = computed(() => {
  if (props.steps.length <= 1) return 100
  return ((props.currentStep - 1) / (props.steps.length - 1)) * 100
})

function getStepClasses(stepNumber) {
  if (stepNumber < props.currentStep) {
    return 'bg-blue-600 text-white'
  } else if (stepNumber === props.currentStep) {
    return 'bg-blue-600 text-white ring-4 ring-blue-100'
  } else {
    return 'bg-gray-200 text-gray-500'
  }
}
</script>