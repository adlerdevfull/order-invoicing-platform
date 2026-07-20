import { createContext, useContext, useMemo, useState, useCallback } from 'react'

const messages = {
  "en": {
    "appName": "Orders Platform",
    "appTagline": "Orders & invoicing",
    "logout": "Log out",
    "loading": "Loading…",
    "language": "Language",
    "login": {
      "title": "Orders Platform",
      "subtitle": "Orders & invoicing",
      "email": "Email",
      "password": "Password",
      "submit": "Sign in",
      "loading": "Signing in…",
      "error": "Invalid credentials",
      "hint": "admin@platform.test / password"
    },
    "nav": {
      "home": "Dashboard",
      "products": "Products",
      "orders": "Orders",
      "invoices": "Invoices"
    },
    "dashboard": {
      "title": "Dashboard"
    }
  },
  "es": {
    "appName": "Plataforma de Pedidos",
    "appTagline": "Pedidos y facturación",
    "logout": "Cerrar sesión",
    "loading": "Cargando…",
    "language": "Idioma",
    "login": {
      "title": "Plataforma de Pedidos",
      "subtitle": "Pedidos y facturación",
      "email": "Email",
      "password": "Contraseña",
      "submit": "Iniciar sesión",
      "loading": "Entrando…",
      "error": "Credenciales inválidas",
      "hint": "admin@platform.test / password"
    },
    "nav": {
      "home": "Dashboard",
      "products": "Productos",
      "orders": "Pedidos",
      "invoices": "Facturas"
    },
    "dashboard": {
      "title": "Dashboard"
    }
  },
  "pt": {
    "appName": "Plataforma de Pedidos",
    "appTagline": "Pedidos e faturamento",
    "logout": "Sair",
    "loading": "Carregando…",
    "language": "Idioma",
    "login": {
      "title": "Plataforma de Pedidos",
      "subtitle": "Pedidos e faturamento",
      "email": "Email",
      "password": "Senha",
      "submit": "Entrar",
      "loading": "Entrando…",
      "error": "Credenciais inválidas",
      "hint": "admin@platform.test / password"
    },
    "nav": {
      "home": "Dashboard",
      "products": "Produtos",
      "orders": "Pedidos",
      "invoices": "Faturas"
    },
    "dashboard": {
      "title": "Dashboard"
    }
  }
}

const I18nContext = createContext(null)

function detect() {
  const saved = localStorage.getItem('locale')
  if (saved && messages[saved]) return saved
  const nav = (navigator.language || 'en').slice(0, 2)
  if (nav === 'es' || nav === 'pt') return nav
  return 'en'
}

export function I18nProvider({ children }) {
  const [locale, setLocaleState] = useState(detect)
  const setLocale = useCallback((next) => {
    setLocaleState(next)
    localStorage.setItem('locale', next)
    document.documentElement.lang = next
  }, [])
  const t = useCallback((key) => {
    const parts = key.split('.')
    let cur = messages[locale]
    for (const p of parts) cur = cur?.[p]
    return typeof cur === 'string' ? cur : key
  }, [locale])
  const value = useMemo(() => ({ locale, setLocale, t }), [locale, setLocale, t])
  return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>
}

export function useI18n() {
  const ctx = useContext(I18nContext)
  if (!ctx) throw new Error('useI18n must be used within I18nProvider')
  return ctx
}
