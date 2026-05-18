<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Mudas Cítricas',    'slug' => 'mudas-citricas',   'sort_order' => 1],
            ['name' => 'Ornamentais',        'slug' => 'ornamentais',      'sort_order' => 2],
            ['name' => 'Suculentas',         'slug' => 'suculentas',       'sort_order' => 3],
            ['name' => 'Ervas & Temperos',   'slug' => 'ervas-temperos',   'sort_order' => 4],
            ['name' => 'Plantas de Interior','slug' => 'interior',         'sort_order' => 5],
            ['name' => 'Frutíferas',         'slug' => 'frutiferas',       'sort_order' => 6],
            ['name' => 'Cactos',             'slug' => 'cactos',           'sort_order' => 7],
            ['name' => 'Aquáticas',          'slug' => 'aquaticas',        'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat + ['is_active' => true]);
        }
    }
}
