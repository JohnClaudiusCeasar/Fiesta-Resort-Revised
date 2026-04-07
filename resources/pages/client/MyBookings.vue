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
      <h2 class="text-3xl font-bold mb-8">My Reservations</h2>

      <div v-if="bookings.length === 0" class="bg-white p-12 text-center rounded-2xl shadow-sm border border-gray-100">
        <div class="text-6xl mb-4">🌴</div>
        <h3 class="text-xl font-semibold mb-2">No bookings yet</h3>
        <p class="text-gray-500 mb-6">Looks like you haven't booked a stay with us yet.</p>
        <Link href="/#booking" class="bg-[#00B4FF] text-white px-6 py-3 rounded-lg font-medium hover:bg-[#009CE0] transition-colors">
          Explore Rooms
        </Link>
      </div>

      <div v-else class="space-y-4">
         </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  bookings: Array
});
</script>