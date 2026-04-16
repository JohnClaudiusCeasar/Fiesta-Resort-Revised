<template>
  <div class="calendar-modal-wrapper">
    <!-- Date Input Trigger -->
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
        @click="toggleCalendar"
        placeholder="Select check-in and check-out dates"
        readonly
        class="w-full bg-white border border-gray-200 rounded-xl pl-10 pr-4 py-3 outline-none cursor-pointer hover:border-[#00B4FF] transition-colors text-gray-700 font-medium placeholder-gray-400 placeholder:text-sm" 
      />
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF] absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
      </svg>
    </div>

    <!-- Calendar Popup Modal -->
    <div v-if="isOpen" class="calendar-popup">
      <div class="calendar-content">
        <!-- Dual Calendar Container -->
        <div class="dual-calendar-container">
          <!-- Left Calendar (Current Month) -->
          <div class="calendar-panel">
            <!-- Header -->
            <div class="calendar-header">
              <button v-if="canGoPrevious" @click="prevMonth" class="nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
              </button>
              <span v-else class="nav-placeholder"></span>
              <h2 class="month-name">{{ monthName }}</h2>
              <span class="nav-placeholder"></span>
            </div>

            <!-- Day Labels -->
            <div class="day-labels">
              <div v-for="label in dayLabels" :key="label" class="day-label">{{ label }}</div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
              <button
                v-for="(day, index) in calendarDays"
                :key="'left-' + index"
                @click="selectDate(day, 'current')"
                :disabled="!day || isPastDate(day, 'current')"
                :class="[
                  'calendar-day',
                  {
                    'is-selected': isSelected(day),
                    'is-check-in': isCheckInDay(day, 'current'),
                    'is-check-out': isCheckOutDay(day, 'current'),
                    'is-in-range': isInRange(day, 'current'),
                    'is-empty': !day,
                    'is-past': isPastDate(day, 'current')
                  }
                ]"
              >
                <span v-if="day">{{ day }}</span>
              </button>
            </div>
          </div>

          <!-- Right Calendar (Next Month) -->
          <div class="calendar-panel">
            <!-- Header -->
            <div class="calendar-header">
              <span class="nav-placeholder"></span>
              <h2 class="month-name">{{ nextMonthName }}</h2>
              <button @click="nextMonth" class="nav-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
              </button>
            </div>

            <!-- Day Labels -->
            <div class="day-labels">
              <div v-for="label in dayLabels" :key="label" class="day-label">{{ label }}</div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
              <button
                v-for="(day, index) in nextMonthDays"
                :key="'right-' + index"
                @click="selectDate(day, 'next')"
                :disabled="!day || isPastDate(day, 'next')"
                :class="[
                  'calendar-day',
                  {
                    'is-selected': isSelected(day),
                    'is-check-in': isCheckInDay(day, 'next'),
                    'is-check-out': isCheckOutDay(day, 'next'),
                    'is-in-range': isInRange(day, 'next'),
                    'is-empty': !day,
                    'is-past': isPastDate(day, 'next')
                  }
                ]"
              >
                <span v-if="day">{{ day }}</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Selected Date Display -->
        <div class="selected-date-display">
          <template v-if="mode === 'range'">
            <p class="selected-date-label">{{ selectingCheckIn ? 'Select Check-in Date' : 'Select Check-out Date' }}</p>
            <p class="selected-date-value">
              <span v-if="checkInDate">{{ checkInDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
              <span v-if="checkOutDate"> - {{ checkOutDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) }}</span>
              <span v-if="!checkInDate && !checkOutDate" class="text-gray-400">Click to select dates</span>
            </p>
          </template>
          <template v-else>
            <p class="selected-date-label">Selected Date</p>
            <p class="selected-date-value">
              {{ selectedDate.toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
              }) }}
            </p>
          </template>
        </div>

        <!-- Action Buttons -->
        <div class="calendar-actions">
          <button @click="cancel" class="btn-cancel">
            Cancel
          </button>
          <button @click="confirm" class="btn-confirm">
            Confirm
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  isOpen: {
    type: Boolean,
    default: false
  },
  // New props for date range
  initialCheckIn: {
    type: String,
    default: ''
  },
  initialCheckOut: {
    type: String,
    default: ''
  },
  mode: {
    type: String,
    default: 'single' // 'single' or 'range'
  }
});

const emit = defineEmits(['update:modelValue', 'update:isOpen', 'date-selected', 'update:checkIn', 'update:checkOut']);

// Calendar visibility state (internal)
const isOpen = ref(false);

// Watch for prop changes to sync internal state
watch(() => props.isOpen, (newVal) => {
  isOpen.value = newVal;
});

// Close modal when clicking outside
const handleClickOutside = (event) => {
  if (!isOpen.value) return;
  
  const wrapper = document.querySelector('.calendar-modal-wrapper');
  const popup = document.querySelector('.calendar-popup');
  
  // Don't close if click is inside wrapper (trigger area) or inside popup
  if (wrapper && (wrapper.contains(event.target) || (popup && popup.contains(event.target)))) {
    return;
  }
  
  isOpen.value = false;
  emit('update:isOpen', false);
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Toggle calendar visibility
const toggleCalendar = () => {
  isOpen.value = !isOpen.value;
};

// Date range display
const dateRangeDisplay = computed(() => {
  if (checkInDate.value && checkOutDate.value) {
    return `${formatDate(checkInDate.value)} - ${formatDate(checkOutDate.value)}`;
  } else if (checkInDate.value) {
    return `${formatDate(checkInDate.value)}`;
  }
  return '';
});

// Format date helper
const formatDate = (date) => {
  if (!date) return '';
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

// Initialize with today's date
const today = ref(new Date());

// Date range state
const checkInDate = ref(null);
const checkOutDate = ref(null);
const selectingCheckIn = ref(true); // true = waiting for check-in, false = waiting for check-out

// Parse initial values if provided
watch(() => props.initialCheckIn, (val) => {
  if (val) {
    checkInDate.value = new Date(val);
  }
}, { immediate: true });

watch(() => props.initialCheckOut, (val) => {
  if (val) {
    checkOutDate.value = new Date(val);
  }
}, { immediate: true });
const currentDate = ref(new Date());
const nextMonthDate = computed(() => {
  const date = new Date(currentDate.value);
  date.setMonth(date.getMonth() + 1);
  return date;
});
const selectedDate = ref(new Date());

const dayLabels = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];

const monthName = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric',
  });
});

const nextMonthName = computed(() => {
  return nextMonthDate.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric',
  });
});

const canGoPrevious = computed(() => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const viewDate = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth(), 1);
  const todayMonth = new Date(today.getFullYear(), today.getMonth(), 1);
  return viewDate > todayMonth;
});

const getDaysInMonth = (date) => {
  return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
};

const getFirstDayOfMonth = (date) => {
  return new Date(date.getFullYear(), date.getMonth(), 1).getDay();
};

// Check if a date is in the past - supports both calendars
const isPastDate = (day, monthCal = 'current') => {
  if (!day) return false;
  const baseDate = monthCal === 'next' ? nextMonthDate.value : currentDate.value;
  const dateToCheck = new Date(
    baseDate.getFullYear(),
    baseDate.getMonth(),
    day
  );
  // Set time to midnight for accurate comparison
  dateToCheck.setHours(0, 0, 0, 0);
  const todayMidnight = new Date(today.value);
  todayMidnight.setHours(0, 0, 0, 0);
  return dateToCheck < todayMidnight;
};

const calendarDays = computed(() => {
  const daysInMonth = getDaysInMonth(currentDate.value);
  const firstDay = getFirstDayOfMonth(currentDate.value);
  const daysArray = Array.from({ length: daysInMonth }, (_, i) => i + 1);
  const emptyDays = Array.from({ length: firstDay }, (_, i) => null);
  return [...emptyDays, ...daysArray];
});

// Next month calendar days
const nextMonthDays = computed(() => {
  const daysInMonth = getDaysInMonth(nextMonthDate.value);
  const firstDay = getFirstDayOfMonth(nextMonthDate.value);
  const daysArray = Array.from({ length: daysInMonth }, (_, i) => i + 1);
  const emptyDays = Array.from({ length: firstDay }, (_, i) => null);
  return [...emptyDays, ...daysArray];
});

const isSelected = (day) => {
  if (!day) return false;
  // Single mode
  if (props.mode === 'single') {
    return (
      day === selectedDate.value.getDate() &&
      selectedDate.value.getMonth() === currentDate.value.getMonth() &&
      selectedDate.value.getFullYear() === currentDate.value.getFullYear()
    );
  }
  return false;
};

// Range mode helpers
// Check-in day helper - checks both current and next month calendars
const isCheckInDay = (day, monthCal = 'current') => {
  if (!day || !checkInDate.value) return false;
  const baseDate = monthCal === 'next' ? nextMonthDate.value : currentDate.value;
  const checkDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), day);
  return checkDate.toDateString() === checkInDate.value.toDateString();
};

// Check-out day helper - checks both current and next month calendars
const isCheckOutDay = (day, monthCal = 'current') => {
  if (!day || !checkOutDate.value) return false;
  const baseDate = monthCal === 'next' ? nextMonthDate.value : currentDate.value;
  const checkDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), day);
  return checkDate.toDateString() === checkOutDate.value.toDateString();
};

// In-range helper - checks both calendars
const isInRange = (day, monthCal = 'current') => {
  if (!day || !checkInDate.value || !checkOutDate.value) return false;
  const baseDate = monthCal === 'next' ? nextMonthDate.value : currentDate.value;
  const checkDate = new Date(baseDate.getFullYear(), baseDate.getMonth(), day);
  return checkDate > checkInDate.value && checkDate < checkOutDate.value;
};

const prevMonth = () => {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() - 1
  );
};

const nextMonth = () => {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() + 1
  );
};

const selectDate = (day, monthCal = 'current') => {
  if (day) {
    const baseDate = monthCal === 'next' ? nextMonthDate.value : currentDate.value;
    const isPast = isPastDate(day, monthCal);
    if (isPast) return;
    
    const clickedDate = new Date(
      baseDate.getFullYear(),
      baseDate.getMonth(),
      day
    );
    
    if (props.mode === 'range') {
      // Range mode: first click = check-in, second click = check-out
      if (selectingCheckIn.value) {
        // First click - set check-in, reset check-out
        checkInDate.value = clickedDate;
        checkOutDate.value = null;
        selectingCheckIn.value = false;
      } else {
        // Second click - must be after check-in
        if (clickedDate > checkInDate.value) {
          checkOutDate.value = clickedDate;
          selectingCheckIn.value = true;
        } else {
          // If clicked before check-in, treat as new check-in
          checkInDate.value = clickedDate;
          checkOutDate.value = null;
        }
      }
    } else {
      // Single mode - existing behavior
      selectedDate.value = clickedDate;
      currentDate.value = new Date(selectedDate.value);
    }
  }
};

const cancel = () => {
  checkInDate.value = null;
  checkOutDate.value = null;
  selectingCheckIn.value = true;
  isOpen.value = false;
  emit('update:isOpen', false);
};

const confirm = () => {
  if (props.mode === 'range' && checkInDate.value && checkOutDate.value) {
    // Range mode - emit both check-in and check-out
    const checkInFormatted = checkInDate.value.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
    const checkOutFormatted = checkOutDate.value.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
    
    emit('update:checkIn', checkInFormatted);
    emit('update:checkOut', checkOutFormatted);
    emit('update:modelValue', `${checkInFormatted} - ${checkOutFormatted}`);
    emit('date-selected', { checkIn: checkInFormatted, checkOut: checkOutFormatted });
  } else if (props.mode === 'range' && checkInDate.value && !checkOutDate.value) {
    // Only check-in selected in range mode
    const checkInFormatted = checkInDate.value.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
    
    emit('update:checkIn', checkInFormatted);
    emit('update:checkOut', '');
    emit('update:modelValue', checkInFormatted);
  } else {
    // Single mode - existing behavior
    const formattedDate = selectedDate.value.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
    
    emit('update:modelValue', formattedDate);
    emit('date-selected', formattedDate);
  }
  
  emit('update:isOpen', false);
  isOpen.value = false;
};
</script>

<style scoped>
.calendar-modal-wrapper {
  position: relative;
  width: 100%;
}

/* Calendar Dropdown Popup */
.calendar-popup {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 0.5rem;
  background-color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  animation: slideDown 0.2s ease-out;
  border-radius: 1.5rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 720px;
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

.calendar-content {
  background: white;
  border-radius: 1.5rem;
  padding: 1rem;
  max-width: 700px;
  width: 100%;
  animation: slideDown 0.3s ease-out;
  overflow: hidden;
}

/* Dual Calendar Container */
.dual-calendar-container {
  display: flex;
  gap: 1rem;
}

.calendar-panel {
  flex: 1;
  min-width: 250px;
}

.calendar-panel .calendar-header {
  justify-content: center;
}

.calendar-panel .day-labels {
  margin-bottom: 0;
}

/* Calendar Header */
.calendar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
}

.nav-button {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
  border-radius: 0.5rem;
  background-color: transparent;
  border: none;
  cursor: pointer;
  color: #599bf9;
  transition: all 0.2s ease-out;
  min-width: 2rem;
  min-height: 2rem;
}

.nav-button:hover {
  background-color: #f0f9ff;
  color: #00B4FF;
}

.nav-placeholder {
  width: 2rem;
  height: 2rem;
}

.month-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  letter-spacing: -0.025em;
  min-width: 10rem;
  text-align: center;
  margin: 0;
  flex: 1;
}

/* Day Labels */
.day-labels {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.25rem;
}

.day-label {
  text-align: center;
  font-size: 0.7rem;
  font-weight: 600;
  color: white;
  padding: 0;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #78d7ff, #b8e8fd);
  border-radius: 0.4rem;
  margin-bottom: 1.5rem;
}

/* Calendar Grid */
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.calendar-day {
  position: relative;
  height: 2.2rem;
  border-radius: 0.4rem;
  font-weight: 500;
  font-size: 0.8rem;
  border: none;
  transition: all 0.2s ease-out;
  cursor: pointer;
  color: #374151;
}

.calendar-day:not(.is-empty):hover {
  transform: scale(1.05);
  background-color: #f0f9ff;
  color: #00B4FF;
}

.calendar-day:not(.is-empty):active {
  transform: scale(0.95);
}

.calendar-day.is-empty {
  cursor: default;
  color: #d1d5db;
}

.calendar-day.is-past {
  color: #9ca3af;
  cursor: not-allowed;
  background-color: #e7e7e7;
}

.calendar-day.is-past:hover {
  transform: none;
  background-color: #e7e7e7;
  color: #9ca3af;
}

.calendar-day.is-selected {
  background: linear-gradient(135deg, #00B4FF, #0099D8);
  color: white;
  box-shadow: 0 10px 15px -3px rgba(0, 180, 255, 0.2);
  font-weight: 600;
}

.calendar-day.is-check-in {
  background: linear-gradient(135deg, #00B4FF, #0099D8) !important;
  color: white !important;
  border-radius: 0.5rem 0 0 0.5rem;
  font-weight: 600;
}

.calendar-day.is-check-out {
  background: linear-gradient(135deg, #00B4FF, #0099D8) !important;
  color: white !important;
  border-radius: 0 0.5rem 0.5rem 0;
  font-weight: 600;
}

.calendar-day.is-in-range {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
  color: #1d4ed8 !important;
  border-radius: 0;
}

.calendar-day.is-check-in.is-check-out {
  border-radius: 0.5rem;
}

/* Selected Date Display */
.selected-date-display {
  padding: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
  margin-bottom: 1.5rem;
}

.selected-date-label {
  font-size: 0.75rem;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
  margin: 0 0 0.5rem 0;
}

.selected-date-value {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

/* Action Buttons */
.calendar-actions {
  display: flex;
  gap: 1rem;
}

.btn-cancel,
.btn-confirm {
  flex: 1;
  padding: 0.75rem;
  border-radius: 0.5rem;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
}

.btn-cancel {
  background-color: white;
  color: #4b5563;
  border: 2px solid #d1d5db;
}

.btn-cancel:hover {
  background-color: #f9fafb;
}

.btn-confirm {
  background-color: #00B4FF;
  color: white;
}

.btn-confirm:hover {
  background-color: #0099D8;
}

.btn-confirm:active {
  transform: scale(0.98);
}
</style>