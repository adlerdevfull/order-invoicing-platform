import { useI18n } from '../hooks/useI18n'
import { useState, useEffect } from 'react'
import { products, orders, invoices } from '../services/api'
import { Package, ShoppingCart, FileText, TrendingUp } from 'lucide-react'

export default function Dashboard() {
  const { t } = useI18n()
  const [stats, setStats] = useState({ products: 0, orders: 0, invoices: 0, revenue: 0 })

  useEffect(() => {
    Promise.all([products.list(), orders.list(), invoices.list()])
      .then(([p, o, i]) => {
        const prodList = p.data.data || p.data || []
        const orderList = o.data.data || o.data || []
        const invList = i.data.data || i.data || []
        const revenue = invList.reduce((sum, inv) => sum + (inv.total_amount || inv.total || 0), 0)
        setStats({
          products: prodList.length,
          orders: orderList.length,
          invoices: invList.length,
          revenue,
        })
      })
      .catch(() => {})
  }, [])

  const cards = [
    { label: 'Productos', value: stats.products, icon: Package, color: 'bg-blue-500' },
    { label: 'Pedidos', value: stats.orders, icon: ShoppingCart, color: 'bg-green-500' },
    { label: 'Facturas', value: stats.invoices, icon: FileText, color: 'bg-purple-500' },
    { label: 'Facturación', value: `€${(stats.revenue / 100).toFixed(2)}`, icon: TrendingUp, color: 'bg-orange-500' },
  ]

  return (
    <div>
      <h2 className="text-2xl font-bold text-gray-900 mb-6">{t('dashboard.title')}</h2>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {cards.map(({ label, value, icon: Icon, color }) => (
          <div key={label} className="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
            <div className={`${color} p-3 rounded-lg text-white`}>
              <Icon size={24} />
            </div>
            <div>
              <p className="text-sm text-gray-500">{label}</p>
              <p className="text-2xl font-bold text-gray-900">{value}</p>
            </div>
          </div>
        ))}
      </div>
      <div className="bg-white rounded-xl shadow-sm p-6">
        <h3 className="font-semibold text-gray-900 mb-3">Información del Sistema</h3>
        <div className="grid grid-cols-2 gap-4 text-sm">
          <div className="p-3 bg-gray-50 rounded-lg">
            <p className="text-gray-500">Stack</p>
            <p className="font-medium">Laravel 11 + PostgreSQL 16 + Redis 7</p>
          </div>
          <div className="p-3 bg-gray-50 rounded-lg">
            <p className="text-gray-500">Arquitectura</p>
            <p className="font-medium">Hexagonal + DDD + SOLID</p>
          </div>
          <div className="p-3 bg-gray-50 rounded-lg">
            <p className="text-gray-500">Autenticación</p>
            <p className="font-medium">JWT con roles (RBAC)</p>
          </div>
          <div className="p-3 bg-gray-50 rounded-lg">
            <p className="text-gray-500">Facturación</p>
            <p className="font-medium">IVA 21% España + Firma Digital</p>
          </div>
        </div>
      </div>
    </div>
  )
}
