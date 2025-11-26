<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;
use Spatie\Permission\Models\{Role as SpatieRole, Permission};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles & Permissions
        foreach (['admin', 'seller', 'financial'] as $role) {
            SpatieRole::findOrCreate($role, 'api');
        }
        $permissions = ['products.manage', 'orders.create', 'orders.manage', 'invoices.manage', 'users.manage'];
        foreach ($permissions as $perm) {
            Permission::findOrCreate($perm, 'api');
        }
        SpatieRole::findByName('admin', 'api')->givePermissionTo($permissions);
        SpatieRole::findByName('seller', 'api')->givePermissionTo(['products.manage', 'orders.create', 'orders.manage']);
        SpatieRole::findByName('financial', 'api')->givePermissionTo(['invoices.manage', 'orders.create']);

        // Users
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@platform.test', 'password' => Hash::make('password')]);
        $admin->assignRole('admin');

        $seller = User::create(['name' => 'Carlos Vendedor', 'email' => 'seller@platform.test', 'password' => Hash::make('password')]);
        $seller->assignRole('seller');

        $financial = User::create(['name' => 'Ana Financeiro', 'email' => 'financial@platform.test', 'password' => Hash::make('password')]);
        $financial->assignRole('financial');

        // Products
        $products = [
            ['name' => 'Laptop Dell XPS 15', 'sku' => 'DELL-XPS-15', 'price_cents' => 149900, 'stock' => 25, 'description' => 'Portátil profesional 15 pulgadas'],
            ['name' => 'Monitor LG 27" 4K', 'sku' => 'LG-27-4K', 'price_cents' => 44900, 'stock' => 50, 'description' => 'Monitor IPS 4K HDR'],
            ['name' => 'Teclado Mecánico Keychron', 'sku' => 'KEY-K8-PRO', 'price_cents' => 8900, 'stock' => 100, 'description' => 'Teclado mecánico wireless'],
            ['name' => 'Ratón Logitech MX Master', 'sku' => 'LOG-MX-3S', 'price_cents' => 9900, 'stock' => 80, 'description' => 'Ratón ergonómico profesional'],
            ['name' => 'Webcam Logitech Brio', 'sku' => 'LOG-BRIO-4K', 'price_cents' => 19900, 'stock' => 40, 'description' => 'Webcam 4K HDR'],
        ];
        foreach ($products as $p) {
            DB::table('products')->insert(array_merge($p, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Orders
        $order = DB::table('orders')->insertGetId([
            'user_id' => $seller->id, 'status' => 'confirmed',
            'subtotal' => 159800, 'tax' => 33558, 'shipping' => 0, 'discount' => 0, 'total' => 193358,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('order_items')->insert([
            ['order_id' => $order, 'product_id' => 1, 'quantity' => 1, 'unit_price' => 149900, 'created_at' => now(), 'updated_at' => now()],
            ['order_id' => $order, 'product_id' => 4, 'quantity' => 1, 'unit_price' => 9900, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Invoice
        DB::table('invoices')->insert([
            'order_id' => $order, 'number' => 'INV-2024-0001',
            'identification_key' => Str::random(64),
            'tax_type' => 'general', 'net_amount' => 159800, 'tax_amount' => 33558, 'total_amount' => 193358,
            'digital_signature' => hash('sha256', 'INV-2024-0001'),
            'issued_at' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
