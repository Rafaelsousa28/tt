<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    public function index(): JsonResponse
    {
        $heroes = Banner::active()->hero()->orderBy('sort_order')->get();
        $promos = Banner::active()->promo()->orderBy('sort_order')->limit(4)->get();

        return response()->json([
            'heroes' => $heroes->map->append('image_url'),
            'promos' => $promos->map->append('image_url'),
        ]);
    }
}
