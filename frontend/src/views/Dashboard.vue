<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  login as apiLogin,
  logout as apiLogout,
  fetchMe,
  fetchAuteurs,
  fetchCategories,
  fetchLivres,
  fetchUsers,
} from '@/services/api'

const router = useRouter()

const credentials = reactive({ email: '', password: '' })
const error = ref('')
const loading = ref(false)

const currentUser = ref<any>(null)
const isAuthenticated = ref(false)

const auteurs = ref<Array<{ id: number; nom: string; prenom: string }>>([])
const categories = ref<Array<{ id: number; nom_categorie: string }>>([])
const livres = ref<Array<{ id: number; titre: string }>>([])
const users = ref<Array<{ id: number; nom: string }>>([])

function isLoggedIn(): boolean {
  return isAuthenticated.value
}

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    await apiLogin(credentials.email, credentials.password)
    isAuthenticated.value = true
    await fetchAll()
    router.push('/dashboard')
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Identifiants invalides'
  } finally {
    loading.value = false
  }
}

async function handleLogout() {
  try {
    await apiLogout()
  } finally {
    isAuthenticated.value = false
    currentUser.value = null
    router.push('/login')
  }
}

async function fetchAll() {
  try {
    const [meRes, aRes, cRes, lRes, uRes] = await Promise.all([
      fetchMe().catch(() => null),
      fetchAuteurs().catch(() => ({ data: [] })),
      fetchCategories().catch(() => ({ data: [] })),
      fetchLivres().catch(() => ({ data: [] })),
      fetchUsers().catch(() => ({ data: [] })),
    ])
    currentUser.value = meRes?.data || null
    auteurs.value = aRes.data
    categories.value = cRes.data
    livres.value = lRes.data
    users.value = uRes.data
  } catch {
    // ignore
  }
}

onMounted(fetchAll)
</script>

<template>
  <div class="dashboard">
    <aside class="sidebar">
      <h2>Bibliotheque</h2>
      <nav>
        <router-link to="/dashboard">Home</router-link>
        <router-link to="/auteurs">Auteurs</router-link>
        <router-link to="/categories">Categories</router-link>
        <router-link to="/livres">Livres</router-link>
        <router-link to="/emprunts">Emprunts</router-link>
        <router-link to="/stock">Stock</router-link>
        <router-link to="/ressources">Ressources</router-link>
      </nav>
      <button v-if="isLoggedIn()" @click="handleLogout" class="logout-btn">Deconnexion</button>
    </aside>
    <div class="content">
      <header class="header">
        <h1>Tableau de bord</h1>
        <p v-if="currentUser">Connecté : <strong>{{ currentUser.nom }}</strong> ({{ currentUser.role?.nom_role }})</p>
      </header>

      <section class="grid">
        <router-link to="/auteurs" class="card">
          <h3>Auteurs</h3>
          <p class="count">{{ auteurs.length }} enregistrés</p>
        </router-link>

        <router-link to="/categories" class="card">
          <h3>Categories</h3>
          <p class="count">{{ categories.length }} enregistrées</p>
        </router-link>

        <router-link to="/livres" class="card">
          <h3>Livres</h3>
          <p class="count">{{ livres.length }} enregistrés</p>
        </router-link>

        <router-link to="/emprunts" class="card">
          <h3>Emprunts</h3>
          <p class="count">Gérer les prêts</p>
        </router-link>

        <router-link to="/stock" class="card">
          <h3>Stock</h3>
          <p class="count">Gérer le stock</p>
        </router-link>

        <router-link to="/ressources" class="card">
          <h3>Ressources</h3>
          <p class="count">Ressources numériques</p>
        </router-link>
      </section>
    </div>
  </div>
</template>

<style scoped>
.dashboard { display: flex; min-height: 100vh; font-family: sans-serif; }
.sidebar { width: 220px; background: #1e293b; color: #fff; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
.sidebar h2 { font-size: 1.3rem; margin-bottom: .5rem; }
.sidebar nav { display: flex; flex-direction: column; gap: .4rem; }
.sidebar nav a { color: #cbd5e1; text-decoration: none; padding: .5rem .75rem; border-radius: 6px; }
.sidebar nav a:hover { background: #334155; }
sidebar nav a.router-link-active { background: #3b82f6; }
.logout-btn { margin-top: auto; background: #ef4444; color: #fff; border: none; padding: .5rem; border-radius: 6px; cursor: pointer; }
.content { flex: 1; padding: 2rem; }
.header { margin-bottom: 2rem; }
.header h1 { font-size: 1.8rem; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; }
.card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.5rem; text-decoration: none; color: inherit; transition: box-shadow .15s; }
.card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.card h3 { margin: 0 0 .5rem; font-size: 1.1rem; }
.count { color: #64748b; font-size: .9rem; margin: 0; }
</style>
