<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario; // <-- Impor Model
use Illuminate\Http\Request;

class ScenarioController extends Controller
{
    /**
     * Menampilkan satu skenario spesifik.
     * Ini adalah implementasi API 19 (GET /scenario/{id})
     *
     * @param  \App\Models\Scenario  $scenario
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Scenario $scenario)
    {
        // 1. (Otomatis) Laravel sudah menemukan 'Scenario' berdasarkan
        //    ID di URL. Jika tidak ada, ia otomatis 404.

        // 2. (Langkah 6 di diagram)
        //    Kita perlu memuat "relasi" pilihan jawabannya.
        //    Ini adalah 'JOIN' atau query kedua yang efisien.
        $scenario->load('options');

        // 3. (Langkah 7 di diagram)
        //    Kembalikan data sebagai JSON.
        return response()->json($scenario);
    }
}