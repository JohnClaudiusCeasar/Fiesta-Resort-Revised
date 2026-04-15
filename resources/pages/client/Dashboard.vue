<template>
 
  <div class="min-h-screen bg-white font-sans text-gray-800">
    <header class="bg-white py-4 px-4 md:px-8 shadow-sm z-20 sticky top-0">
      <div class="flex items-center justify-between">
        <div class="flex items-center flex-1">
          <h1 class="text-[2.5rem] font-bold tracking-tight">
            <span class="text-[#00B4FF]">Fiesta</span> <span class="text-black">Resort</span>
          </h1>
        </div>
 
        <nav class="hidden lg:flex items-center gap-10 text-lg font-medium text-gray-600 flex-1 justify-center">
          <a href="#Home" @click.prevent="scrollToSection('first-section')" class="hover:text-[#00B4FF] transition-colors cursor-pointer">Home</a>
          <a href="#Rooms" @click.prevent="scrollToSection('second-section')" class="hover:text-[#00B4FF] transition-colors cursor-pointer">Rooms</a>
          <Link href="/my-bookings" class="hover:text-[#00B4FF] transition-colors cursor-pointer">My Bookings</Link>
          <a href="#About" @click.prevent="scrollToSection('third-section')" class="hover:text-[#00B4FF] transition-colors cursor-pointer">About</a>
          <a href="#contact" @click.prevent="scrollToSection('sixth-section')" class="hover:text-[#00B4FF] transition-colors cursor-pointer">Contact</a>
        </nav>
 
        <div class="flex-1 flex justify-end">
          <div v-if="user" class="flex items-center gap-3 px-6 py-2 border-2 border-gray-200 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer" @click="showLogoutModal = true">
            <div class="w-8 h-8 bg-[#00B4FF] rounded-full flex items-center justify-center text-white font-bold text-sm">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <span class="text-gray-600 font-semibold">Welcome back, {{ user.name.split(' ').length === 2 ? user.name.split(' ')[0] : user.name.split(' ').slice(0, 2).join(' ') }}</span>
          </div>
          <Link v-else href="/login" class="px-8 py-2 border-2 border-gray-200 rounded-xl text-gray-600 font-semibold hover:bg-gray-50 transition-colors">
            Login
          </Link>
        </div>
        
        <!-- Logout Modal -->
        <div v-if="showLogoutModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white rounded-2xl p-8 max-w-sm shadow-2xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Confirm Logout</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to log out of your account?</p>
            <div class="flex gap-4">
              <button @click="showLogoutModal = false" class="flex-1 px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                Cancel
              </button>
              <Link href="/logout" method="post" as="button" @click="showLogoutModal = false" class="flex-1 px-4 py-2 bg-[#00B4FF] hover:bg-[#009CE0] text-white rounded-lg font-semibold transition-colors">
                Logout
              </Link>
            </div>
          </div>
        </div>
      </div>
</header>
  
    <main class="w-full">

      <!-- Overview Section -->
        <section id="first-section" class="bg-white pt-8 pb-12">
            <div class="w-full bg-[#F9FAFB] shadow-xl border-t border-b border-gray-100">
                
                <div class="max-w-screen-2xl mx-auto flex flex-col md:flex-row items-center gap-20 md:gap-32 py-12 md:py-20 px-2 md:px-4">
                
                  <div class="flex-1 flex flex-col items-start space-y-24">
                      <div class="space-y-4">
                          <h2 class="text-5xl md:text-2xl xl:text-5xl font-bold text-[#55A8D1] leading-tight tracking-tight">
                              Forget Busy Work,<br />Start Next Vacation
                          </h2>
                          <p class="text-gray-400 text-xl md:text-0.5xl max-w-xl leading-relaxed font-light">
                              We provide what you need to enjoy your holidays with family. Time to make another memorable moment.
                          </p>
                      </div>

                      <button class="bg-[#00B4FF] hover:bg-[#009CE0] text-white px-12 py-5 rounded-2xl text-2xl font-bold shadow-xl shadow-blue-100/50 transition-all hover:-translate-y-1 active:scale-95">
                      Show More
                      </button>
                      
                  </div>

                    <div class="flex-1 relative group w-full max-w-850px">
                        <div class="absolute -inset-4 bg-blue-100/30 rounded-[4rem] blur-2xl group-hover:bg-blue-200/40 transition-all duration-500"></div>
                        <img 
                        src="https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80" 
                        alt="Resort Pool" 
                        class="relative rounded-[4rem] shadow-2xl w-full object-cover h-500px xl:h-600px border-[6px] border-white transition-transform duration-500 group-hover:scale-[1.02]"
                        />
                    </div>
                </div>
            </div>
          </section>
    
<!-- Bookings and Rooms Section -->
        <section id="second-section" class="bg-[#ffffff]">
          <section id="booking" class="py-16 px-8 md:px-16">
            <div class="max-w-7xl mx-auto">
              <div class="bg-[#dff7ff] p-11 rounded-2rem shadow-sm flex flex-wrap justify-between items-center gap-9 relative">
                <CalendarModal 
                  ref="calendarModalRef"
                  class="flex-1"
                  mode="range"
                  @update:checkIn="checkInDate = $event"
                  @update:checkOut="checkOutDate = $event"
                />
                <GuestModal 
                  ref="guestModalRef"
                  class="flex-1"
                />
                <PriceModal 
                  ref="priceModalRef"
                  class="flex-1"
                  :priceMin="priceMin" 
                  :priceMax="priceMax"
                  @update:priceMin="priceMin = $event"
                  @update:priceMax="priceMax = $event"
                  @reset="resetPriceRange"
                />
                <button 
                  @click="handleSearchWithPrice"
                  class="bg-[#00B4FF] hover:bg-[#009CE0] text-white px-12 py-3.5 rounded-xl font-bold text-xl transition-all"
                >
                  Search
                </button>
              </div>
            </div>
          </section>
        </section>
  
        <section id="rooms" class="bg-white pt-8 pb-12">
            <div class="w-full bg-[#F9FAFB] shadow-xl border-t border-b border-gray-100">
                
                <div class="max-w-7xl mx-auto py-12 md:py-20 px-4 md:px-8">
                
                    <div class="text-center space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-4xl font-bold text-[#55A8D1]">Our Rooms</h3>
                            <button class="bg-[#00B4FF] text-white px-6 py-2 rounded-lg font-semibold">View All</button>
                        </div>
                        <p class="text-gray-400 text-lg max-w-3xl mx-auto">
                            Choose from our selection of comfortable and well-appointed rooms at Fiesta Resort, Brgy. Ipil, Surigao City.
                        </p>

                        <!-- Horizontal Scrollable Rooms Container -->
                        <div v-if="displayRooms && displayRooms.length > 0" class="py-6 overflow-x-auto scrollbar-hide">
                            <div class="flex gap-5 pb-4" style="min-width: max-content;">
                                
                                <!-- Single Room Card (Repeated for each room) -->
                                <div 
                                    v-for="room in displayRooms" 
                                    :key="room.id"
                                    class="shrink-0 w-72 bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-100"
                                >
                                    <!-- Room Photo -->
                                    <div class="relative h-40 bg-gray-200">
                                        <img 
                                            :src="room.photo || 'https://via.placeholder.com/400x200?text=No+Image'" 
                                            :alt="room.name"
                                            class="w-full h-full object-cover"
                                        />
                                        <span class="absolute top-3 right-3 bg-[#00B4FF] text-white text-xs font-bold px-3 py-1 rounded-full">
                                            {{ room.type }}
                                        </span>
                                    </div>
                                    
                                    <!-- Room Info -->
                                    <div class="p-4">
                                        <div class="mb-2">
                                            <p class="text-xs text-gray-400 font-semibold uppercase">Room {{ room.number }}</p>
                                            <h4 class="text-base font-bold text-gray-800">{{ room.name }}</h4>
                                        </div>
                                        
                                        <div class="flex items-center gap-2 text-gray-500 text-xs mb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>Up to {{ room.capacity }} guests</span>
                                        </div>
                                        
                                        <!-- Price -->
                                        <div class="flex items-center justify-between mb-3">
                                            <div v-if="room.discount && room.discount > 0">
                                                <span class="text-xs text-gray-400 line-through">₱{{ parseFloat(room.price_per_night).toLocaleString() }}</span>
                                                <span class="text-lg font-bold text-[#00B4FF] ml-1">₱{{ getDiscountedPrice(room.price_per_night, room.discount).toLocaleString() }}</span>
                                            </div>
                                            <div v-else>
                                                <span class="text-lg font-bold text-[#00B4FF]">₱{{ parseFloat(room.price_per_night).toLocaleString() }}</span>
                                                <span class="text-xs text-gray-400">/night</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Book Button -->
                                        <button 
                                            @click="bookRoom(room)"
                                            class="w-full bg-[#00B4FF] hover:bg-[#009CE0] text-white font-bold py-2.5 rounded-xl transition-colors text-sm"
                                        >
                                            Book Now
                                        </button>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <!-- Warning Message for No Results -->
                        <div v-if="hasSearched && displayRooms.length === 0" class="py-12 px-8 border-2 border-dashed border-orange-200 rounded-3xl bg-orange-50">
                            <div class="text-center">
                                <div class="text-4xl mb-3">🔍</div>
                                <p class="text-orange-700 font-semibold text-lg mb-2">No rooms match your criteria</p>
                                <p class="text-orange-600 text-sm mb-4">Try adjusting your check-in/check-out dates, guest count, or price range.</p>
                                <button 
                                    @click="resetFilters"
                                    class="px-6 py-2 bg-[#00B4FF] hover:bg-[#009CE0] text-white rounded-lg font-medium transition-colors"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </div>

                        <!-- Scroll Hint Indicator -->
                        <div class="flex justify-center gap-2 text-gray-400 text-xs mt-2" v-else-if="displayRooms && displayRooms.length > 3">
                            <span>← Scroll to see more →</span>
                        </div>

                        <div v-else-if="!hasSearched && (!rooms || rooms.length === 0)" class="py-12 border-2 border-dashed border-gray-200 rounded-3xl bg-white">
                            <p class="text-gray-400 font-medium italic">No rooms available at the moment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
  
        <!-- About Us Section -->
        <section id="third-section" class="bg-white">
          <div class="max-w-7xl mx-auto px-4 md:px-8 py-12">
            <h1 class="text-4xl font-bold text-[#55A8D1] mb-8">About Us</h1>
            
            <div class="flex gap-8 items-center">
              <!-- Image -->
              <div class="shrink-0">
                <img 
                  :src="resortPoolImage" 
                  alt="Fiesta Resort Pool"
                  class="rounded-lg shadow-lg w-96 h-64 object-cover border-4 border-blue-300"
                />
              </div>
              
              <!-- Welcome Text -->
              <div class="flex-1">
                <p class="text-gray-400 text-lg leading-relaxed">
                  Welcome to Fiesta Resort, Surigao City. Nestled along the scenic coast of Surigao City. 
                  Fiesta Resort is your perfect escape for relaxation, adventure, and authentic island hospitality.
                </p>
              </div>
            </div>
          </div>
        </section>
 
        <!-- Our Resort Experience Section -->
        <section id="fourth-section" class="bg-white">
          <div class="max-w-7xl mx-auto px-4 md:px-8 py-12">
            <h2 class="text-3xl font-bold text-[#55A8D1] mb-8">Our Resort Experience</h2>
            
            <div class="space-y-4">
              <p class="text-gray-400 text-lg">
                At Fiesta Resort, we take pride in creating a warm and relaxing atmosphere for every guest.
              </p>
              
              <p class="text-gray-400 text-lg">
                Guest can indulge in local Surigaonon cuisine at our on site restaurant, often accompanied 
                by a free breakfast to start your day bright.
              </p>
            </div>
          </div>
        </section>
 
        <!-- Discover Surigao's Natural Treasures Section -->
        <section id="fifth-section" class="bg-white">
          <div class="max-w-7xl mx-auto px-4 md:px-8 py-12">
            <h2 class="text-3xl font-bold text-[#55A8D1] mb-8">Discover Surigao's Natural Treasures</h2>
            
            <div class="flex gap-12 items-start">
              <!-- Left Content -->
              <div class="flex-1">
                <p class="text-gray-400 text-lg mb-8">
                  Fiesta Resort is more than just a place to stay – It's your gateway to Surigao's 
                  vibrant eco-tourism scene:
                </p>
                
                <div class="space-y-6">
                  <!-- Island Hopping -->
                  <div>
                    <h3 class="font-bold text-[#55A8D1] text-lg">Island Hopping:</h3>
                    <p class="text-gray-400 ml-4">
                      Explore Basul and Hikdop Islands for snorkeling, kayaking, and beach adventures.
                    </p>
                  </div>
                  
                  <!-- Mangrove Exploration -->
                  <div>
                    <h3 class="font-bold text-[#55A8D1] text-lg">Mangrove Exploration:</h3>
                    <p class="text-gray-400 ml-4">
                      Cruise through the waterways of the Day-asan Mangrove Forest.
                    </p>
                  </div>
                  
                  <!-- Inland Excursions -->
                  <div>
                    <h3 class="font-bold text-[#55A8D1] text-lg">Inland Excursions:</h3>
                    <p class="text-gray-400 ml-4">
                      Take a refreshing dip at Songkoy Cold Spring or visit the Rock and Mineral Museum
                    </p>
                  </div>
                </div>
              </div>
              
              <!-- Right Image -->
              <div class="shrink-0">
                <img 
                  :src="mangroveImage" 
                  alt="Surigao Natural Treasures"
                  class="rounded-lg shadow-lg w-96 h-80 object-cover border-4 border-blue-400"
                />
              </div>
            </div>
          </div>
        </section>
 
        <section class="bg-white">
          <div class="max-w-7xl mx-auto px-4 md:px-8 py-12">
            <!-- Relax, Explore, and Experience Local Life -->
            <div class="mb-12">
              <h2 class="text-3xl font-bold text-[#55A8D1] mb-8">Relax, Explore, and Experience Local Life</h2>
              <p class="text-gray-400 text-lg">
                Whether you're enjoying a peaceful afternoon by the pool, strolling along the Surigao City Boulevard, or discovering 
                hidden gems across the islands, Fiesta Resort provides a balance of relaxation, culture, and adventure.
              </p>
            </div>
 
            <!-- Your Surigao Getaway Awaits -->
            <div>
              <h2 class="text-3xl font-bold text-[#55A8D1] mb-8 mt-25">Your Surigao Getaway Awaits</h2>
              <p class="text-gray-400 text-lg mb-8">
                At Fiesta Resort, every day feels like a destination. Let us be your home as you discover the beauty, culture, and 
                simplistic charm of Surigao City.
              </p>
              
              <!-- Image -->
              <div class="flex justify-center">
                <img 
                  :src="getawayImage" 
                  alt="Your Surigao Getaway"
                  class="rounded-3xl shadow-lg w-full max-w-2xl h-80 object-cover border-4 border-blue-400 mt-15"
                />
              </div>
            </div>
          </div>
        </section>

      <section id="sixth-section" class="bg-white pt-8 pb-12">
        <div class="w-full bg-[#F9FAFB] shadow-xl border-t border-b border-gray-100">
          <div class="max-w-7xl mx-auto px-4 md:px-8 py-12">
            <!-- Contact Us Title -->
            <h1 class="text-5xl font-bold text-[#55A8D1] text-center mb-12">Contact Us</h1>
 
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
          <!-- Left Side - Get in Touch -->
          <div>
            <h2 class="text-3xl font-bold text-[#55A8D1] mb-4">Get in Touch</h2>
            <p class="text-gray-400 text-lg mb-8">
              We're here to help and answer any question you might have.
            </p>
 
            <!-- Address -->
            <div class="mb-8">
              <h3 class="text-xl font-bold text-[#55A8D1] italic mb-3">Address</h3>
              <p class="text-gray-400">
                Sitio Dacuman, Barangay Ipil<br />
                Surigao City, Surigao del Norte, 8400<br />
                Philippines
              </p>
            </div>
 
            <!-- Phone -->
            <div class="mb-8">
              <h3 class="text-xl font-bold text-[#55A8D1] italic mb-3">Phone</h3>
              <p class="text-gray-400">
                (+63)912-345-6789<br />
                (+63)998-765-4321
              </p>
            </div>
 
            <!-- Email -->
            <div class="mb-8">
              <h3 class="text-xl font-bold text-[#55A8D1] italic mb-3">Email</h3>
              <p class="text-gray-400">
                info@fiestasort.com<br />
                bookings@fiestasort.come
              </p>
            </div>
 
            <!-- Business Hours -->
            <div>
              <h3 class="text-xl font-bold text-[#55A8D1] italic mb-3">Business Hours</h3>
              <p class="text-gray-400">
                Monday - Friday: 9:00 AM - 6:00 PM<br />
                Saturday - Sunday: 10:00 AM - 4:00 PM
              </p>
            </div>
          </div>
 
          <!-- Right Side - Contact Form -->
          <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
            <h2 class="text-3xl font-bold text-[#55A8D1] mb-6">Send us a Message</h2>
 
            <form @submit.prevent="handleSubmit" class="space-y-6">
              <!-- Full Name -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Full Name:</label>
                <input 
                  v-model="form.fullName"
                  type="text" 
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:border-[#00B4FF] transition-colors"
                  placeholder="Enter your full name"
                />
              </div>
 
              <!-- Email -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Email:</label>
                <input 
                  v-model="form.email"
                  type="email" 
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:border-[#00B4FF] transition-colors"
                  placeholder="Enter your email"
                />
              </div>
 
              <!-- Phone Number -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Phone Number:</label>
                <input 
                  v-model="form.phoneNumber"
                  type="tel" 
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:border-[#00B4FF] transition-colors"
                  placeholder="Enter your phone number"
                />
              </div>
 
              <!-- Subject -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Subject:</label>
                <input 
                  v-model="form.subject"
                  type="text" 
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:border-[#00B4FF] transition-colors"
                  placeholder="Enter subject"
                />
              </div>
 
              <!-- Message -->
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Subject:</label>
                <textarea 
                  v-model="form.message"
                  class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:border-[#00B4FF] transition-colors h-32 resize-none"
                  placeholder="Enter your message"
                ></textarea>
              </div>
 
              <!-- Submit Button -->
              <button 
                type="submit"
                class="w-full bg-[#00B4FF] hover:bg-[#009CE0] text-white font-bold py-3 rounded-lg transition-colors"
              >
                Send a Message
              </button>
            </form>
          </div>
        </div>
      </div>
      </div>
      </section>
 
    </main>
  </div>
</template>
 
<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import resortPoolImage from '../assets/FiestaResort1.jpg';
import getawayImage from '../assets/FiestaResort4.jpg';
import mangroveImage from '../assets/FiestaResort5.jpg';
import CalendarModal from './CalendarModal.vue';
import GuestModal from './GuestModal.vue';
import PriceModal from './PriceModal.vue'; 
 
// define props to recieve data from the backend
const props = defineProps({
  user: { type: Object, default: null },
  rooms: { type: Array, default: () => []}
});
 
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId);
  if (element) {
    window.scrollTo({ top: element.offsetTop, behavior: 'smooth' });
  }
};
 
const form = ref({
  fullName: '',
  email: '',
  phoneNumber: '',
  subject: '',
  message: ''
});
 
const handleSubmit = () => {
  console.log('Form submitted:', form.value);
  // Add your form submission logic here
};

const getDiscountedPrice = (basePrice, discountPercent) => {
    const discount = (basePrice * discountPercent) / 100;
    return Math.round(basePrice - discount);
};

const bookRoom = (room) => {
    console.log('Selected room:', room);
    // TODO: Implement booking logic (scroll to form, open modal, etc.)
};
 
// Filtered rooms state
const filteredRooms = ref([]);
const hasSearched = ref(false);

// Component refs
const calendarModalRef = ref(null);
const guestModalRef = ref(null);
const priceModalRef = ref(null);

// Date state
const checkInDate = ref('');
const checkOutDate = ref('');

// Reset all filters
const resetFilters = () => {
  hasSearched.value = false;
  filteredRooms.value = [];
};

// Price range state
const priceMin = ref(0);
const priceMax = ref(0);

// Reset price range
const resetPriceRange = () => {
  priceMin.value = 0;
  priceMax.value = 0;
};

// Handle search - combine all filter data
const handleSearchWithPrice = () => {
  hasSearched.value = true;
  
  // Get guest data from GuestModal
  let totalGuests = 2;
  let totalRooms = 1;
  try {
    const guestModal = guestModalRef.value;
    if (guestModal && guestModal.guests) {
      const guestsData = guestModal.guests.value || guestModal.guests;
      totalGuests = (guestsData.adults || 0) + (guestsData.children || 0);
      totalRooms = guestsData.rooms || 1;
    }
  } catch (err) {
    console.log('GuestModal access error:', err);
  }
  
  const priceMinVal = priceMin.value;
  const priceMaxVal = priceMax.value;
  
  // Get available rooms - ensure props.rooms exists
  const allRooms = props.rooms || [];
  
  // Filter rooms based on criteria
  filteredRooms.value = allRooms.filter(room => {
    // Filter by guest capacity (room must accommodate all guests)
    const roomCapacity = room.capacity || 0;
    if (roomCapacity < totalGuests) {
      return false;
    }
    
    // Filter by price range
    const roomPrice = parseFloat(room.price_per_night) || 0;
    if (priceMinVal > 0 && roomPrice < priceMinVal) {
      return false;
    }
    if (priceMaxVal > 0 && roomPrice > priceMaxVal) {
      return false;
    }
    
    // Filter by availability - exclude only unavailable status (allow occupied/reserved to show)
    const roomStatus = room.status || '';
    if (roomStatus === 'unavailable') {
      return false;
    }
    
    return true;
  });
  
  console.log('Search triggered - Check-in:', checkInDate.value, 'Check-out:', checkOutDate.value, 'Total Guests:', totalGuests, 'Rooms:', totalRooms, 'Price Range:', priceMinVal, '-', priceMaxVal, 'Rooms found:', filteredRooms.value.length);
  
  // Scroll to rooms section
  const roomsSection = document.getElementById('rooms');
  if (roomsSection) {
    roomsSection.scrollIntoView({ behavior: 'smooth' });
  }
};

// Computed display rooms (original or filtered)
const displayRooms = computed(() => {
  // If search was performed and we have results, show filtered
  if (hasSearched.value && filteredRooms.value.length > 0) {
    return filteredRooms.value;
  }
  // Otherwise show all rooms from props
  return props.rooms || [];
});
</script>