import { createRouter, createWebHistory } from 'vue-router'

const authEnabled = import.meta.env.VITE_AUTH_ENABLED !== 'false'

const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', name: 'login', component: () => import('@/views/LoginView.vue') },
  { path: '/register', name: 'register', component: () => import('@/views/RegisterView.vue') },
  { path: '/dashboard', name: 'dashboard', component: () => import('@/views/Dashboard.vue') },
  { path: '/auteurs', name: 'auteur-form', component: () => import('@/views/AuteurForm.vue') },
  { path: '/categories', name: 'categorie-form', component: () => import('@/views/CategorieForm.vue') },
  { path: '/livres', name: 'livre-form', component: () => import('@/views/LivreForm.vue') },
  { path: '/emprunts', name: 'emprunt-form', component: () => import('@/views/EmpruntForm.vue') },
  { path: '/stock', name: 'stock-form', component: () => import('@/views/StockForm.vue') },
  { path: '/ressources', name: 'ressource-form', component: () => import('@/views/RessourceForm.vue') },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router
