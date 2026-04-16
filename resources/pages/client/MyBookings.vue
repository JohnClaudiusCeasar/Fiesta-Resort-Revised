<template>
  <div class="min-h-screen bg-gray-50 font-sans text-gray-800">
    <header class="bg-white py-4 px-4 md:px-8 shadow-sm z-20 sticky top-0">
      <div class="flex items-center justify-between">
        <div class="flex items-center flex-1">
          <Link href="/">
            <h1 class="text-[2.5rem] font-bold tracking-tight">
              <span class="text-[#00B4FF]">Fiesta</span> <span class="text-black">Resort</span>
            </h1>
          </Link>
        </div>
        
        <div class="flex items-center justify-end flex-1">
          <Link href="/" class="text-lg font-medium text-gray-600 hover:text-[#00B4FF] transition-colors flex items-center gap-2">
            &larr; Back to Dashboard
          </Link>
        </div>
      </div>
    </header>

    <main class="max-w-6xl mx-auto py-12 px-4">
      <h2 class="text-3xl font-bold mb-8">My Reservations</h2>

      <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-gray-800">All Rooms</h3>
        </div>

        <div class="bg-white rounded-2xl p-4 mb-6 shadow-sm border border-gray-100">
          <div class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
              <CalendarModal 
                ref="calendarModalRef"
                mode="range"
                :initialCheckIn="filters.checkIn"
                :initialCheckOut="filters.checkOut"
                @update:checkIn="handleCheckInUpdate"
                @update:checkOut="handleCheckOutUpdate"
              />
            </div>
            <div class="w-auto">
              <label class="block text-xs text-gray-500 mb-1 font-medium">Guests</label>
              <div class="flex items-center gap-2">
                <button 
                  @click="decrementGuests"
                  class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                  :disabled="filters.guests <= 1"
                >
                  <span class="text-sm">−</span>
                </button>
                <span class="w-6 text-center font-medium">{{ filters.guests }}</span>
                <button 
                  @click="incrementGuests"
                  class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                >
                  <span class="text-sm">+</span>
                </button>
              </div>
            </div>
            <button 
              @click="clearFilters"
              class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200"
            >
              Clear
            </button>
          </div>
        </div>

        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
          <div 
            v-for="room in filteredRooms" 
            :key="room.id"
            class="shrink-0 w-72 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all"
            :class="{ 'opacity-60': !isRoomAvailable(room) }"
          >
            <div class="relative h-36 bg-gray-200">
              <img 
                v-if="room.photo" 
                :src="room.photo" 
                :alt="room.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center text-4xl">
                🏨
              </div>
              <span 
                class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs font-semibold"
                :class="statusBadgeClasses[room.status]"
              >
                {{ formatStatus(room.status) }}
              </span>
            </div>
            
            <div class="p-4">
              <div class="mb-2">
                <p class="text-xs text-gray-400 font-medium uppercase">Room {{ room.number }}</p>
                <h4 class="font-bold text-gray-800">{{ room.name }}</h4>
                <p class="text-sm text-gray-500">{{ room.type }} &bull; Max {{ room.capacity }} guests</p>
              </div>
              
              <div class="flex items-center justify-between mb-3">
                <div>
                  <span class="text-lg font-bold text-gray-800">${{ room.price_per_night }}</span>
                  <span class="text-sm text-gray-500">/night</span>
                </div>
                <span v-if="room.discount" class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded">
                  {{ room.discount }}% off
                </span>
              </div>
              
              <button 
                @click="bookRoom(room)"
                :disabled="!isRoomAvailable(room)"
                class="w-full py-2 rounded-lg font-semibold text-sm transition-colors"
                :class="isRoomAvailable(room) 
                  ? 'bg-[#00B4FF] hover:bg-[#009CE0] text-white' 
                  : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
              >
                {{ isRoomAvailable(room) ? 'Book Now' : 'Unavailable' }}
              </button>
            </div>
          </div>
        </div>

        <div v-if="filteredRooms.length === 0" class="bg-white p-8 text-center rounded-xl border border-gray-100">
          <p class="text-gray-500">No rooms available.</p>
        </div>
      </section>

      <section v-if="bookings.length > 0">
        <h3 class="text-xl font-bold text-gray-800 mb-6">My Reservations</h3>

        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            class="px-4 py-2 rounded-lg font-medium whitespace-nowrap transition-colors"
            :class="activeTab === tab.id 
              ? 'bg-[#00B4FF] text-white' 
              : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'"
          >
            {{ tab.label }}
            <span class="ml-1 text-sm opacity-80">({{ tab.count }})</span>
          </button>
        </div>

        <div class="space-y-4">
          <div 
            v-for="booking in filteredBookings" 
            :key="booking.id"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow"
          >
            <div class="flex flex-col md:flex-row">
              <div class="md:w-48 h-40 md:h-auto bg-gray-200 relative">
                <img 
                  v-if="booking.room?.photo" 
                  :src="booking.room.photo" 
                  :alt="booking.room.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                  <span class="text-4xl">🏨</span>
                </div>
              </div>
              
              <div class="flex-1 p-5">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                  <div>
                    <div class="flex items-center gap-3 mb-2">
                      <span class="text-sm font-medium text-gray-500">
                        {{ booking.booking_reference || `#${booking.id}` }}
                      </span>
                      <span 
                        class="px-2.5 py-1 rounded-full text-xs font-medium"
                        :class="statusClasses[booking.status]"
                      >
                        {{ booking.status }}
                      </span>
                      <span 
                        v-if="booking.payment_status"
                        class="px-2.5 py-1 rounded-full text-xs font-medium"
                        :class="booking.payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                      >
                        {{ booking.payment_status === 'paid' ? 'Paid' : 'Pending Payment' }}
                      </span>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">
                      {{ booking.room?.name || 'Room' }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-3">
                      {{ booking.room?.type }} &bull; {{ booking.room?.capacity }} Guest{{ booking.room?.capacity > 1 ? 's' : '' }}
                    </p>
                    
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                      <div class="flex items-center gap-1.5">
                        <span>📅</span>
                        <span>{{ formatDate(booking.check_in) }} - {{ formatDate(booking.check_out) }}</span>
                      </div>
                      <div class="flex items-center gap-1.5">
                        <span>⏱️</span>
                        <span>{{ calculateNights(booking.check_in, booking.check_out) }} Night{{ calculateNights(booking.check_in, booking.check_out) > 1 ? 's' : '' }}</span>
                      </div>
                      <div class="flex items-center gap-1.5">
                        <span>👥</span>
                        <span>{{ booking.guest_count }} Guest{{ booking.guest_count > 1 ? 's' : '' }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex flex-col items-start md:items-end gap-3">
                    <div class="text-right">
                      <p class="text-sm text-gray-500">Total Price</p>
                      <p class="text-xl font-bold text-gray-900">
                        ${{ booking.total_price || calculateTotal(booking) }}
                      </p>
                    </div>
                    
                    <div class="flex gap-2 flex-wrap">
                      <button 
                        v-if="booking.status !== 'Cancelled' && booking.status !== 'Checked-Out'"
                        @click="showCancelModal(booking)"
                        class="px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        Cancel
                      </button>
                      <button 
                        class="px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors border border-gray-200"
                      >
                        Details
                      </button>
                    </div>
                  </div>
                </div>
                
                <div v-if="booking.notes" class="mt-4 pt-4 border-t border-gray-100">
                  <p class="text-sm text-gray-500">
                    <span class="font-medium">Notes:</span> {{ booking.notes }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <div v-if="filteredBookings.length === 0" class="bg-white p-8 text-center rounded-xl border border-gray-100">
            <p class="text-gray-500">No {{ activeTab }} bookings found.</p>
          </div>
        </div>
      </section>

      <div v-if="bookings.length === 0" class="bg-white p-12 text-center rounded-2xl shadow-sm border border-gray-100">
        <div class="text-6xl mb-4">🌴</div>
        <h3 class="text-xl font-semibold mb-2">No bookings yet</h3>
        <p class="text-gray-500">Select a room above to make your first reservation!</p>
      </div>
    </main>

    <Transition name="fade">
      <div v-if="cancelModal.show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="cancelModal.show = false">
        <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-xl">
          <h3 class="text-lg font-semibold mb-2">Cancel Booking?</h3>
          <p class="text-gray-600 mb-4">Are you sure you want to cancel this booking? This action cannot be undone.</p>
          <div class="flex gap-3 justify-end">
            <button 
              @click="cancelModal.show = false"
              class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            >
              Keep Booking
            </button>
            <button 
              @click="confirmCancel"
              class="px-4 py-2 bg-red-500 text-white hover:bg-red-600 rounded-lg transition-colors"
            >
              Cancel Booking
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <BookingModal 
      :show="bookingModal.show"
      :room="bookingModal.room"
      :initial-check-in="bookingModal.checkIn"
      :initial-check-out="bookingModal.checkOut"
      @close="closeBookingModal"
      @booking-created="onBookingCreated"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import BookingModal from './BookingModal.vue';
import CalendarModal from './CalendarModal.vue';

const props = defineProps({
  bookings: Array,
  rooms: Array,
  selectedRoom: Object,
  checkIn: String,
  checkOut: String
});

const page = usePage();

const today = computed(() => {
  return new Date().toISOString().split('T')[0];
});

const calendarModalRef = ref(null);

const handleCheckInUpdate = (dateStr) => {
  filters.value.checkIn = formatToISO(dateStr);
};

const handleCheckOutUpdate = (dateStr) => {
  filters.value.checkOut = formatToISO(dateStr);
};

const formatToISO = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toISOString().split('T')[0];
};

const bookingModal = ref({
  show: false,
  room: null,
  checkIn: '',
  checkOut: ''
});

const filters = ref({
  checkIn: '',
  checkOut: '',
  guests: 2
});

const statusBadgeClasses = {
  'available': 'bg-green-100 text-green-700',
  'unavailable': 'bg-gray-200 text-gray-600',
  'occupied': 'bg-orange-100 text-orange-700',
  'reserved': 'bg-blue-100 text-blue-700'
};

const formatStatus = (status) => {
  const statusMap = {
    'available': 'Available',
    'unavailable': 'Unavailable',
    'occupied': 'Occupied',
    'reserved': 'Reserved'
  };
  return statusMap[status] || status;
};

const isRoomAvailable = (room) => {
  return room.status === 'available';
};

const filteredRooms = computed(() => {
  if (!props.rooms) return [];
  
  let rooms = [...props.rooms];
  
  if (filters.value.guests > 0) {
    rooms = rooms.filter(room => room.capacity >= filters.value.guests);
  }
  
  return rooms;
});

const incrementGuests = () => {
  filters.value.guests++;
};

const decrementGuests = () => {
  if (filters.value.guests > 1) {
    filters.value.guests--;
  }
};

const clearFilters = () => {
  filters.value = {
    checkIn: '',
    checkOut: '',
    guests: 2
  };
};

onMounted(() => {
  if (props.selectedRoom) {
    bookingModal.value = {
      show: true,
      room: props.selectedRoom,
      checkIn: props.checkIn || '',
      checkOut: props.checkOut || ''
    };
  }
});

const bookRoom = (room) => {
  bookingModal.value = {
    show: true,
    room: room,
    checkIn: filters.value.checkIn,
    checkOut: filters.value.checkOut
  };
};

const closeBookingModal = () => {
  bookingModal.value.show = false;
};

const onBookingCreated = () => {
  router.reload({ only: ['bookings'] });
};

const activeTab = ref('all');

const statusClasses = {
  'Pending': 'bg-yellow-100 text-yellow-700',
  'Confirmed': 'bg-blue-100 text-blue-700',
  'Checked-In': 'bg-green-100 text-green-700',
  'Checked-Out': 'bg-gray-100 text-gray-600',
  'Cancelled': 'bg-red-100 text-red-700'
};

const tabs = computed(() => [
  { id: 'all', label: 'All', count: props.bookings.length },
  { id: 'upcoming', label: 'Upcoming', count: props.bookings.filter(b => ['Pending', 'Confirmed', 'Checked-In'].includes(b.status) && new Date(b.check_in) >= new Date()).length },
  { id: 'past', label: 'Past', count: props.bookings.filter(b => b.status === 'Checked-Out' || (b.status !== 'Cancelled' && new Date(b.check_out) < new Date())).length },
  { id: 'cancelled', label: 'Cancelled', count: props.bookings.filter(b => b.status === 'Cancelled').length }
]);

const filteredBookings = computed(() => {
  switch (activeTab.value) {
    case 'upcoming':
      return props.bookings.filter(b => 
        ['Pending', 'Confirmed', 'Checked-In'].includes(b.status) && 
        new Date(b.check_in) >= new Date()
      );
    case 'past':
      return props.bookings.filter(b => 
        b.status === 'Checked-Out' || 
        (b.status !== 'Cancelled' && new Date(b.check_out) < new Date())
      );
    case 'cancelled':
      return props.bookings.filter(b => b.status === 'Cancelled');
    default:
      return props.bookings;
  }
});

const cancelModal = ref({
  show: false,
  booking: null
});

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const calculateNights = (checkIn, checkOut) => {
  const start = new Date(checkIn);
  const end = new Date(checkOut);
  return Math.ceil((end - start) / (1000 * 60 * 60 * 24));
};

const calculateTotal = (booking) => {
  if (!booking.room?.price_per_night) return '0.00';
  const nights = calculateNights(booking.check_in, booking.check_out);
  const price = parseFloat(booking.room.price_per_night);
  const discount = booking.room.discount || 0;
  const discountedPrice = price - (price * discount / 100);
  return (discountedPrice * nights).toFixed(2);
};

const showCancelModal = (booking) => {
  cancelModal.value = { show: true, booking };
};

const confirmCancel = () => {
  if (cancelModal.value.booking) {
    router.patch(`/admin/bookings/${cancelModal.value.booking.id}/status`, {
      status: 'Cancelled'
    }, {
      onSuccess: () => {
        cancelModal.value.show = false;
      }
    });
  }
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

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
