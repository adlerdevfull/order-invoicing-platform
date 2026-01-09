import { useState, useEffect } from 'react'
import { products } from '../services/api'
import { Plus, Package } from 'lucide-react'

export default function Products() {
  const [list, setList] = useState([])
  const [showForm, setShowForm] = useState(false)
  const [form, setForm] = useState({ name: '', sku: '', price_cents: '', stock: '', description: '' })

  const load = () => products.list().then(r => setList(r.data.data || r.data || [])).catch(() => {})

  useEffect(() => { load() }, [])

  const handleSubmit = async (e) => {
    e.preventDefault()
    await products.create({ ...form, price_cents: parseInt(form.price_cents), stock: parseInt(form.stock) })
    setShowForm(false)
    setForm({ name: '', sku: '', price_cents: '', stock: '', description: '' })
    load()
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h2 className="text-2xl font-bold text-gray-900">Productos</h2>
        <button
          onClick={() => setShowForm(!showForm)}
          className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm"
        >
          <Plus size={16} /> Nuevo Producto
        </button>
      </div>

      {showForm && (
        <form onSubmit={handleSubmit} className="bg-white rounded-xl shadow-sm p-5 mb-6 grid grid-cols-2 gap-4">
          <input placeholder="Nombre" value={form.name} onChange={e => setForm({ ...form, name: e.target.value })} className="px-3 py-2 border rounded-lg" required />
          <input placeholder="SKU" value={form.sku} onChange={e => setForm({ ...form, sku: e.target.value })} className="px-3 py-2 border rounded-lg" required />
          <input placeholder="Precio (céntimos)" type="number" value={form.price_cents} onChange={e => setForm({ ...form, price_cents: e.target.value })} className="px-3 py-2 border rounded-lg" required />
          <input placeholder="Stock" type="number" value={form.stock} onChange={e => setForm({ ...form, stock: e.target.value })} className="px-3 py-2 border rounded-lg" required />
          <input placeholder="Descripción" value={form.description} onChange={e => setForm({ ...form, description: e.target.value })} className="col-span-2 px-3 py-2 border rounded-lg" />
          <div className="col-span-2 flex gap-2">
            <button type="submit" className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">Guardar</button>
            <button type="button" onClick={() => setShowForm(false)} className="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Cancelar</button>
          </div>
        </form>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {list.map((p) => (
          <div key={p.id} className="bg-white rounded-xl shadow-sm p-5">
            <div className="flex items-start justify-between">
              <div className="flex items-center gap-3">
                <div className="bg-blue-50 p-2 rounded-lg">
                  <Package size={20} className="text-blue-600" />
                </div>
                <div>
                  <h3 className="font-semibold text-gray-900">{p.name}</h3>
                  <p className="text-xs text-gray-400">SKU: {p.sku}</p>
                </div>
              </div>
            </div>
            <div className="mt-4 flex items-center justify-between">
              <span className="text-lg font-bold text-green-600">€{(p.price_cents / 100).toFixed(2)}</span>
              <span className={`text-xs px-2 py-1 rounded-full ${p.stock > 20 ? 'bg-green-100 text-green-700' : p.stock > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'}`}>
                Stock: {p.stock}
              </span>
            </div>
            {p.description && <p className="text-sm text-gray-500 mt-2">{p.description}</p>}
          </div>
        ))}
      </div>
      {list.length === 0 && (
        <div className="text-center py-12 text-gray-400">
          <Package size={48} className="mx-auto mb-3 opacity-50" />
          <p>No hay productos registrados</p>
        </div>
      )}
    </div>
  )
}
