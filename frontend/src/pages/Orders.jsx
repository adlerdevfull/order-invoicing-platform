import { useState, useEffect } from 'react'
import { orders, products as productsApi } from '../services/api'
import { ShoppingCart, Plus, ArrowRight } from 'lucide-react'

const statusColors = {
  draft: 'bg-gray-100 text-gray-700',
  confirmed: 'bg-blue-100 text-blue-700',
  paid: 'bg-yellow-100 text-yellow-700',
  shipped: 'bg-purple-100 text-purple-700',
  cancelled: 'bg-red-100 text-red-700',
}

const transitions = {
  draft: ['confirmed', 'cancelled'],
  confirmed: ['paid', 'cancelled'],
  paid: ['shipped', 'cancelled'],
}

export default function Orders() {
  const [list, setList] = useState([])
  const [productList, setProductList] = useState([])
  const [showForm, setShowForm] = useState(false)
  const [cart, setCart] = useState([])

  const load = () => orders.list().then(r => setList(r.data.data || r.data || [])).catch(() => {})

  useEffect(() => {
    load()
    productsApi.list().then(r => setProductList(r.data.data || r.data || [])).catch(() => {})
  }, [])

  const addToCart = (productId) => {
    const existing = cart.find(i => i.product_id === productId)
    if (existing) {
      setCart(cart.map(i => i.product_id === productId ? { ...i, quantity: i.quantity + 1 } : i))
    } else {
      setCart([...cart, { product_id: productId, quantity: 1 }])
    }
  }

  const createOrder = async () => {
    await orders.create({ items: cart })
    setCart([])
    setShowForm(false)
    load()
  }

  const handleTransition = async (id, status) => {
    await orders.transition(id, status)
    load()
  }

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h2 className="text-2xl font-bold text-gray-900">Pedidos</h2>
        <button onClick={() => setShowForm(!showForm)} className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
          <Plus size={16} /> Nuevo Pedido
        </button>
      </div>

      {showForm && (
        <div className="bg-white rounded-xl shadow-sm p-5 mb-6">
          <h3 className="font-semibold mb-3">Seleccionar Productos</h3>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
            {productList.map(p => (
              <button key={p.id} onClick={() => addToCart(p.id)} className="text-left p-3 border rounded-lg hover:border-blue-400 transition text-sm">
                <p className="font-medium truncate">{p.name}</p>
                <p className="text-green-600">€{(p.price_cents / 100).toFixed(2)}</p>
              </button>
            ))}
          </div>
          {cart.length > 0 && (
            <div className="border-t pt-3">
              <p className="text-sm text-gray-500 mb-2">Carrito: {cart.map(i => `${i.product_id}×${i.quantity}`).join(', ')}</p>
              <div className="flex gap-2">
                <button onClick={createOrder} className="bg-green-600 text-white px-4 py-2 rounded-lg text-sm">Crear Pedido</button>
                <button onClick={() => { setCart([]); setShowForm(false) }} className="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Cancelar</button>
              </div>
            </div>
          )}
        </div>
      )}

      <div className="space-y-3">
        {list.map((order) => (
          <div key={order.id} className="bg-white rounded-xl shadow-sm p-5">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-3">
                <ShoppingCart size={20} className="text-gray-400" />
                <div>
                  <p className="font-semibold">Pedido #{order.id}</p>
                  {order.created_at && <p className="text-xs text-gray-400">{new Date(order.created_at).toLocaleDateString('es-ES')}</p>}
                </div>
              </div>
              <div className="flex items-center gap-3">
                <span className={`text-xs px-3 py-1 rounded-full font-medium ${statusColors[order.status] || 'bg-gray-100'}`}>
                  {order.status}
                </span>
                <span className="text-lg font-bold">€{((order.total || 0) / 100).toFixed(2)}</span>
              </div>
            </div>
            {transitions[order.status] && (
              <div className="mt-3 pt-3 border-t flex gap-2">
                {transitions[order.status].map(s => (
                  <button
                    key={s}
                    onClick={() => handleTransition(order.id, s)}
                    className="flex items-center gap-1 text-xs bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition"
                  >
                    <ArrowRight size={12} /> {s}
                  </button>
                ))}
              </div>
            )}
          </div>
        ))}
      </div>
      {list.length === 0 && (
        <div className="text-center py-12 text-gray-400">
          <ShoppingCart size={48} className="mx-auto mb-3 opacity-50" />
          <p>No hay pedidos registrados</p>
        </div>
      )}
    </div>
  )
}
