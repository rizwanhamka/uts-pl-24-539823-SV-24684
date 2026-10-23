import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import BukuView from '../views/BukuView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', name: 'login', component: LoginView },
    { path: '/', name: 'buku', component: BukuView, meta: { requiresAuth: true } },
  ],
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')
  if (to.meta.requiresAuth && !token) return { name: 'login' }
  if (to.name === 'login' && token) return { name: 'buku' }
})

export default router
