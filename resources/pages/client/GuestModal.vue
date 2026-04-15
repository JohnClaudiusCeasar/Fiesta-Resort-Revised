<template>
  <div class="guest-selector-wrapper">
    <!-- Trigger Button -->
    <button
      @click="isOpen = true"
      class="guest-selector-trigger"
    >
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
      </svg>
      <span class="text-gray-700 font-medium">
        {{ displayText }}
      </span>
    </button>

    <!-- Dropdown Menu -->
    <div v-if="isOpen" class="guest-selector-dropdown">
      <div class="guest-selector-content">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">Select Guests</h2>

        <!-- Adults Counter -->
        <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-200">
          <div>
            <h3 class="text-lg font-bold text-gray-800">Adults</h3>
          </div>
          <div class="flex items-center gap-4">
            <button
              @click="decrementAdults"
              class="counter-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
              </svg>
            </button>
            <input
              v-model.number="guests.adults"
              type="number"
              min="0"
              class="counter-input"
              @change="validateAdults"
            />
            <button
              @click="incrementAdults"
              class="counter-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Children Counter -->
        <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-200">
          <div>
            <h3 class="text-lg font-bold text-gray-800">Children</h3>
          </div>
          <div class="flex items-center gap-4">
            <button
              @click="decrementChildren"
              class="counter-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
              </svg>
            </button>
            <input
              v-model.number="guests.children"
              type="number"
              min="0"
              class="counter-input"
              @change="validateChildren"
            />
            <button
              @click="incrementChildren"
              class="counter-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Rooms Counter -->
        <div class="flex items-center justify-between mb-8 pb-8 border-b border-gray-200">
          <div>
            <h3 class="text-lg font-bold text-gray-800">Rooms</h3>
          </div>
          <div class="flex items-center gap-4">
            <button
              @click="decrementRooms"
              class="counter-btn"
              :disabled="guests.rooms <= 1"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
              </svg>
            </button>
            <input
              v-model.number="guests.rooms"
              type="number"
              min="1"
              class="counter-input"
              @change="validateRooms"
            />
            <button
              @click="incrementRooms"
              class="counter-btn"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Pet-friendly Checkbox -->
        <div class="flex items-center gap-3 mb-8">
          <input
            v-model="guests.petFriendly"
            type="checkbox"
            id="pet-friendly"
            class="w-5 h-5 rounded border-2 border-gray-300 cursor-pointer accent-[#00B4FF]"
          />
          <label for="pet-friendly" class="cursor-pointer flex flex-col">
            <span class="text-lg font-bold text-gray-800">Pet-friendly</span>
            <span class="text-sm text-gray-500">Only show stays that allow pets</span>
          </label>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 pt-4">
          <button
            @click="resetSelections"
            class="flex-1 px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50 transition-colors"
          >
            RESET
          </button>
          <button
            @click="applySelections"
            class="flex-1 px-6 py-3 bg-[#00B4FF] hover:bg-[#009CE0] text-white rounded-lg font-bold transition-colors"
          >
            Apply
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const isOpen = ref(false);

const guests = ref({
  adults: 2,
  children: 0,
  rooms: 1,
  petFriendly: false
});

const initialGuests = {
  adults: 2,
  children: 0,
  rooms: 1,
  petFriendly: false
};

// Display text for trigger button
const displayText = computed(() => {
  const total = guests.value.adults + guests.value.children;
  let text = `${total} ${total === 1 ? 'Guest' : 'Guests'}, ${guests.value.rooms} ${guests.value.rooms === 1 ? 'Room' : 'Rooms'}`;
  if (guests.value.petFriendly) {
    text += ', Pet-friendly';
  }
  return text;
});

// Adults counter
const incrementAdults = () => {
  guests.value.adults++;
};

const decrementAdults = () => {
  if (guests.value.adults > 0) {
    guests.value.adults--;
  }
};

const validateAdults = () => {
  if (guests.value.adults < 0) guests.value.adults = 0;
};

// Children counter
const incrementChildren = () => {
  guests.value.children++;
};

const decrementChildren = () => {
  if (guests.value.children > 0) {
    guests.value.children--;
  }
};

const validateChildren = () => {
  if (guests.value.children < 0) guests.value.children = 0;
};

// Rooms counter
const incrementRooms = () => {
  guests.value.rooms++;
};

const decrementRooms = () => {
  if (guests.value.rooms > 1) {
    guests.value.rooms--;
  }
};

const validateRooms = () => {
  if (guests.value.rooms < 1) guests.value.rooms = 1;
};

// Modal actions
const resetSelections = () => {
  guests.value = { ...initialGuests };
};

const applySelections = () => {
  isOpen.value = false;
  // Emit data to parent or handle submission
  console.log('Guest selection applied:', guests.value);
};

// Close dropdown when clicking outside
const closeOnOutside = (e) => {
  const wrapper = document.querySelector('.guest-selector-wrapper');
  if (wrapper && !wrapper.contains(e.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', closeOnOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', closeOnOutside);
});

// Expose guests data for parent component
defineExpose({
  guests
});
</script>

<style scoped>
.guest-selector-wrapper {
  position: relative;
}

.guest-selector-trigger {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.75rem;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.guest-selector-trigger:hover {
  border-color: #00B4FF;
  background-color: #f9fafb;
}

.guest-selector-trigger:focus {
  outline: none;
  border-color: #00B4FF;
  box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.1);
}

/* Dropdown Styles */
.guest-selector-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 0.5rem;
  background-color: white;
  z-index: 50;
  animation: slideDown 0.2s ease-out;
  border-radius: 1.5rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  width: 100%;
  max-width: 400px;
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

.guest-selector-content {
  background: white;
  border-radius: 1.5rem;
  padding: 1.5rem;
  max-width: 400px;
  width: 100%;
  overflow: hidden;
}

.counter-btn {
  width: 2.5rem;
  height: 2.5rem;
  border: 2px solid #e0f2fe;
  border-radius: 50%;
  background-color: white;
  color: #00B4FF;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.counter-btn:hover:not(:disabled) {
  border-color: #00B4FF;
  background-color: #e0f2fe;
  transform: scale(1.05);
}

.counter-btn:active:not(:disabled) {
  transform: scale(0.95);
}

.counter-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.counter-input {
  width: 4rem;
  text-align: center;
  padding: 0.5rem 0.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  outline: none;
}

.counter-input:focus {
  border-color: #00B4FF;
  box-shadow: 0 0 0 3px rgba(0, 180, 255, 0.1);
}

/* Remove number input spinner */
.counter-input::-webkit-outer-spin-button,
.counter-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.counter-input[type=number] {
  -moz-appearance: textfield;
}
</style>