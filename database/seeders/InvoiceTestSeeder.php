<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Users\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoiceTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario o crear uno de prueba
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Usuario de Prueba',
                'email' => 'test@gesticode.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $testInvoices = [
            [
                'number' => 'FAC-2025-001',
                'total_amount' => 45.50,
                'currency_code' => 'EUR',
                'payment_method' => 'Targeta Bancaria',
                'status' => 'aprobada',
                'date' => Carbon::now()->subDays(1),
                'seller_info' => [
                    'name' => 'Supermercado El Corte Inglés',
                    'address' => 'Calle Gran Vía 32, Madrid',
                    'cif' => 'A12345678'
                ],
                'items' => [
                    ['description' => 'Pan integral', 'quantity' => 2, 'price' => 3.00, 'price_per_unit' => 1.50],
                    ['description' => 'Leche desnatada', 'quantity' => 1, 'price' => 1.20, 'price_per_unit' => 1.20],
                    ['description' => 'Manzanas Golden', 'quantity' => 1.5, 'price' => 2.80, 'price_per_unit' => 3.00],
                    ['description' => 'Pollo filetes', 'quantity' => 1, 'price' => 6.90, 'price_per_unit' => 6.90],
                    ['description' => 'Yogures naturales', 'quantity' => 1, 'price' => 3.20, 'price_per_unit' => 3.20]
                ],
                'content_description' => 'Compra en supermercado con productos básicos de alimentación incluyendo lácteos, carnes y frutas.'
            ],
            [
                'number' => 'FAC-2025-002',
                'total_amount' => 65.80,
                'currency_code' => 'EUR',
                'payment_method' => 'Efectivo',
                'status' => 'aprobada',
                'date' => Carbon::now()->subDays(3),
                'seller_info' => [
                    'name' => 'Gasolinera Repsol',
                    'address' => 'Autopista A-1 Km 15',
                    'cif' => 'B87654321'
                ],
                'items' => [
                    ['description' => 'Gasolina 95', 'quantity' => 45.2, 'price' => 1.45, 'price_per_unit' => 1.45]
                ],
                'content_description' => 'Repostaje de combustible gasolina 95 octanos en estación de servicio Repsol.'
            ],
            [
                'number' => 'FAC-2025-003',
                'total_amount' => 28.90,
                'currency_code' => 'EUR',
                'payment_method' => 'Targeta Bancaria',
                'status' => 'aprobada',
                'date' => Carbon::now()->subDays(5),
                'seller_info' => [
                    'name' => 'Farmacia López',
                    'address' => 'Plaza Mayor 8, Madrid',
                    'cif' => 'C11223344'
                ],
                'items' => [
                    ['description' => 'Ibuprofeno 600mg', 'quantity' => 1, 'price' => 8.50, 'price_per_unit' => 8.50],
                    ['description' => 'Vitamina C', 'quantity' => 1, 'price' => 12.40, 'price_per_unit' => 12.40],
                    ['description' => 'Crema hidratante', 'quantity' => 1, 'price' => 8.00, 'price_per_unit' => 8.00]
                ],
                'content_description' => 'Compra en farmacia de medicamentos analgésicos, suplementos vitamínicos y productos de cuidado personal.'
            ],
            [
                'number' => 'FAC-2025-004',
                'total_amount' => 89.99,
                'currency_code' => 'EUR',
                'payment_method' => 'Targeta Bancaria',
                'status' => 'aprobada',
                'date' => Carbon::now()->subDays(7),
                'seller_info' => [
                    'name' => 'Restaurante La Tasquita',
                    'address' => 'Calle de los Cuchilleros 5, Madrid',
                    'cif' => 'D55667788'
                ],
                'items' => [
                    ['name' => 'Menú degustación para 2', 'quantity' => 1, 'price' => 75.00, 'price_per_unit' => 75.00],
                    ['name' => 'Vino Ribera del Duero', 'quantity' => 1, 'price' => 14.99, 'price_per_unit' => 14.99]
                ],
                'content_description' => 'Cena en restaurante tradicional español con menú degustación y maridaje de vinos.'
            ],
            [
                'number' => 'FAC-2025-005',
                'total_amount' => 156.45,
                'currency_code' => 'EUR',
                'payment_method' => 'Desconocido',
                'status' => 'pendiente',
                'date' => Carbon::now()->subDays(2),
                'seller_info' => [
                    'name' => 'Taller Mecánico AutoService',
                    'address' => 'Polígono Industrial Sur, Nave 12',
                    'cif' => 'E99887766'
                ],
                'items' => [
                    ['description' => 'Cambio de aceite y filtros', 'quantity' => 1, 'price' => 65.00, 'price_per_unit' => 65.00],
                    ['description' => 'Revisión ITV', 'quantity' => 1, 'price' => 45.00, 'price_per_unit' => 45.00],
                    ['description' => 'Mano de obra', 'quantity' => 2, 'price' => 23.225, 'price_per_unit' => 11.6125]
                ],
                'content_description' => 'Servicio de mantenimiento vehicular con cambio de aceite, filtros y revisión técnica reglamentaria.'
            ],
            [
                'number' => 'FAC-2025-006',
                'total_amount' => 23.75,
                'currency_code' => 'EUR',
                'payment_method' => 'Targeta Bancaria',
                'status' => 'aprobada',
                'date' => Carbon::now()->subDays(4),
                'seller_info' => [
                    'name' => 'Librería Cervantes',
                    'address' => 'Calle de Alcalá 156, Madrid',
                    'cif' => 'F33445566'
                ],
                'items' => [
                    ['description' => 'Cuaderno A4', 'quantity' => 2, 'price' => 3.50, 'price_per_unit' => 1.75],
                    ['description' => 'Bolígrafos azules', 'quantity' => 1, 'price' => 4.25, 'price_per_unit' => 4.25],
                    ['description' => 'Agenda 2025', 'quantity' => 1, 'price' => 12.50, 'price_per_unit' => 12.50],
                ],
                'content_description' => 'Compra de material de oficina y papelería para uso personal y profesional.'
            ]
        ];

        foreach ($testInvoices as $invoiceData) {
            $invoice = Invoice::create([
                'user_id' => $user->id,
                'number' => $invoiceData['number'],
                'total_amount' => $invoiceData['total_amount'],
                'currency_code' => $invoiceData['currency_code'],
                'payment_method' => $invoiceData['payment_method'],
                'status' => $invoiceData['status'],
                'date' => $invoiceData['date'],
                'time' => $invoiceData['date']->format('H:i:s'),
                'seller_info' => $invoiceData['seller_info'],
                'items' => $invoiceData['items'],
                'content_description' => $invoiceData['content_description'],
            ]);

            echo "✅ Factura creada: {$invoice->number} - {$invoice->total_amount} {$invoice->currency_code}\n";
        }

        echo "\n🎉 Se han creado " . count($testInvoices) . " facturas de prueba para el usuario {$user->email}\n";
    }
}
