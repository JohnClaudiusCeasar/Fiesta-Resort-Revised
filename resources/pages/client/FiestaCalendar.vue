<template>
  <div class="fiesta-calendar-container">
    <!-- Calendar Popup Modal -->
    <div v-if="isOpen" class="fiesta-calendar-popup">
      <div class="fiesta-calendar-content">
        <!-- Header with Month/Year and Navigation -->
        <div class="calendar-header">
          <button
            @click="prevMonth"
            class="nav-button"
            aria-label="Previous month"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
          </button>

          <h2 class="month-name">
            {{ monthName }}
          </h2>

          <button
            @click="nextMonth"
            class="nav-button"
            aria-label="Next month"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5L15.75 12l-7.5 7.5" />
            </svg>
          </button>
        </div>

        <!-- Day Labels -->
        <div class="day-labels">
          <div v-for="label in dayLabels" :key="label" class="day-label">
            {{ label }}
          </div>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid">
          <button
            v-for="(day, index) in calendarDays"
            :key="index"
            @click="selectDate(day)"
            :disabled="!day"
            :class="[
              'calendar-day',
              {
                'is-selected': isSelected(day),
                'is-empty': !day
              }
            ]"
          >
            <span v-if="day">{{ day }}</span>
          </button>
        </div>

        <!-- Selected Date Display -->
        <div class="selected-date-display">
          <p class="selected-date-label">Selected Date</p>
          <p class="selected-date-value">
            {{ selectedDate.toLocaleDateString('en-US', {
              weekday: 'short',
              year: 'numeric',
              month: 'short',
              day: 'numeric',
            }) }}
          </p>
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
import { ref, computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  isOpen: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'update:isOpen', 'date-selected']);

// Initialize with today's date
const currentDate = ref(new Date());
const selectedDate = ref(new Date());

const dayLabels = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'];

const monthName = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric',
  });
});

const getDaysInMonth = (date) => {
  return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
};

const getFirstDayOfMonth = (date) => {
  return new Date(date.getFullYear(), date.getMonth(), 1).getDay();
};

const calendarDays = computed(() => {
  const daysInMonth = getDaysInMonth(currentDate.value);
  const firstDay = getFirstDayOfMonth(currentDate.value);
  const daysArray = Array.from({ length: daysInMonth }, (_, i) => i + 1);
  const emptyDays = Array.from({ length: firstDay }, (_, i) => null);
  return [...emptyDays, ...daysArray];
});

const isSelected = (day) => {
  if (!day) return false;
  return (
    day === selectedDate.value.getDate() &&
    selectedDate.value.getMonth() === currentDate.value.getMonth() &&
    selectedDate.value.getFullYear() === currentDate.value.getFullYear()
  );
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

const selectDate = (day) => {
  if (day) {
    selectedDate.value = new Date(
      currentDate.value.getFullYear(),
      currentDate.value.getMonth(),
      day
    );
    // Update currentDate to match for visual consistency
    currentDate.value = new Date(selectedDate.value);
  }
};

const cancel = () => {
  emit('update:isOpen', false);
};

const confirm = () => {
  const formattedDate = selectedDate.value.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
  
  // Emit all three events for maximum compatibility
  emit('update:modelValue', formattedDate);
  emit('date-selected', formattedDate);
  emit('update:isOpen', false);
};
</script>

<style scoped>
.fiesta-calendar-container {
  position: relative;
}

/* Calendar Popup Overlay */
.fiesta-calendar-popup {
  position: fixed;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.fiesta-calendar-content {
  background: white;
  border-radius: 1.5rem;
  padding: 1.5rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 360px;
  width: 90%;
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* Calendar Header */
.calendar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.nav-button {
  padding: 0.5rem;
  border-radius: 0.5rem;
  background-color: transparent;
  border: none;
  cursor: pointer;
  color: #4b5563;
  transition: all 0.2s ease-out;
}

.nav-button:hover {
  background-color: #f0f9ff;
  color: #00B4FF;
}

.month-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  letter-spacing: -0.025em;
  min-width: 10rem;
  text-align: center;
  margin: 0;
}

/* Day Labels */
.day-labels {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.day-label {
  text-align: center;
  font-size: 0.75rem;
  font-weight: 600;
  color: #4b5563;
  padding: 0.75rem;
  background: linear-gradient(to bottom right, #f9fafb, #f3f4f6);
  border-radius: 0.5rem;
}

/* Calendar Grid */
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.calendar-day {
  position: relative;
  height: 3rem;
  border-radius: 0.5rem;
  font-weight: 500;
  font-size: 0.875rem;
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

.calendar-day.is-selected {
  background: linear-gradient(135deg, #00B4FF, #0099D8);
  color: white;
  box-shadow: 0 10px 15px -3px rgba(0, 180, 255, 0.2);
  font-weight: 600;
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