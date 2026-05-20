import axios from 'axios'

export const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  withCredentials: true,
})

// Auth
export const login = (email: string, password: string) =>
  api.post('/login', { email, password })
export const register = (data: { nom: string; email: string; password: string; telephone?: string }) =>
  api.post('/register', data)
export const logout = () => api.post('/logout')

// Users
export const fetchUsers = () => api.get('/users')
export const createUser = (data: { nom: string; email: string; password: string; telephone?: string; role_id?: number }) =>
  api.post('/users', data)
export const updateUser = (id: number, data: { nom?: string; email?: string; password?: string; telephone?: string; role_id?: number }) =>
  api.put(`/users/${id}`, data)
export const deleteUser = (id: number) => api.delete(`/users/${id}`)

// Auteurs
export const fetchAuteurs = () => api.get('/auteurs')
export const createAuteur = (data: { nom: string; prenom: string; nationalite?: string }) =>
  api.post('/auteurs', data)
export const updateAuteur = (id: number, data: { nom?: string; prenom?: string; nationalite?: string }) =>
  api.put(`/auteurs/${id}`, data)
export const deleteAuteur = (id: number) => api.delete(`/auteurs/${id}`)

// Categories
export const fetchCategories = () => api.get('/categories')
export const createCategorie = (data: { nom_categorie: string; description?: string }) =>
  api.post('/categories', data)
export const updateCategorie = (id: number, data: { nom_categorie?: string; description?: string }) =>
  api.put(`/categories/${id}`, data)
export const deleteCategorie = (id: number) => api.delete(`/categories/${id}`)

// Livres
export const fetchLivres = () => api.get('/livres')
export const createLivre = (data: { titre: string; isbn: string; description?: string; annee_publication?: number; auteur_id?: number | null; categorie_id?: number | null }) =>
  api.post('/livres', data)
export const updateLivre = (id: number, data: { titre?: string; isbn?: string; description?: string; annee_publication?: number; auteur_id?: number | null; categorie_id?: number | null }) =>
  api.put(`/livres/${id}`, data)
export const deleteLivre = (id: number) => api.delete(`/livres/${id}`)

// Emprunts
export const fetchEmprunts = () => api.get('/emprunts')
export const createEmprunt = (data: { user_id: number; livre_id: number; date_retour_prevue: string }) =>
  api.post('/emprunts', data)
export const updateEmprunt = (id: number, data: { user_id?: number; livre_id?: number; date_retour_prevue?: string; date_retour_effective?: string; statut?: 'en_cours' | 'termine' }) =>
  api.put(`/emprunts/${id}`, data)
export const deleteEmprunt = (id: number) => api.delete(`/emprunts/${id}`)
export const retournerEmprunt = (id: number) =>
  api.post(`/emprunts/${id}/retourner`)

// Stock
export const fetchStocks = () => api.get('/stock')
export const createStock = (data: { quantite_totale: number; quantite_disponible: number; seuil_alerte?: number; livre_id: number }) =>
  api.post('/stock', data)
export const updateStock = (id: number, data: { quantite_totale?: number; quantite_disponible?: number; seuil_alerte?: number }) =>
  api.put(`/stock/${id}`, data)
export const deleteStock = (id: number) => api.delete(`/stock/${id}`)
export const fetchStockByLivre = (livreId: number) => api.get(`/stock/livre/${livreId}`)

// Ressources numeriques
export const fetchRessources = () => api.get('/ressources-numeriques')
export const createRessource = (data: { titre: string; fichier: string; type_fichier: string; user_id: number }) =>
  api.post('/ressources-numeriques', data)
export const updateRessource = (id: number, data: { titre?: string; fichier?: string; type_fichier?: string; user_id?: number }) =>
  api.put(`/ressources-numeriques/${id}`, data)
export const deleteRessource = (id: number) => api.delete(`/ressources-numeriques/${id}`)

// Permissions
export const fetchPermissions = () => api.get('/permissions')
export const createPermission = (data: { nom_permission: string }) =>
  api.post('/permissions', data)
export const updatePermission = (id: number, data: { nom_permission: string }) =>
  api.put(`/permissions/${id}`, data)
export const deletePermission = (id: number) => api.delete(`/permissions/${id}`)

// Roles
export const fetchRoles = () => api.get('/roles')
export const createRole = (data: { nom_role: string }) =>
  api.post('/roles', data)
export const updateRole = (id: number, data: { nom_role: string }) =>
  api.put(`/roles/${id}`, data)
export const deleteRole = (id: number) => api.delete(`/roles/${id}`)

// Me
export const fetchMe = () => api.get('/user')
