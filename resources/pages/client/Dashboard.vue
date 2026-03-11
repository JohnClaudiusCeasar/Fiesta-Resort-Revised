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
          <a href="#" class="hover:text-[#00B4FF] transition-colors">Home</a>
          <a href="#" class="hover:text-[#00B4FF] transition-colors">Rooms</a>
          <a href="#" class="hover:text-[#00B4FF] transition-colors">My Bookings</a>
          <a href="#" class="hover:text-[#00B4FF] transition-colors">About</a>
          <a href="#" class="hover:text-[#00B4FF] transition-colors">Contact</a>
        </nav>

        <div class="flex-1 flex justify-end">
          <Link href="/login" class="px-8 py-2 border-2 border-gray-200 rounded-xl text-gray-600 font-semibold hover:bg-gray-50 transition-colors">
            Login
          </Link>
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

                      <div class="flex flex-wrap gap-12 pt-6 w-full max-w-2xl">
              
                          <div class="flex items-center gap-5 group">
                              <div class="bg-[#FEE2E2] p-4 rounded-2xl shadow-sm border border-red-50 transition-transform group-hover:scale-105">
                                  <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10 text-red-400">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                  </svg>
                              </div>
                              <div class="flex flex-col">
                                  <span class="text-3xl font-black text-gray-800 leading-none">0</span>
                                  <div class="w-12 h-1 bg-red-200 my-2 rounded-full"></div>
                                  <p class="text-red-400 font-semibold italic text-sm tracking-wider uppercase">Users</p>
                              </div>
                          </div>

                          <div class="flex items-center gap-5 group">
                              <div class="bg-[#E0F2FE] p-4 rounded-2xl shadow-sm border border-blue-50 transition-transform group-hover:scale-105">
                                  <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10 text-blue-400">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                  </svg>
                              </div>

                              <div class="flex flex-col">
                                  <span class="text-3xl font-black text-gray-800 leading-none">0</span>
                                  <div class="w-12 h-1 bg-blue-200 my-2 rounded-full"></div>
                                  <p class="text-blue-400 font-semibold italic text-sm tracking-wider uppercase">Rooms</p>
                              </div>
                          </div>

                          <div class="flex items-center gap-5 group">
                              <div class="bg-[#FEF3C7] p-4 rounded-2xl shadow-sm border border-yellow-100 transition-transform group-hover:scale-105">
                                  <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10 text-yellow-500">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                  </svg>
                              </div>
                              <div class="flex flex-col">
                                  <span class="text-3xl font-black text-gray-800 leading-none">0</span>
                                  <div class="w-12 h-1 bg-yellow-200 my-2 rounded-full"></div>
                                  <p class="text-yellow-500 font-semibold italic text-sm tracking-wider uppercase">Bookings</p>
                              </div>
                          </div>
                      </div>
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
              <div class="bg-[#dff7ff] p-8 rounded-2rem shadow-sm flex flex-wrap items-end gap-6">
                <div class="flex-1 min-w-200px">
                  <label class="block text-gray-700 font-medium mb-2 pl-2 items-center gap-2">
                    <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    Check In
                  </label>
                  <input type="date" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 outline-none" />
                </div>

              <div class="flex-1 min-w-200px">
                <label class="block text-gray-700 font-medium mb-2 pl-2 items-center gap-2">
                  <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                  </svg>
                  Check Out
                </label>
                <input type="date" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 outline-none" />
              </div>

              <div class="flex-1 min-w-200px">
                <label class="text-gray-700 font-medium mb-2 pl-2 flex items-center gap-2">
                  <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                  </svg>
                  Persons
                </label>
                <select class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 outline-none appearance-none">
                  <option>2</option>
                  <option>3</option>
                </select>
              </div>

              <button class="bg-[#00B4FF] hover:bg-[#009CE0] text-white px-12 py-3.5 rounded-xl font-bold text-xl transition-all">
                Search
              </button>
            </div>
          </div>
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
                        <div class="py-12 border-2 border-dashed border-gray-200 rounded-3xl bg-white">
                            <p class="text-gray-400 font-medium italic">No rooms available at the moment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import resortPoolImage from '../assets/FiestaResort1.jpg';
import getawayImage from '../assets/FiestaResort4.jpg';
import mangroveImage from '../assets/FiestaResort5.jpg';

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
</script>