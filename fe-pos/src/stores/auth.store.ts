import { logoutApi, meApi } from "@/api/auth.api";
import type { User } from "@/types/user";
import { defineStore } from "pinia";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        loading: false,
        isAuthenticated: !!localStorage.getItem('token')
    }),

    actions:{
        async fetchUser(){
            const res = await meApi()

            this.user = res.data.data
        },
        async logou(){
            try {
                await logoutApi()
            } finally {
                localStorage.removeItem('token')
                this.user = null
                this.isAuthenticated = false
            }
        }
    }
})
