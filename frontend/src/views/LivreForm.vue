<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import {
  fetchLivres,
  fetchAuteurs,
  fetchCategories,
  fetchStockByLivre,
  createLivre,
  updateLivre,
  deleteLivre,
} from '@/services/api'

const form = reactive({
  titre: '',
  isbn: '',
  description: '',
  annee_publication: null as number | null,
  auteur_id: null as number | null,
  categorie_id: null as number | null,
})
const editingId = ref<number | null>(null)
const list = ref<Array<any>>([])
const auteursList = ref<Array<{ id: number; nom: string; prenom: string }>>([])
const categoriesList = ref<Array<{ id: number; nom_categorie: string }>>([])
const msg = ref('')
const err = ref('')
const expandedId = ref<number | null>(null)
const stockExpanded = ref<Record<number, any>>({})

async function load() {
  const [{ data: livres }, { data: auteurs }, { data: cats }] = await Promise.all([
    fetchLivres().catch(() => ({ data: [] })),
    fetchAuteurs().catch(() => ({ data: [] })),
    fetchCategories().catch(() => ({ data: [] })),
  ])
  list.value = livres
  auteursList.value = auteurs
  categoriesList.value = cats
}

async function loadStock(livreId: number) {
  try {
    const { data } = await fetchStockByLivre(livreId)
    stockExpanded.value[livreId] = data
  } catch {
    stockExpanded.value[livreId] = null
  }
}

async function submit() {
  msg.value = ''
  err.value = ''
  const payload = {
    ...form,
    auteur_id: form.auteur_id,
    categorie_id: form.categorie_id,
  }
  try {
    if (editingId.value) {
      await updateLivre(editingId.value, payload)
      msg.value = 'Livre mis à jour.'
    } else {
      await createLivre(payload)
      msg.value = 'Livre créé.'
    }
    editingId.value = null
    form.titre = ''
    form.isbn = ''
    form.description = ''
    form.annee_publication = null
    form.auteur_id = null
    form.categorie_id = null
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat().join(', ') || 'Erreur.'
  }
}

function edit(l: typeof list.value[0]) {
  editingId.value = l.id
  form.titre = l.titre
  form.isbn = l.isbn
  form.description = l.description || ''
  form.annee_publication = l.annee_publication || null
  form.auteur_id = l.auteur_id || null
  form.categorie_id = l.categorie_id || null
}

function cancel() {
  editingId.value = null
  form.titre = ''
  form.isbn = ''
  form.description = ''
  form.annee_publication = null
  form.auteur_id = null
  form.categorie_id = null
}

async function remove(id: number) {
  if (!confirm('Supprimer ce livre ?')) return
  try {
    await deleteLivre(id)
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || 'Erreur.'
  }
}

function toggleStock(id: number) {
  if (expandedId.value === id) {
    expandedId.value = null
    return
  }
  expandedId.value = id
  if (!stockExpanded.value[id]) loadStock(id)
}

onMounted(load)
</script>

<template>
  <div class="form-page">
    <h1>Gestion des Livres</h1>
    <div v-if="msg" class="success">{{ msg }}</div>
    <div v-if="err" class="error">{{ err }}</div>

    <form class="data-form" @submit.prevent="submit">
      <h2>{{ editingId ? 'Modifier' : 'Créer' }} un livre</h2>
      <div class="row">
        <label>Titre <input v-model="form.titre" required /></label>
        <label>ISBN <input v-model="form.isbn" required /></label>
      </div>
      <div class="row">
        <label>Année publication <input type="number" v-model.number="form.annee_publication" /></label>
        <label>Auteur
          <select v-model="form.auteur_id">
            <option :value="null">— Aucun —</option>
            <option v-for="a in auteursList" :key="a.id" :value="a.id">{{ a.prenom }} {{ a.nom }}</option>
          </select>
        </label>
        <label>Catégorie
          <select v-model="form.categorie_id">
            <option :value="null">— Aucune —</option>
            <option v-for="c in categoriesList" :key="c.id" :value="c.id">{{ c.nom_categorie }}</option>
          </select>
        </label>
      </div>
      <label>Description
        <textarea v-model="form.description" rows="2" style="padding:.5rem;border:1px solid #d1d5db;border-radius:6px;width:100%;font-family:inherit;"></textarea>
      </label>
      <div class="btn-row">
        <button type="submit">{{ editingId ? 'Mettre à jour' : 'Créer' }}</button>
        <button v-if="editingId" type="button" @click="cancel" class="secondary">Annuler</button>
      </div>
    </form>

    <table class="table">
      <thead><tr><th>ID</th><th>Titre</th><th>ISBN</th><th>Auteur</th><th>Catégorie</th><th>Stock</th><th>Actions</th></tr></thead>
      <tbody>
        <tr v-for="l in list" :key="l.id">
          <td>{{ l.id }}</td>
          <td>{{ l.titre }}</td>
          <td>{{ l.isbn }}</td>
          <td>{{ l.auteur?.prenom }} {{ l.auteur?.nom }}</td>
          <td>{{ l.categorie?.nom_categorie }}</td>
          <td>
            <button @click="toggleStock(l.id)" class="small">{{ expandedId === l.id ? '—' : (l.stock?.quantite_disponible ?? '—') }}</button>
          </td>
          <td>
            <button @click="edit(l)" class="small">Éditer</button>
            <button @click="remove(l.id)" class="small danger">Supprimer</button>
          </td>
        </tr>
        <tr v-if="expandedId !== null && stockExpanded[expandedId!]">
          <td colspan="7" class="stock-detail">
            <pre>{{ stockExpanded[expandedId!] || 'Aucun stock' }}</pre>
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
.row label { display: flex; flex-direction: column; gap: .3rem; flex: 1; min-width: 140px; font-size: .85rem; font-weight: 600; }
.row input, .row select, .row textarea { padding: .5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
.btn-row { display: flex; gap: .5rem; margin-top: 1rem; }
button { padding: .5rem 1rem; border: none; border-radius: 6px; cursor: pointer; background: #2563eb; color: #fff; }
.secondary { background: #64748b; }
.small { padding: .2rem .6rem; font-size: .8rem; }
.danger { background: #ef4444; }
.success { background: #f0fdf4; color: #16a34a; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.error { background: #fef2f2; color: #dc2626; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
.table th, .table td { padding: .7rem 1rem; text-align: left; border-bottom: 1px solid #f1f5f9; }
.table th { background: #f1f5f9; font-size: .85rem; }
.table tr:last-child td { border-bottom: none; }
.stock-detail { background: #f8fafc; padding: 1rem; }
.stock-detail pre { margin: 0; font-size: .85rem; }
</style>
