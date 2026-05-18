<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::firstOrCreate(['title' => 'Natureza que floresce em casa'], [
            'subtitle'    => 'Mudas cítricas, ornamentais e plantas exóticas com frete para todo o Brasil.',
            'button_text' => 'Explorar Plantas',
            'button_url'  => '/plantas',
            'type'        => 'hero',
            'is_active'   => true,
            'sort_order'  => 1,
            'image'       => 'banners/hero-placeholder.jpg',
        ]);

        Banner::firstOrCreate(['title' => '🍋 Mudas Cítricas com 20% OFF'], [
            'subtitle'    => 'Use o cupom CITRICO20 e aproveite a promoção.',
            'button_text' => 'Ver Promoção',
            'button_url'  => '/plantas?categoria=mudas-citricas',
            'type'        => 'promo',
            'is_active'   => true,
            'sort_order'  => 1,
            'image'       => 'banners/promo-citricos.jpg',
        ]);
    }
}
