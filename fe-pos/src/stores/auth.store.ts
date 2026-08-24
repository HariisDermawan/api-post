import { loginApi, logoutApi, meApi } from "@/api/auth.api";
import type { User } from "@/types/user";
import { defineStore } from "pinia";
import Password from 'primevue/password';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null as User | null,
        loading: false,
        isAuthenticated: !!localStorage.getItem('token')
    }),

    actions:{
        async login(email: string, password: string){
            this.loading = true

            try {
                const res = await loginApi({email, password})
                localStorage.setItem('token', res.data.data.token)

                this.isAuthenticated = true
                await this.fetchUser()
            } finally {
                this.loading = false
            }
        },
        async fetchUser(){
            const res = await meApi()

            this.user = res.data.data
        },
        async logout(){
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
