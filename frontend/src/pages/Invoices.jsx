import { useState, useEffect } from 'react'
import { invoices, orders as ordersApi } from '../services/api'
import { FileText, Plus, Download } from 'lucide-react'

export default function Invoices() {
  const [list, setList] = useState([])
  const [orderList, setOrderList] = useState([])
  const [showForm, setShowForm] = useState(false)

  const load = () => invoices.list().then(r => setList(r.data.data || r.data || [])).catch(() => {})

  useEffect(() => {
    load()
    ordersApi.list().then(r => setOrderList(r.data.data || r.data || [])).catch(() => {})
  }, [])

  const createInvoice = async (orderId) => {
    try {
      await invoices.create({ order_id: orderId })
      setShowForm(false)
      load()
    } catch (e) {
      alert(e.response?.data?.message || 'Error al generar factura')
    }
  }

  const invoicedOrderIds = list.map(inv => inv.order_id)
  const paidOrders = orderList.filter(o => o.status === 'paid' && !invoicedOrderIds.includes(o.id))

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h2 className="text-2xl font-bold text-gray-900">Facturas</h2>
        <button onClick={() => setShowForm(!showForm)} className="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
          <Plus size={16} /> Generar Factura
        </button>
      </div>

      {showForm && (
        <div className="bg-white rounded-xl shadow-sm p-5 mb-6">
          <h3 className="font-semibold mb-3">Seleccionar Pedido para Facturar</h3>
          <div className="space-y-2">
            {paidOrders.map(o => (
              <button key={o.id} onClick={() => createInvoice(o.id)} className="w-full text-left p-3 border rounded-lg hover:border-blue-400 transition flex justify-between items-center">
                <span className="text-sm">Pedido #{o.id} - {o.status}</span>
                <span className="font-medium text-green-600">€{((o.total || 0) / 100).toFixed(2)}</span>
              </button>
            ))}
            {paidOrders.length === 0 && <p className="text-sm text-gray-400">No hay pedidos pagados para facturar</p>}
          </div>
        </div>
      )}

      <div className="space-y-3">
        {list.map((inv) => (
          <div key={inv.id} className="bg-white rounded-xl shadow-sm p-5">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="bg-purple-50 p-2 rounded-lg">
                  <FileText size={20} className="text-purple-600" />
                </div>
                <div>
                  <p className="font-semibold">{inv.number}</p>
                  <p className="text-xs text-gray-400">Pedido #{inv.order_id} • {inv.issued_at ? new Date(inv.issued_at).toLocaleDateString('es-ES') : 'Pendiente'}</p>
                </div>
              </div>
              <div className="text-right">
                <p className="text-lg font-bold text-gray-900">€{((inv.total_amount || 0) / 100).toFixed(2)}</p>
                <p className="text-xs text-gray-400">IVA: €{((inv.tax_amount || 0) / 100).toFixed(2)}</p>
              </div>
            </div>
            <div className="mt-3 pt-3 border-t grid grid-cols-4 gap-4 text-xs">
              <div>
                <p className="text-gray-400">Base Imponible</p>
                <p className="font-medium">€{((inv.net_amount || 0) / 100).toFixed(2)}</p>
              </div>
              <div>
                <p className="text-gray-400">Tipo IVA</p>
                <p className="font-medium">{inv.tax_type || 'IVA_21'}</p>
              </div>
              <div>
                <p className="text-gray-400">Firma Digital</p>
                <p className="font-medium text-green-600 truncate">{inv.digital_signature ? '✓ Firmada' : '—'}</p>
              </div>
              <div>
                <p className="text-gray-400">Clave</p>
                <p className="font-medium truncate" title={inv.identification_key}>{inv.identification_key?.slice(0, 12)}...</p>
              </div>
            </div>
          </div>
        ))}
      </div>
      {list.length === 0 && (
        <div className="text-center py-12 text-gray-400">
          <FileText size={48} className="mx-auto mb-3 opacity-50" />
          <p>No hay facturas generadas</p>
        </div>
      )}
    </div>
  )
}
