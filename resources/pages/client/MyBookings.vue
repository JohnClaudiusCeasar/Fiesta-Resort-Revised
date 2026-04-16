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

    <main class="max-w-5xl mx-auto py-12 px-4">
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold">My Reservations</h2>
        <button 
          @click="openNewBookingModal"
          class="bg-[#00B4FF] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#009CE0] transition-colors flex items-center gap-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
          New Booking
        </button>
      </div>

      <div v-if="bookings.length === 0" class="bg-white p-12 text-center rounded-2xl shadow-sm border border-gray-100">
        <div class="text-6xl mb-4">🌴</div>
        <h3 class="text-xl font-semibold mb-2">No bookings yet</h3>
        <p class="text-gray-500 mb-6">Looks like you haven't booked a stay with us yet.</p>
        <Link href="/#booking" class="bg-[#00B4FF] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#009CE0] transition-colors">
          Explore Rooms
        </Link>
      </div>

      <div v-else>
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
      @close="closeBookingModal"
      @booking-created="onBookingCreated"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import BookingModal from './BookingModal.vue';

const props = defineProps({
  bookings: Array
});

const page = usePage();

const bookingModal = ref({
  show: false,
  room: null
});

onMounted(() => {
  if (page.props.selectedRoom) {
    bookingModal.value = {
      show: true,
      room: page.props.selectedRoom
    };
  }
});

const openNewBookingModal = () => {
  bookingModal.value = {
    show: true,
    room: null
  };
};

const closeBookingModal = () => {
  bookingModal.value.show = false;
};

const onBookingCreated = () => {
  // Refresh bookings after creating a new booking
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
</style>