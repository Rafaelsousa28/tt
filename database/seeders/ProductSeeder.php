<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $citricoId     = Category::where('slug', 'mudas-citricas')->value('id');
        $ornamentalId  = Category::where('slug', 'ornamentais')->value('id');
        $suculentaId   = Category::where('slug', 'suculentas')->value('id');
        $ervasId       = Category::where('slug', 'ervas-temperos')->value('id');
        $interiorId    = Category::where('slug', 'interior')->value('id');

        $products = [
            [
                'category_id'       => $citricoId,
                'name'              => 'Limão Siciliano',
                'price'             => 45.00,
                'sale_price'        => 38.00,
                'stock'             => 25,
                'is_featured'       => true,
                'care_level'        => 'facil',
                'light_requirement' => 'pleno_sol',
                'water_frequency'   => '2x por semana',
                'height_cm'         => '40-60',
                'short_description' => 'Muda enxertada de Limão Siciliano, frutificação precoce. Ideal para vasos e jardins.',
            ],
            [
                'category_id'       => $citricoId,
                'name'              => 'Laranja Bahia',
                'price'             => 52.00,
                'stock'             => 15,
                'is_featured'       => true,
                'care_level'        => 'facil',
                'light_requirement' => 'pleno_sol',
                'water_frequency'   => '2-3x por semana',
                'short_description' => 'Muda enxertada de Laranja Bahia, frutos grandes e suculentos sem sementes.',
            ],
            [
                'category_id'       => $citricoId,
                'name'              => 'Mexerica Ponkan',
                'price'             => 48.00,
                'stock'             => 18,
                'care_level'        => 'facil',
                'light_requirement' => 'pleno_sol',
                'water_frequency'   => '2x por semana',
                'short_description' => 'Muda de Mexerica Ponkan, fácil de descascar. Ótima para o quintal.',
            ],
            [
                'category_id'       => $ornamentalId,
                'name'              => 'Costela de Adão',
                'price'             => 35.00,
                'stock'             => 30,
                'is_featured'       => true,
                'care_level'        => 'facil',
                'light_requirement' => 'media',
                'water_frequency'   => '1x por semana',
                'height_cm'         => '30-50',
                'short_description' => 'Monstera deliciosa, a clássica costela de adão. Perfeita para ambientes internos.',
            ],
            [
                'category_id'       => $ornamentalId,
                'name'              => 'Orquídea Phalaenopsis',
                'price'             => 89.00,
                'sale_price'        => 69.90,
                'stock'             => 12,
                'is_featured'       => true,
                'care_level'        => 'medio',
                'light_requirement' => 'media',
                'water_frequency'   => '1x por semana',
                'short_description' => 'Orquídea branca em vaso, floração longa de 2 a 3 meses. Elegante e sofisticada.',
            ],
            [
                'category_id'       => $suculentaId,
                'name'              => 'Echeveria Rosada',
                'price'             => 18.00,
                'stock'             => 50,
                'care_level'        => 'facil',
                'light_requirement' => 'alta',
                'water_frequency'   => '1x por semana',
                'short_description' => 'Suculenta Echeveria com belas rosetas rosadas. Fácil cuidado e muito decorativa.',
            ],
            [
                'category_id'       => $ervasId,
                'name'              => 'Manjericão Genovês',
                'price'             => 12.00,
                'stock'             => 40,
                'care_level'        => 'facil',
                'light_requirement' => 'alta',
                'water_frequency'   => 'Diariamente',
                'short_description' => 'Muda de Manjericão Genovês, indispensável na cozinha. Aroma intenso e delicioso.',
            ],
            [
                'category_id'       => $interiorId,
                'name'              => 'Espada de São Jorge',
                'price'             => 28.00,
                'stock'             => 35,
                'care_level'        => 'facil',
                'light_requirement' => 'baixa',
                'water_frequency'   => '1x por 2 semanas',
                'short_description' => 'Sansevieria trifasciata, conhecida como espada de São Jorge. Purifica o ar e requer pouquíssima atenção.',
            ],
        ];

        foreach ($products as $data) {
            $data['slug']      = Str::slug($data['name']);
            $data['is_active'] = true;
            $data['is_featured'] = $data['is_featured'] ?? false;
            Product::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
