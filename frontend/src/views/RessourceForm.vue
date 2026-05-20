<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import {
  fetchRessources,
  createRessource,
  updateRessource,
  deleteRessource,
  fetchUsers,
} from '@/services/api'

const form = reactive({
  titre: '',
  fichier: '',
  type_fichier: '',
  user_id: null as number | null,
})
const editingId = ref<number | null>(null)
const list = ref<Array<any>>([])
const usersList = ref<Array<{ id: number; nom: string }>>([])
const fileInput = ref<HTMLInputElement | null>(null)
const fileName = ref('')
const msg = ref('')
const err = ref('')

async function load() {
  const [{ data: res }, { data: users }] = await Promise.all([
    fetchRessources().catch(() => ({ data: [] })),
    fetchUsers().catch(() => ({ data: [] })),
  ])
  list.value = res
  usersList.value = users
}

function onFileSelected(e: Event) {
  const target = e.target as HTMLInputElement
  if (target.files?.length) {
    fileName.value = target.files[0].name
  }
}

async function submit() {
  msg.value = ''
  err.value = ''
  const formData = new FormData()
  formData.append('titre', form.titre)
  formData.append('type_fichier', form.type_fichier)
  if (form.user_id) formData.append('user_id', String(form.user_id))
  if (fileInput.value?.files?.length) formData.append('fichier', fileInput.value.files[0])
  try {
    if (editingId.value) {
      await updateRessource(editingId.value, form)
      msg.value = 'Ressource mise à jour.'
    } else {
      await createRessource(form)
      msg.value = 'Ressource créée.'
    }
    editingId.value = null
    form.titre = ''
    form.fichier = ''
    form.type_fichier = ''
    form.user_id = null
    form.titre = ''
    fileName.value = ''
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || Object.values(e.response?.data?.errors || {}).flat().join(', ') || 'Erreur.'
  }
}

function edit(r: typeof list.value[0]) {
  editingId.value = r.id
  form.titre = r.titre
  form.fichier = r.fichier || ''
  form.type_fichier = r.type_fichier || ''
  form.user_id = r.user_id || null
  fileName.value = r.fichier || ''
}

function cancel() {
  editingId.value = null
  form.titre = ''
  form.fichier = ''
  form.type_fichier = ''
  form.user_id = null
  fileName.value = ''
}

async function remove(id: number) {
  if (!confirm('Supprimer cette ressource ?')) return
  try {
    await deleteRessource(id)
    await load()
  } catch (e: any) {
    err.value = e.response?.data?.message || 'Erreur.'
  }
}

onMounted(load)
</script>

<template>
  <div class="form-page">
    <h1>Gestion des Ressources Numériques</h1>
    <div v-if="msg" class="success">{{ msg }}</div>
    <div v-if="err" class="error">{{ err }}</div>

    <form class="data-form" @submit.prevent="submit">
      <h2>{{ editingId ? 'Modifier' : 'Créer' }} une ressource</h2>
      <div class="row">
        <label>Titre <input v-model="form.titre" required /></label>
        <label>Type fichier <input v-model="form.type_fichier" required placeholder="pdf, mp3, mp4…" /></label>
        <label>Utilisateur
          <select v-model="form.user_id">
            <option :value="null">— Aucun —</option>
            <option v-for="u in usersList" :key="u.id" :value="u.id">{{ u.nom }}</option>
          </select>
        </label>
      </div>
      <label>Fichier
        <input ref="fileInput" type="file" @change="onFileSelected" />
        <span v-if="fileName" class="file-name">{{ fileName }}</span>
      </label>
      <div class="btn-row">
        <button type="submit">{{ editingId ? 'Mettre à jour' : 'Créer' }}</button>
        <button v-if="editingId" type="button" @click="cancel" class="secondary">Annuler</button>
      </div>
    </form>

    <table class="table">
      <thead><tr><th>ID</th><th>Titre</th><th>Type</th><th>Fichier</th><th>Utilisateur</th><th>Actions</th></tr></thead>
      <tbody>
        <tr v-for="r in list" :key="r.id">
          <td>{{ r.id }}</td>
          <td>{{ r.titre }}</td>
          <td>{{ r.type_fichier }}</td>
          <td>{{ r.fichier || '-' }}</td>
          <td>{{ r.user?.nom || '-' }}</td>
          <td>
            <button @click="edit(r)" class="small">Éditer</button>
            <button @click="remove(r.id)" class="small danger">Supprimer</button>
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
.row { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: .75rem; }
.row label { display: flex; flex-direction: column; gap: .3rem; flex: 1; min-width: 140px; font-size: .85rem; font-weight: 600; }
.row select, .row input { padding: .5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
label { display: flex; flex-direction: column; gap: .3rem; font-size: .85rem; font-weight: 600; margin-bottom: .75rem; }
.btn-row { display: flex; gap: .5rem; margin-top: 1rem; }
button { padding: .5rem 1rem; border: none; border-radius: 6px; cursor: pointer; background: #2563eb; color: #fff; }
.secondary { background: #64748b; }
.small { padding: .25rem .6rem; font-size: .8rem; }
.danger { background: #ef4444; }
.file-name { font-size: .8rem; color: #64748b; }
.success { background: #f0fdf4; color: #16a34a; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.error { background: #fef2f2; color: #dc2626; padding: .75rem; border-radius: 8px; margin-bottom: 1rem; }
.table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; }
.table th, .table td { padding: .7rem 1rem; text-align: left; border-bottom: 1px solid #f1f5f9; }
.table th { background: #f1f5f9; font-size: .85rem; }
</style>
