<template>
  <Transition name="fade">
    <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="closeModal">
      <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Book Your Stay</h2>
            <button @click="closeModal" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div v-if="successMessage" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-3">
              <span class="text-2xl">🎉</span>
              <div>
                <p class="font-semibold text-green-800">{{ successMessage }}</p>
              </div>
            </div>
          </div>

          <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-red-700">{{ errorMessage }}</p>
          </div>

          <form v-if="!successMessage" @submit.prevent="submitBooking">
            <div class="mb-6">
              <div class="bg-gray-50 rounded-xl p-4 flex gap-4">
                <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                  <img 
                    v-if="room?.photo" 
                    :src="room.photo" 
                    :alt="room.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center text-3xl">
                    🏨
                  </div>
                </div>
                <div class="flex-1">
                  <span class="inline-block px-2 py-1 bg-[#00B4FF] text-white text-xs font-semibold rounded-full mb-1">
                    {{ room?.type || 'Room' }}
                  </span>
                  <h3 class="font-bold text-gray-800">{{ room?.name || 'Select a Room' }}</h3>
                  <p class="text-sm text-gray-500">Room {{ room?.number }}</p>
                  <p class="text-sm text-gray-600 mt-1">
                    <span class="font-semibold">${{ room?.price_per_night || 0 }}</span>/night
                    <span v-if="room?.discount" class="text-green-600 ml-2">{{ room.discount }}% off</span>
                  </p>
                </div>
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Select Dates</label>
              <div class="flex gap-3">
                <div class="flex-1">
                  <label class="block text-xs text-gray-500 mb-1">Check-in</label>
                  <input 
                    type="date" 
                    v-model="form.check_in"
                    :min="today"
                    @change="calculatePrice"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#00B4FF]"
                    required
                  />
                </div>
                <div class="flex-1">
                  <label class="block text-xs text-gray-500 mb-1">Check-out</label>
                  <input 
                    type="date" 
                    v-model="form.check_out"
                    :min="form.check_in || today"
                    @change="calculatePrice"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#00B4FF]"
                    required
                  />
                </div>
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests</label>
              <div class="flex items-center gap-4">
                <button 
                  type="button"
                  @click="decrementGuests"
                  class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                  :disabled="form.guest_count <= 1"
                >
                  <span class="text-xl">−</span>
                </button>
                <span class="text-xl font-semibold w-8 text-center">{{ form.guest_count }}</span>
                <button 
                  type="button"
                  @click="incrementGuests"
                  class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                  :disabled="form.guest_count >= (room?.capacity || 10)"
                >
                  <span class="text-xl">+</span>
                </button>
                <span class="text-sm text-gray-500 ml-2">
                  Max: {{ room?.capacity || 'N/A' }} guests
                </span>
              </div>
            </div>

            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Special Requests (Optional)</label>
              <textarea 
                v-model="form.notes"
                rows="3"
                placeholder="Any special requests or preferences..."
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#00B4FF] resize-none"
              ></textarea>
            </div>

            <div v-if="nights > 0" class="mb-6 bg-blue-50 rounded-xl p-4">
              <h3 class="font-semibold text-gray-800 mb-3">Price Breakdown</h3>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600">${{ room?.price_per_night || 0 }} × {{ nights }} night{{ nights > 1 ? 's' : '' }}</span>
                  <span class="text-gray-800">${{ subtotal.toFixed(2) }}</span>
                </div>
                <div v-if="room?.discount" class="flex justify-between text-green-600">
                  <span>Discount ({{ room.discount }}%)</span>
                  <span>-${{ discountAmount.toFixed(2) }}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between font-bold text-base">
                  <span class="text-gray-800">Total</span>
                  <span class="text-[#00B4FF]">${{ totalPrice.toFixed(2) }}</span>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-3">
                Payment Status: <span class="font-semibold text-yellow-600">Pending</span> - You will be notified once your booking is confirmed.
              </p>
            </div>

            <div class="flex gap-3">
              <button 
                type="button"
                @click="closeModal"
                class="flex-1 px-4 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-colors"
              >
                Cancel
              </button>
              <button 
                type="submit"
                :disabled="isSubmitting || !isFormValid"
                class="flex-1 px-4 py-3 bg-[#00B4FF] hover:bg-[#009CE0] text-white rounded-xl font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="isSubmitting">Processing...</span>
                <span v-else>Confirm Booking</span>
              </button>
            </div>
          </form>

          <div v-else class="mt-4">
            <button 
              @click="goToMyBookings"
              class="w-full px-4 py-3 bg-[#00B4FF] hover:bg-[#009CE0] text-white rounded-xl font-semibold transition-colors"
            >
              View My Bookings
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  room: {
    type: Object,
    default: null
  },
  initialCheckIn: {
    type: String,
    default: ''
  },
  initialCheckOut: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['close', 'booking-created']);

const today = computed(() => {
  return new Date().toISOString().split('T')[0];
});

const form = ref({
  room_id: '',
  check_in: '',
  check_out: '',
  guest_count: 1,
  notes: ''
});

const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

watch(() => props.show, (newVal) => {
  if (newVal) {
    successMessage.value = '';
    errorMessage.value = '';
    
    if (props.room) {
      form.value.room_id = props.room.id;
    }
    
    if (props.initialCheckIn) {
      form.value.check_in = props.initialCheckIn;
    }
    if (props.initialCheckOut) {
      form.value.check_out = props.initialCheckOut;
    }
    
    calculatePrice();
  }
});

watch(() => props.room, (newRoom) => {
  if (newRoom) {
    form.value.room_id = newRoom.id;
    calculatePrice();
  }
});

const nights = computed(() => {
  if (!form.value.check_in || !form.value.check_out) return 0;
  const checkIn = new Date(form.value.check_in);
  const checkOut = new Date(form.value.check_out);
  const diffTime = checkOut - checkIn;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  return diffDays > 0 ? diffDays : 0;
});

const subtotal = computed(() => {
  if (!props.room || !nights.value) return 0;
  return parseFloat(props.room.price_per_night) * nights.value;
});

const discountAmount = computed(() => {
  if (!props.room?.discount || !subtotal.value) return 0;
  return subtotal.value * (props.room.discount / 100);
});

const totalPrice = computed(() => {
  return subtotal.value - discountAmount.value;
});

const isFormValid = computed(() => {
  return form.value.room_id && 
         form.value.check_in && 
         form.value.check_out && 
         nights.value > 0 &&
         form.value.guest_count >= 1;
});

const calculatePrice = () => {
  // Price is calculated automatically via computed properties
};

const incrementGuests = () => {
  const maxGuests = props.room?.capacity || 10;
  if (form.value.guest_count < maxGuests) {
    form.value.guest_count++;
  }
};

const decrementGuests = () => {
  if (form.value.guest_count > 1) {
    form.value.guest_count--;
  }
};

const closeModal = () => {
  emit('close');
};

const goToMyBookings = () => {
  router.get('/my-bookings');
};

const submitBooking = () => {
  if (!isFormValid.value) return;
  
  isSubmitting.value = true;
  errorMessage.value = '';
  
  router.post('/bookings', {
    room_id: form.value.room_id,
    check_in: form.value.check_in,
    check_out: form.value.check_out,
    guest_count: form.value.guest_count,
    notes: form.value.notes || null
  }, {
    onSuccess: (page) => {
      isSubmitting.value = false;
      if (page.props.flash?.success) {
        successMessage.value = page.props.flash.success;
        form.value = {
          room_id: '',
          check_in: '',
          check_out: '',
          guest_count: 1,
          notes: ''
        };
        emit('booking-created');
      }
    },
    onError: (errors) => {
      isSubmitting.value = false;
      if (errors.error) {
        errorMessage.value = errors.error;
      } else {
        errorMessage.value = 'Something went wrong. Please try again.';
      }
    }
  });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
