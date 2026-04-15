<template>
  <div class="bg-[#dff7ff] p-8 rounded-2rem shadow-sm flex flex-wrap items-end gap-6 relative booking-bar-container">
    <!-- Check-in / Check-out -->
    <div class="flex-1 min-w-200px relative booking-section-dates">
      <label class="flex items-center gap-2 text-gray-700 font-medium mb-2 pl-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
        </svg>
        Check In - Check Out
      </label>
      <div class="relative">
        <input 
          type="text" 
          :value="dateRangeDisplay" 
          @click="dateCalendarOpen = true"
          placeholder="Select check-in and check-out dates"
          readonly
          class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 outline-none cursor-pointer hover:border-[#00B4FF] transition-colors text-gray-700 font-medium placeholder-gray-400 placeholder:text-sm" 
        />
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
        </svg>
      </div>
    </div>

    <!-- Guests & Rooms -->
    <div class="flex-1 min-w-200px">
      <label class="flex items-center gap-2 text-gray-700 font-medium mb-2 pl-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
        Guests & Rooms
      </label>
      <GuestSelector ref="guestSelectorRef"/>
    </div>

    <!-- Price Range -->
    <div class="flex-1 min-w-200px relative booking-section-price">
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
          <span class="text-gray-500 text-sm">{{ priceRangeDisplay }}</span>
        </button>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879-.659a11.464 11.464 0 015.728 0l.879.659M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
        </svg>
      </div>
                
      <!-- Price Range Modal -->
      <div v-if="priceModalOpen" class="price-modal-dropdown">
        <div class="price-modal-content">
          <h3 class="text-lg font-bold text-gray-800 mb-4">Select Price Range</h3>
          
          <div class="price-input-group mb-4">
            <label class="text-sm font-medium text-gray-600 mb-2 block">Min Price (per night)</label>
            <div class="flex items-center gap-2">
              <span class="text-gray-500">₱</span>
              <input 
                v-model.number="priceMin" 
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
                v-model.number="priceMax" 
                type="number" 
                min="0"
                placeholder="No limit"
                class="flex-1 border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-[#00B4FF]"
              />
            </div>
          </div>
          
          <div class="flex gap-3">
            <button 
              @click="resetPriceRange"
              class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors"
            >
              Reset
            </button>
            <button 
              @click="priceModalOpen = false"
              class="flex-1 px-4 py-2 bg-[#00B4FF] hover:bg-[#009CE0] text-white rounded-lg font-semibold transition-colors"
            >
              Apply
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Button -->
    <button
      @click="handleSearch"
      class="bg-[#00B4FF] hover:bg-[#009CE0] text-white px-12 py-3.5 rounded-xl font-bold text-xl transition-all"
    >
      Search
    </button>

    <!-- Calendar positioned inside filter bar container -->
    <div v-if="dateCalendarOpen" class="calendar-absolute-wrapper">
      <FiestaCalendar
        mode="range"
        :isOpen="dateCalendarOpen"
        @update:isOpen="dateCalendarOpen = $event"
        @update:checkIn="checkInDate = $event"
        @update:checkOut="checkOutDate = $event"
        :initialCheckIn="checkInDate"
        :initialCheckOut="checkOutDate"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import GuestSelector from './GuestSelector.vue';
import FiestaCalendar from './FiestaCalendar.vue';

const emit = defineEmits(['search', 'update:checkIn', 'update:checkOut']);

// Calendar state
const checkInDate = ref('');
const checkOutDate = ref('');
const dateCalendarOpen = ref(false);

// Date range display
const dateRangeDisplay = computed(() => {
  if (checkInDate.value && checkOutDate.value) {
    return `${checkInDate.value} - ${checkOutDate.value}`;
  } else if (checkInDate.value) {
    return `${checkInDate.value}`;
  }
  return '';
});

// GuestSelector ref
const guestSelectorRef = ref(null);

// Price range state
const priceMin = ref(0);
const priceMax = ref(0);
const priceModalOpen = ref(false);
const priceRangeDisplay = computed(() => {
  if (priceMin.value > 0 || priceMax.value > 0) {
    const min = priceMin.value > 0 ? `₱${priceMin.value.toLocaleString()}` : 'Min';
    const max = priceMax.value > 0 ? `₱${priceMax.value.toLocaleString()}` : 'Max';
    return `${min} - ${max}`;
  }
  return 'Select price range';
});

// Reset price range
const resetPriceRange = () => {
  priceMin.value = 0;
  priceMax.value = 0;
};

// Handle search
const handleSearch = () => {
  let totalGuests = 2;
  let totalRooms = 1;
  
  try {
    const guestSelector = guestSelectorRef.value;
    if (guestSelector && guestSelector.guests) {
      const guestsData = guestSelector.guests.value || guestSelector.guests;
      totalGuests = (guestsData.adults || 0) + (guestsData.children || 0);
      totalRooms = guestsData.rooms || 1;
    }
  } catch (err) {
    console.log('GuestSelector access error:', err);
  }

  emit('search', {
    checkIn: checkInDate.value,
    checkOut: checkOutDate.value,
    guests: totalGuests,
    rooms: totalRooms,
    priceMin: priceMin.value,
    priceMax: priceMax.value
  });
};

// Close calendars when clicking outside
const closeCalendars = (e) => {
  try {
    const calendarWrapper = document.querySelector('.calendar-absolute-wrapper');
    const inputField = document.querySelector('.booking-section-dates');
    
    const isClickOnInput = inputField && inputField.contains(e.target);
    const isClickOnCalendar = calendarWrapper && calendarWrapper.contains(e.target);
    
    if (!isClickOnInput && !isClickOnCalendar) {
      dateCalendarOpen.value = false;
    }
  } catch (err) {
    // Silently ignore errors
  }
};

// Expose methods for parent
defineExpose({
  guestSelectorRef,
  resetPriceRange
});
</script>

<style scoped>
.booking-bar-container {
  position: relative;
}

.calendar-absolute-wrapper {
  position: absolute;
  top: 100%;
  left: 0;
  z-index: 50;
  margin-left: 24px;
  width: calc(100% - 3rem);
  max-width: 850px;
  margin-top: -2px;
}
</style>