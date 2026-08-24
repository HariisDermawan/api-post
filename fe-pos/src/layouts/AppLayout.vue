<script setup lang="ts">
import { RouterView } from 'vue-router';
import AppSidbar from './AppSidbar.vue';
</script>

<template>
    <div class="min-h-screen bg-surface-50 flex">
        <!-- sidebar -->
         <AppSidbar />
         <div class="flax-1 ml-64 transition-all duration-300">
            <div class="p-8 w-full">
                <RouterView />
            </div>
         </div>
    </div>
</template>
