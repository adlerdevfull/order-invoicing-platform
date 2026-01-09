import axios from 'axios'

const api = axios.create({ baseURL: '/api/v1', headers: { 'Accept': 'application/json' } })

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(err)
  }
)

export const auth = {
  login: (data) => api.post('/auth/login', data),
  me: () => api.get('/auth/me'),
  logout: () => api.post('/auth/logout'),
}

export const products = {
  list: () => api.get('/products'),
  create: (data) => api.post('/products', data),
  update: (id, data) => api.put(`/products/${id}`, data),
}

export const orders = {
  list: () => api.get('/orders'),
  show: (id) => api.get(`/orders/${id}`),
  create: (data) => api.post('/orders', data),
  transition: (id, status) => api.patch(`/orders/${id}/transition`, { status }),
}

export const invoices = {
  list: () => api.get('/invoices'),
  show: (id) => api.get(`/invoices/${id}`),
  create: (data) => api.post('/invoices', data),
}

export default api
