<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { register } from '@/services/api'

const router = useRouter()

const form = reactive({
  nom: '',
  email: '',
  password: '',
  telephone: '',
})
const error = ref('')
const loading = ref(false)

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    await register(form)
    router.push('/login')
  } catch (e: any) {
    error.value = e.response?.data?.message || "Erreur lors de l'inscription"
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="auth-page">
    <form class="auth-form" @submit.prevent="handleSubmit">
      <h1>Inscription</h1>
      <p class="subtitle">Créer un compte bibliothèque</p>
      <div v-if="error" class="error">{{ error }}</div>
      <div class="field">
        <label for="nom">Nom complet</label>
        <input id="nom" v-model="form.nom" type="text" required placeholder="Jean Dupont" />
      </div>
      <div class="field">
        <label for="reg-email">Email</label>
        <input id="reg-email" v-model="form.email" type="email" required placeholder="jean@mail.com" />
      </div>
      <div class="field">
        <label for="reg-phone">Téléphone (optionnel)</label>
        <input id="reg-phone" v-model="form.telephone" type="tel" placeholder="06 12 34 56 78" />
      </div>
      <div class="field">
        <label for="reg-password">Mot de passe</label>
        <input id="reg-password" v-model="form.password" type="password" required placeholder="••••••••" minlength="6" />
      </div>
      <button type="submit" :disabled="loading">{{ loading ? 'Inscription...' : "S'inscrire" }}</button>
      <p class="link">Déjà un compte ? <router-link to="/login">Se connecter</router-link></p>
    </form>
  </div>
</template>

<style scoped>
.auth-page { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #eef2f7; font-family: sans-serif; }
.auth-form { background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,.08); width: 380px; display: flex; flex-direction: column; gap: 1rem; }
.auth-form h1 { margin: 0 0 .25rem; font-size: 1.6rem; text-align: center; }
.subtitle { text-align: center; color: #64748b; margin: 0; font-size: .9rem; }
.field { display: flex; flex-direction: column; gap: .3rem; }
.field label { font-size: .85rem; font-weight: 600; color: #374151; }
.field input { padding: .6rem .8rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; }
.error { background: #fef2f2; color: #dc2626; padding: .6rem; border-radius: 8px; font-size: .85rem; }
button { background: #2563eb; color: #fff; border: none; padding: .7rem; border-radius: 8px; font-size: 1rem; cursor: pointer; }
button:disabled { opacity: .6; }
.link { text-align: center; font-size: .85rem; color: #64748b; }
.link a { color: #2563eb; }
</style>
