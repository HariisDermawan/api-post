<script setup>
import { useAuthStore } from '@/stores/auth.store';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const authStore = useAuthStore()
const { user } = authStore
const logoutDialog = ref(false)
const menuItems = ref([
    {
        label: "General",
        items: [
            { icon: "pi pi-th-large", to: "/", label: "Dashboard" }
        ]
    }
])
</script>
<template>
    <aside
        class="fixed left-0 top-0 z-50 flex h-screen w-64 flex-col border-r border-surface-200 bg-white transition-all duration-300">
        <div class="flex h-20 shrink-0 items-center border-b border-surface-100 px-6">
            <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm">
                <i class="pi pi-bolt text-lg"></i>
            </div>

            <div class="flex flex-col">
                <span class="text-lg font-bold leading-tight text-surface-900">
                    POS CASHIER
                </span>

                <span class="text-xs text-surface-500">
                    Point of Sale
                </span>
            </div>
        </div>
        <!-- menu -->
        <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-6">
            <div v-for="(section, i) in menuItems" :key="i">
                <div class="text-xs font-semibold text-surface-400 uppercase tracking-wider mb-3 px-3">
                    {{ section.label }}
                </div>
                <div class="flex flex-col gap-1">
                    <router-link v-for="(item, j) in section.items" :to="item.to" :key="index"
                        class="flex item-center gap-3 px-3 py-2.5 rounded-lg transition-colors duration-200"
                        :class="[
                            route.path == item.to ? 'bg-surface-100 text-primary-600' : 'text-surface-900 hover:bg-surface-100']">
                        <i :class="[item.icon, 'text-lg']"></i>
                        <span class="font-medium text-sm">{{ item.label }}</span>
                    </router-link>
                </div>
            </div>
        </div>
        <!-- User Profile -->
        <div class="p-4 border-t border-surface-200">
            <div class="flex items-center gap-3 w-full p-3 rounded-xl
               hover:bg-surface-50 transition-colors duration-200 cursor-pointer">
                <div class="flex items-center justify-center w-10 h-10
                   rounded-full bg-surface-200 overflow-hidden shrink-0">
                    <i class="pi pi-user text-lg text-surface-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-surface-900 truncate">
                        {{ user?.name }}
                    </p>
                    <p class="text-sm text-surface-900 truncate">
                        {{ user?.email }}
                    </p>
                </div>
                <div class="group flex items-center gap-3">
                    <!-- Profile -->
                    <button type="submit" 
                        class="ml-auto mt-5 flex h-8 w-8 items-center justify-center rounded-lg text-surface-400 transition-all duration-200 group-hover:bg-red-500 group-hover:text-white"
                        aria-label="Logout">
                        <i class="pi pi-sign-out text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </aside>
</template>
