<template>
  <div class="min-h-screen flex flex-col font-sans bg-white">
    <!-- Header -->
    <header class="bg-white py-4 px-8 shadow-sm z-20 relative flex items-center">
      <h1 class="text-[2.5rem] font-bold tracking-tight">
        <span class="text-[#00B4FF]">Fiesta</span> <span class="text-black">Resort</span>
      </h1>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col md:flex-row relative overflow-hidden">
      <!-- Left Side - Background Image -->
      <div 
        class="hidden md:block md:w-[55%] lg:w-[60%] bg-cover bg-center"
        style="background-image: url('https://images.unsplash.com/photo-1540541338287-41700207dee6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')"
      >
      </div>

      <!-- Right Side - Form Panel -->
      <div class="w-full md:w-[45%] lg:w-[40%] bg-white shadow-[inset_12px_0_12px_-6px_rgba(0,0,0,0.5)] z-10 flex flex-col items-center py-10 px-8 sm:px-16 overflow-y-auto">
        
        <div class="text-center mb-6 w-full max-w-md">
          <h2 class="text-[1.75rem] text-black mb-1">Welcome to</h2>
          <h1 class="text-[3.5rem] font-bold leading-tight mb-2">
            <span class="text-[#00B4FF]">Fiesta</span> <span class="text-black">Resort!</span>
          </h1>
          <p class="text-[#D1D1D1] font-light text-xl italic">" Where Simplicity meets Luxury "</p>
          
          <h3 class="text-[2rem] text-black mt-8 mb-2">Get Started Now!</h3>
        </div>

        <div class="w-full max-w-md">
          <!-- Error Message -->
          <div v-if="hasError || Object.keys(form.errors).length > 0" class="bg-[#FFD6D6] text-[#FF6B6B] text-sm p-4 rounded-lg mb-6 flex items-center">
            <span class="mr-2 text-[8px]">●</span>
            <p class="leading-snug">You have Incomplete fields, please try again</p>
          </div>

          <form @submit.prevent="submit" class="space-y-5">
            <div>
              <label class="block text-black font-bold mb-2 text-sm sm:text-base">Full Name:</label>
              <input 
                type="text" 
                v-model="form.name"
                class="w-full bg-[#F5F5F5] border border-[#E0E0E0] rounded-xl px-4 py-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-[#00B4FF]"
              />
            </div>

            <div>
              <label class="block text-black font-bold mb-2 text-sm sm:text-base">Email Address:</label>
              <input 
                type="email" 
                v-model="form.email"
                class="w-full bg-[#F5F5F5] border border-[#E0E0E0] rounded-xl px-4 py-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-[#00B4FF]"
              />
            </div>

            <div>
              <label class="block text-black font-bold mb-2 text-sm sm:text-base">Password:</label>
              <input 
                type="password" 
                v-model="form.password"
                class="w-full bg-[#F5F5F5] border border-[#E0E0E0] rounded-xl px-4 py-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-[#00B4FF]"
              />
            </div>

            <div>
              <label class="block text-black font-bold mb-2 text-sm sm:text-base">Confirm Password:</label>
              <input 
                type="password" 
                v-model="form.password_confirmation"
                class="w-full bg-[#F5F5F5] border border-[#E0E0E0] rounded-xl px-4 py-3 shadow-inner focus:outline-none focus:ring-2 focus:ring-[#00B4FF]"
              />
            </div>

            <div class="flex items-center pt-2">
              <input 
                type="checkbox" 
                id="terms" 
                v-model="form.terms"
                class="w-4 h-4 border-gray-300 rounded text-[#00B4FF] focus:ring-[#00B4FF]"
              />
              <label for="terms" class="ml-2 text-gray-500 text-sm">
                I agree to the <a href="#" class="text-[#00B4FF] hover:underline">terms and policy</a>
              </label>
            </div>

            <button 
              type="submit" 
              :disabled="form.processing"
              class="w-full bg-[#00B4FF] hover:bg-[#009CE0] text-white font-semibold text-2xl py-4 rounded-xl transition-colors mt-6 disabled:opacity-50"
            >
              Register
            </button>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const hasError = ref(false);

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  if (!form.name || !form.email || !form.password || !form.password_confirmation || !form.terms) {
    hasError.value = true;
    return;
  }
  
  hasError.value = false;

  form.post('/register', {
    onError: () => {
      hasError.value = true;
      form.reset('password', 'password_confirmation');
    },
    onSuccess: () => {
      hasError.value = false;
    }
  });
};
</script>