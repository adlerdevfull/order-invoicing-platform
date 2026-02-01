#!/bin/bash
# Demo: Plataforma de Gestão de Pedidos e Faturamento
# Uso: ./scripts/demo.sh

BASE_URL="http://localhost:8000/api"
echo "=== order-invoicing-platform ==="
echo ""

# Health check
echo "1. Health Check"
curl -s $BASE_URL/v1/health | jq .
echo ""

# Login
echo "2. Login (admin@platform.test / password)"
TOKEN=$(curl -s -X POST $BASE_URL/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@platform.test","password":"password"}' | jq -r '.token // .access_token')
echo "   Token: ${TOKEN:0:30}..."
echo ""

# List products
echo "3. Listar Productos"
curl -s $BASE_URL/v1/products \
  -H "Authorization: Bearer $TOKEN" | jq '.data[:2]'
echo ""

# Create order
echo "4. Crear Pedido"
curl -s -X POST $BASE_URL/v1/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"items":[{"product_id":1,"quantity":2},{"product_id":3,"quantity":1}]}' | jq .
echo ""

# List orders
echo "5. Listar Pedidos"
curl -s $BASE_URL/v1/orders \
  -H "Authorization: Bearer $TOKEN" | jq '.data[:2]'
echo ""

# Transition order
echo "6. Transición de Estado (draft → confirmed)"
curl -s -X PATCH $BASE_URL/v1/orders/2/transition \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"transition":"confirm"}' | jq .
echo ""

# Create invoice
echo "7. Generar Factura"
curl -s -X POST $BASE_URL/v1/invoices \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"order_id":2}' | jq .
echo ""

# List invoices
echo "8. Listar Facturas"
curl -s $BASE_URL/v1/invoices \
  -H "Authorization: Bearer $TOKEN" | jq '.data[:2]'
echo ""

echo "=== Demo completada ==="
