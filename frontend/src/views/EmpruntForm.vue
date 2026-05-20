<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import {
  fetchEmprunts,
  createEmprunt,
  retournerEmprunt,
  updateEmprunt,
  deleteEmprunt,
  fetchLivres,
  fetchUsers,
} from '@/services/api'

const form = reactive({
  user_id: null as number | null,
  livre_id: null as number | null,
  date_retour_prevue: '',
})
const editingId = ref<number | null>(null)
const list = ref<Array<any>>([])
const livresList = ref<Array<{ id: number; titre: string }>>([])
const usersList = ref<Array<{ id: number; nom: string; email: string }>>([])
const msg = ref('')
const err = ref('')

async function load() {
  const [{ data: emprunts }, { data: livres }, { data: users }] = await Promise.all([
    fetchEmprunts().catch(() => ({ data: [] })),
    fetchLivres().catch(() => ({ data: [] })),
    fetchUsers().catch(() => ({ data: [] })),
  ])
  list.value = emprunts
  livresList.value = livres
  usersList.value = users
}

async function submit() {
  msg.value = ''
  err.value = ''
  try {
    if (editingId.value) {
      await updateEmprunt(editingId.value, form)
      msg.value = 'Emprunt mis à jour.'
    } else {
      await createEmprunt(form)
      msg.value = 'Emprunt créé.'
    }
    editingId.value = null
    form.user_id = null
    form.livre_id = null
    form.date_retour_prevue = ''
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat().join(', ') || 'Erreur.'
  }
}

async function retourner(id: number) {
  try {
    await retournerEmprunt(id)
    msg.value = 'Livre retourné avec succès.'
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || 'Erreur lors du retour.'
  }
}

function edit(e: typeof list.value[0]) {
  editingId.value = e.id
  form.user_id = e.user_id
  form.livre_id = e.livre_id
  form.date_retour_prevue = e.date_retour_prevue || ''
}

function cancel() {
  editingId.value = null
  form.user_id = null
  form.livre_id = null
  form.date_retour_prevue = ''
}

async function remove(id: number) {
  if (!confirm('Supprimer cet emprunt ?')) return
  try {
    await deleteEmprunt(id)
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || 'Erreur.'
  }
}

onMounted(load)
</script>

<template>
  <div class="form-page">
    <h1>Gestion des Emprunts</h1>
    <div v-if="msg" class="success">{{ msg }}</div>
    <div v-if="err" class="error">{{ err }}</div>

    <form class="data-form" @submit.prevent="submit">
      <h2>{{ editingId ? 'Modifier' : 'Créer' }} un emprunt</h2>
      <div class="row">
        <label>Utilisateur
          <select v-model="form.user_id" required>
            <option :value="null">— Choisir —</option>
            <option v-for="u in usersList" :key="u.id" :value="u.id">{{ u.nom }} ({{ u.email }})</option>
          </select>
        </label>
        <label>Livre
          <select v-model="form.livre_id" required>
            <option :value="null">— Choisir —</option>
            <option v-for="l in livresList" :key="l.id" :value="l.id">{{ l.titre }}</option>
          </select>
        </label>
        <label>Date retour prévue <input v-model="form.date_retour_prevue" type="date" required /></label>
      </div>
      <div class="btn-row">
        <button type="submit">{{ editingId ? 'Mettre à jour' : 'Créer' }}</button>
        <button v-if="editingId" type="button" @click="cancel" class="secondary">Annuler</button>
      </div>
    </form>

    <table class="table">
      <thead><tr><th>ID</th><th>Utilisateur</th><th>Livre</th><th>Emprunté le</th><th>Retour prévu</th><th>Retour effectif</th><th>Statut</th><th>Actions</th></tr></thead>
      <tbody>
        <tr v-for="e in list" :key="e.id">
          <td>{{ e.id }}</td>
          <td>{{ e.user?.nom }}</td>
          <td>{{ e.livre?.titre }}</td>
          <td>{{ new Date(e.date_emprunt).toLocaleDateString('fr-FR') }}</td>
          <td>{{ new Date(e.date_retour_prevue).toLocaleDateString('fr-FR') }}</td>
          <td>{{ e.date_retour_effective ? new Date(e.date_retour_effective).toLocaleDateString('fr-FR') : '-' }}</td>
          <td><span :class="['badge', e.statut === 'termine' ? 'done' : 'active']">{{ e.statut }}</span></td>
          <td>
            <button v-if="e.statut !== 'termine'" @click="retourner(e.id)" class="small" title="Marquer retourné">↩ Retourner</button>
            <button @click="edit(e)" class="small">Éditer</button>
            <button @click="remove(e.id)" class="small danger">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.form-page { max-width: 900px; margin: 0 auto; padding: 2rem; font-family: sans-serif; }
.data-form { background: #f8fafc; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; }
.data-form h2 { margin-top: 0; font-size: 1.1rem; }
.row { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: .75rem; }
.row label { display: flex; flex-direction: column; gap: .3rem; flex: 1; min-width: 160px; font-size: .85rem; font-weight: 600; }
.row select, .row input { padding: .5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
.btn-row { display: flex; gap: .5rem; margin-top: 1rem; }
button { padding: .5rem 1rem; border: none; border-radius: 6px; cursor: pointer; background: #2563eb; color: #fff; }
.secondary { background: #64748b; }
.small { padding: .25rem .6rem; font-size: .8rem; }
.danger { background: #ef4444; }
.success { background: #f0fdf4; color: #16a34a; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.error { background: #fef2f2; color: #dc2626; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
.table th, .table td { padding: .6rem .75rem; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: .9rem; }
.table th { background: #f1f5f9; font-size: .85rem; font-weight: 600; }
.badge { padding: .2rem .5rem; border-radius: 99px; font-size: .75rem; }
.done { background: #dcfce7; color: #16a34a; }
.active { background: #dbeafe; color: #2563eb; }
</style>
