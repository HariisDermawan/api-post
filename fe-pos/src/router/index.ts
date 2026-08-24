import { createRouter, createWebHistory } from 'vue-router'

import AppLayout from '@/layouts/AppLayout.vue'
import Dashboard from '@/pages/Dashboard.vue'
import Login from '@/pages/Auth/Login.vue'
import { useAuthStore } from '@/stores/auth.store'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {gues: true}
    },
    {
      path: '/',
      component: AppLayout,
      meta: {requiresAuth: true},
      children: [
        {
          path: '',
          name: 'dashboard',
          component: Dashboard,
        },
      ],
    },
  ],
})

router.beforeEach(async(to, from, next) => {
    const auth = useAuthStore()

    if (auth.isAuthenticated && !auth.user) {
        try {
            await auth.fetchUser()
        } catch{
            auth.logout()
            return next('/login')
        }
    }

    if (to.meta.requiresAuth &&  !auth.isAuthenticated) {
        return next('/login')
    }

    if (to.meta.gues && auth.isAuthenticated) {
        return next('/')
    }

    next()

})


export default router
