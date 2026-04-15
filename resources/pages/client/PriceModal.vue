<template>
  <div class="relative price-modal-wrapper flex-1">
    <!-- Price Range Button Trigger -->
    <label class="flex items-center gap-2 text-gray-700 font-medium mb-2 pl-2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879-.659a11.464 11.464 0 015.728 0l.879.659M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
      </svg>
      Price Range
    </label>
    <div class="relative">
      <button 
        @click="priceModalOpen = true"
        class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 outline-none cursor-pointer hover:border-[#00B4FF] transition-colors text-left"
      >
        <span class="text-gray-500 text-sm">{{ priceDisplay }}</span>
      </button>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879-.659a11.464 11.464 0 015.728 0l.879.659M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
      </svg>
    </div>

    <!-- Price Range Modal -->
    <div v-if="priceModalOpen" class="price-dropdown">
      <div class="price-content">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Select Price Range</h3>
        
        <div class="price-input-group mb-4">
          <label class="text-sm font-medium text-gray-600 mb-2 block">Min Price (per night)</label>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">₱</span>
            <input 
              v-model.number="localPriceMin" 
              type="number" 
              min="0"
              placeholder="0"
              class="flex-1 border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#00B4FF]"
            />
          </div>
        </div>
        
        <div class="price-input-group mb-4">
          <label class="text-sm font-medium text-gray-600 mb-2 block">Max Price (per night)</label>
          <div class="flex items-center gap-2">
            <span class="text-gray-500">₱</span>
            <input 
              v-model.number="localPriceMax" 
              type="number" 
              min="0"
              placeholder="No limit"
              class="flex-1 border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#00B4FF]"
            />
          </div>
        </div>
        
        <div class="flex gap-3">
          <button 
            @click="handleReset"
            class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors"
          >
            Reset
          </button>
          <button 
            @click="handleApply"
            class="flex-1 px-4 py-2 bg-[#00B4FF] hover:bg-[#009CE0] text-white rounded-lg font-semibold transition-colors"
          >
            Apply
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  priceMin: { type: Number, default: 0 },
  priceMax: { type: Number, default: 0 }
});

const emit = defineEmits(['update:priceMin', 'update:priceMax', 'reset']);

const priceModalOpen = ref(false);

// Local state for two-way binding
const localPriceMin = ref(props.priceMin);
const localPriceMax = ref(props.priceMax);

// Watch for prop changes
watch(() => props.priceMin, (newVal) => {
  localPriceMin.value = newVal;
});

watch(() => props.priceMax, (newVal) => {
  localPriceMax.value = newVal;
});

// Display text
const priceDisplay = computed(() => {
  const min = localPriceMin.value > 0 ? `₱${localPriceMin.value.toLocaleString()}` : 'Min';
  const max = localPriceMax.value > 0 ? `₱${localPriceMax.value.toLocaleString()}` : 'Max';
  if (localPriceMin.value > 0 || localPriceMax.value > 0) {
    return `${min} - ${max}`;
  }
  return 'Select price range';
});

// Reset handler
const handleReset = () => {
  localPriceMin.value = 0;
  localPriceMax.value = 0;
  emit('reset');
  emit('update:priceMin', 0);
  emit('update:priceMax', 0);
  priceModalOpen.value = false;
};

// Apply handler
const handleApply = () => {
  emit('update:priceMin', localPriceMin.value);
  emit('update:priceMax', localPriceMax.value);
  priceModalOpen.value = false;
};

// Expose for external reset
const resetFromParent = () => {
  localPriceMin.value = 0;
  localPriceMax.value = 0;
};

defineExpose({
  resetFromParent
});
</script>

<style scoped>
.price-modal-wrapper {
  position: relative;
}

.price-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 0.5rem;
  background-color: white;
  z-index: 60;
  animation: slideDown 0.2s ease-out;
  border-radius: 1.5rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  width: 100%;
  min-width: 320px;
  overflow: hidden;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.price-content {
  background: white;
  border-radius: 1.5rem;
  padding: 1.5rem;
  width: 100%;
  overflow: hidden;
}
</style>