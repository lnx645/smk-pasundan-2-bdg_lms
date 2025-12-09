<script setup lang="ts">
import { ref } from 'vue';
defineProps<{
  ytId : string
}>()
// State untuk melacak status loading
const isLoaded = ref(false);

// Fungsi ini dipanggil otomatis saat iframe selesai loading kontennya
const onIframeLoad = () => {
  isLoaded.value = true;
};
const onError = ()=>{
    alert("Error:")
}
</script>

<template>
  <div class="relative w-full lg:w-[100%] aspect-video rounded-lg overflow-hidden bg-gray-900">
    
    <div 
      v-if="!isLoaded" 
      class="absolute inset-0 flex items-center justify-center bg-neutral-100 animate-pulse"
    >
      <div class="flex flex-col items-center gap-2">
        <svg class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-xs text-gray-500 font-medium">Loading Video Materi...</span>
      </div>
    </div>
    <iframe
        @error="onError"
      @load="onIframeLoad"
      class="w-full h-full object-cover transition-opacity duration-700 ease-in-out"
      :class="isLoaded ? 'opacity-100' : 'opacity-0'"
      :src="`https://www.youtube.com/embed/${ytId}?si=wYGoE1Ua8KOOsWpc`"
      title="YouTube video player"
      frameborder="0"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
      referrerpolicy="strict-origin-when-cross-origin"
      allowfullscreen
    >
    </iframe>
    
  </div>
</template>