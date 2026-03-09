<template>
  <div class="min-h-screen bg-white font-sans text-gray-800">
    <header class="bg-white py-4 px-4 md:px-8 shadow-sm z-20 relative flex items-center justify-between">
      <div class="flex items-center">
        <h1 class="text-[2.5rem] font-bold tracking-tight">
          <span class="text-[#00B4FF]">Fiesta</span> <span class="text-black">Resort</span>
        </h1>
      </div>

      <nav class="hidden lg:flex items-center gap-10 text-lg font-medium text-gray-600">
        <a href="#" class="hover:text-[#00B4FF] transition-colors">Home</a>
        <a href="#" class="hover:text-[#00B4FF] transition-colors">Rooms</a>
        <a href="#" class="hover:text-[#00B4FF] transition-colors">My Bookings</a>
        <a href="#" class="hover:text-[#00B4FF] transition-colors">About</a>
        <a href="#" class="hover:text-[#00B4FF] transition-colors">Contact</a>
      </nav>

      <div>
        <Link href="/login" class="px-8 py-2 border-2 border-gray-200 rounded-xl text-gray-600 font-semibold hover:bg-gray-50 transition-colors">
          Login
        </Link>
      </div>
    </header>

    <main class="w-full">

        <section id="hero" class="bg-white pt-8 pb-12">
            <div class="w-full bg-[#F9FAFB] shadow-xl border-t border-b border-gray-100">
                
                <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-16 py-12 md:py-20 px-8 md:px-16">
                
                    <div class="flex-1 flex flex-col items-start space-y-20">
                        <div class="space-y-6">
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

                        <div class="flex flex-wrap gap-8 pt-6 w-full max-w-2xl">
                
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

                    <div class="flex-1 relative group w-full max-w-[850px]">
                        <div class="absolute -inset-4 bg-blue-100/30 rounded-[4rem] blur-2xl group-hover:bg-blue-200/40 transition-all duration-500"></div>
                        <img 
                        src="https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80" 
                        alt="Resort Pool" 
                        class="relative rounded-[4rem] shadow-2xl w-full object-cover h-[500px] xl:h-[600px] border-[6px] border-white transition-transform duration-500 group-hover:scale-[1.02]"
                        />
                    </div>
                </div>
            </div>
        </section>
    
        <section id="rooms" class="bg-gray-50 py-16 px-8 md:px-16">
        <div class="max-w-7xl mx-auto">
          <div class="bg-[#EBF8FF] p-8 rounded-[2rem] shadow-sm flex flex-wrap items-end gap-6 mb-20">
            <div class="flex-1 min-w-[200px]">
              <label class="block text-gray-700 font-medium mb-2 pl-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                Check In
              </label>
              <input type="date" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 outline-none" />
            </div>

            <div class="flex-1 min-w-[200px]">
              <label class="block text-gray-700 font-medium mb-2 pl-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2003/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00B4FF]">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                Check Out
              </label>
              <input type="date" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 outline-none" />
            </div>

            <div class="flex-1 min-w-[200px]">
              <label class="block text-gray-700 font-medium mb-2 pl-2 flex items-center gap-2">
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
      </section>

      <section id="about" class="h-screen bg-white"> </section>
      <section id="experience" class="h-screen bg-gray-50"> </section>
      <section id="treasures" class="h-screen bg-white"> </section>
      <section id="contact" class="h-screen bg-gray-50"> </section>

    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>