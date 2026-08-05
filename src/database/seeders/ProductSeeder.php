<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'          => 'Loteos',
                'slug'          => 'loteos',
                'public_domain' => 'loteos',
                'description'   => 'Sistema de gestión de loteos y fraccionamientos.',
                'active'        => true,
                'base_price'    => 200000,
            ],
            [
                'name'          => 'Servis',
                'slug'          => 'tallerpro',
                'public_domain' => 'servis',
                'description'   => 'Sistema de gestión para talleres de reparación.',
                'active'        => true,
                'base_price'    => 180000,
            ],
            [
                'name'          => 'Clínica',
                'slug'          => 'historias-clinicas',
                'public_domain' => 'clinica',
                'description'   => 'Sistema de gestión de historias clínicas médicas.',
                'active'        => true,
                'base_price'    => 250000,
            ],
            [
                'name'          => 'Subastas',
                'slug'          => 'subastas',
                'public_domain' => 'subastas',
                'description'   => 'Plataforma de subastas judiciales electrónicas en tiempo real.',
                // TODO: precio placeholder — definir base_price real (producto más
                // complejo/institucional que el resto del portfolio, ver
                // propuesta_subastas_judiciales.pdf §5 para la cotización de desarrollo
                // a medida que sirvió de base a este producto).
                'active'        => true,
                'base_price'    => 300000,
            ],
            [
                'name'          => 'Chatia',
                'slug'          => 'chatia',
                'public_domain' => 'chatia',
                'description'   => 'Automatización conversacional omnicanal (WhatsApp, chat web) con flujos, conectores e IA.',
                // TODO: precio placeholder — definir base_price real (producto
                // horizontal, sin equivalente directo en el resto del portfolio
                // vertical, ver docs/chatia-architecture.md §0).
                'active'        => true,
                'base_price'    => 150000,
            ],
        ];

        $periods = [
            ['period' => 'monthly',    'months' => 1,  'discount' => 0],
            ['period' => 'quarterly',  'months' => 3,  'discount' => 10],
            ['period' => 'semiannual', 'months' => 6,  'discount' => 20],
            ['period' => 'annual',     'months' => 12, 'discount' => 30],
        ];

        foreach ($products as $productData) {
            $basePrice = $productData['base_price'];
            unset($productData['base_price']);

            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );

            foreach ($periods as $period) {
                $finalPrice = $basePrice * $period['months'] * (1 - $period['discount'] / 100);

                Plan::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'period'     => $period['period'],
                    ],
                    [
                        'name'             => $product->name . ' — ' . match($period['period']) {
                            'monthly'    => 'Mensual',
                            'quarterly'  => 'Trimestral',
                            'semiannual' => 'Semestral',
                            'annual'     => 'Anual',
                        },
                        'period_months'    => $period['months'],
                        'base_price'       => $basePrice,
                        'price'            => $finalPrice,
                        'discount_percent' => $period['discount'],
                        'active'           => true,
                    ]
                );
            }
        }
    }
}