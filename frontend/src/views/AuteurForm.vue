<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { createAuteur, updateAuteur, deleteAuteur, fetchAuteurs } from '@/services/api'

const form = reactive({ nom: '', prenom: '', nationalite: '' })
const editingId = ref<number | null>(null)
const list = ref<Array<{ id: number; nom: string; prenom: string; nationalite: string }>>([])
const msg = ref('')
const err = ref('')

async function load() {
  const { data } = await fetchAuteurs()
  list.value = data
}

async function submit() {
  msg.value = ''
  err.value = ''
  try {
    if (editingId.value) {
      await updateAuteur(editingId.value, form)
      msg.value = 'Auteur mis à jour.'
    } else {
      await createAuteur(form)
      msg.value = 'Auteur créé.'
    }
    editingId.value = null
    form.nom = ''
    form.prenom = ''
    form.nationalite = ''
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || 'Erreur.'
  }
}

function edit(a: typeof list.value[0]) {
  editingId.value = a.id
  form.nom = a.nom
  form.prenom = a.prenom
  form.nationalite = a.nationalite || ''
}

function cancel() {
  editingId.value = null
  form.nom = ''
  form.prenom = ''
  form.nationalite = ''
}

async function remove(id: number) {
  if (!confirm('Supprimer cet auteur ?')) return
  try {
    await deleteAuteur(id)
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || 'Erreur.'
  }
}

onMounted(load)
</script>

<template>
  <div class="form-page">
    <h1>Gestion des Auteurs</h1>
    <div v-if="msg" class="success">{{ msg }}</div>
    <div v-if="err" class="error">{{ err }}</div>

    <form class="data-form" @submit.prevent="submit">
      <h2>{{ editingId ? 'Modifier' : 'Créer' }} un auteur</h2>
      <div class="row">
        <label>Prénom <input v-model="form.prenom" required /></label>
        <label>Nom <input v-model="form.nom" required /></label>
        <label>Nationalité <input v-model="form.nationalite" /></label>
      </div>
      <div class="btn-row">
        <button type="submit">{{ editingId ? 'Mettre à jour' : 'Créer' }}</button>
        <button v-if="editingId" type="button" @click="cancel" class="secondary">Annuler</button>
      </div>
    </form>

    <table class="table">
      <thead><tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Nationalité</th><th>Actions</th></tr></thead>
      <tbody>
        <tr v-for="a in list" :key="a.id">
          <td>{{ a.id }}</td>
          <td>{{ a.nom }}</td>
          <td>{{ a.prenom }}</td>
          <td>{{ a.nationalite || '-' }}</td>
          <td>
            <button @click="edit(a)" class="small">Éditer</button>
            <button @click="remove(a.id)" class="small danger">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.form-page { max-width: 800px; margin: 0 auto; padding: 2rem; font-family: sans-serif; }
.data-form { background: #f8fafc; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; }
.data-form h2 { margin-top: 0; font-size: 1.1rem; }
.row { display: flex; gap: 1rem; flex-wrap: wrap; }
.row label { display: flex; flex-direction: column; gap: .3rem; flex: 1; min-width: 160px; font-size: .85rem; font-weight: 600; }
.row input { padding: .5rem; border: 1px solid #d1d5db; border-radius: 6px; }
.btn-row { display: flex; gap: .5rem; margin-top: 1rem; }
button { padding: .5rem 1rem; border: none; border-radius: 6px; cursor: pointer; background: #2563eb; color: #fff; }
.secondary { background: #64748b; }
.small { padding: .25rem .6rem; font-size: .8rem; }
.danger { background: #ef4444; }
.success { background: #f0fdf4; color: #16a34a; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.error { background: #fef2f2; color: #dc2626; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
.table th, .table td { padding: .7rem 1rem; text-align: left; border-bottom: 1px solid #f1f5f9; }
.table th { background: #f1f5f9; font-size: .85rem; }
.table tr:last-child td { border-bottom: none; }
</style>
