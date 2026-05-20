<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { fetchLivres, fetchStocks, createStock, updateStock, deleteStock } from '@/services/api'

const form = reactive({
  quantite_totale: 0,
  quantite_disponible: 0,
  seuil_alerte: 2,
  livre_id: null as number | null,
})
const editingId = ref<number | null>(null)
const list = ref<Array<any>>([])
const livresList = ref<Array<{ id: number; titre: string }>>([])
const msg = ref('')
const err = ref('')

async function load() {
  const [{ data: stocks }, { data: livres }] = await Promise.all([
    fetchStocks().catch(() => ({ data: [] })),
    fetchLivres().catch(() => ({ data: [] })),
  ])
  list.value = stocks
  livresList.value = livres
}

async function submit() {
  msg.value = ''
  err.value = ''
  try {
    if (editingId.value) {
      await updateStock(editingId.value, form)
      msg.value = 'Stock mis à jour.'
    } else {
      await createStock(form)
      msg.value = 'Stock créé.'
    }
    editingId.value = null
    form.quantite_totale = 0
    form.quantite_disponible = 0
    form.seuil_alerte = 2
    form.livre_id = null
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat().join(', ') || 'Erreur.'
  }
}

function edit(s: typeof list.value[0]) {
  editingId.value = s.id
  form.quantite_totale = s.quantite_totale
  form.quantite_disponible = s.quantite_disponible
  form.seuil_alerte = s.seuil_alerte
  form.livre_id = s.livre_id
}

function cancel() {
  editingId.value = null
  form.quantite_totale = 0
  form.quantite_disponible = 0
  form.seuil_alerte = 2
  form.livre_id = null
}

async function remove(id: number) {
  if (!confirm('Supprimer ce stock ?')) return
  try {
    await deleteStock(id)
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || 'Erreur.'
  }
}

onMounted(load)
</script>

<template>
  <div class="form-page">
    <h1>Gestion du Stock</h1>
    <div v-if="msg" class="success">{{ msg }}</div>
    <div v-if="err" class="error">{{ err }}</div>

    <form class="data-form" @submit.prevent="submit">
      <h2>{{ editingId ? 'Modifier' : 'Créer' }} un stock</h2>
      <div class="row">
        <label>Livre
          <select v-model="form.livre_id" required>
            <option :value="null">— Choisir —</option>
            <option v-for="l in livresList" :key="l.id" :value="l.id">{{ l.titre }}</option>
          </select>
        </label>
        <label>Quantité totale <input type="number" v-model.number="form.quantite_totale" min="0" required /></label>
        <label>Quantité disponible <input type="number" v-model.number="form.quantite_disponible" min="0" required /></label>
        <label>Seuil alerte <input type="number" v-model.number="form.seuil_alerte" min="0" /></label>
      </div>
      <div class="btn-row">
        <button type="submit">{{ editingId ? 'Mettre à jour' : 'Créer' }}</button>
        <button v-if="editingId" type="button" @click="cancel" class="secondary">Annuler</button>
      </div>
    </form>

    <table class="table">
      <thead><tr><th>ID</th><th>Livre</th><th>Total</th><th>Disponible</th><th>Seuil alerte</th><th>Actions</th></tr></thead>
      <tbody>
        <tr v-for="s in list" :key="s.id">
          <td>{{ s.id }}</td>
          <td>{{ s.livre?.titre }}</td>
          <td>{{ s.quantite_totale }}</td>
          <td :class="s.quantite_disponible <= s.seuil_alerte ? 'low' : ''">{{ s.quantite_disponible }}</td>
          <td>{{ s.seuil_alerte }}</td>
          <td>
            <button @click="edit(s)" class="small">Éditer</button>
            <button @click="remove(s.id)" class="small danger">Supprimer</button>
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
.row select, .row input { padding: .5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
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
.low { color: #ef4444; font-weight: bold; }
</style>
