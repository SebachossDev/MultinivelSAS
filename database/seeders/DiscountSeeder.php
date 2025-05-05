<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = [
            ['level' => 'Iniciante', 'discount' => 5.00],
            ['level' => 'Aprendiz', 'discount' => 10.00],
            ['level' => 'Intermedio', 'discount' => 15.00],
            ['level' => 'Avanzado', 'discount' => 20.00],
            ['level' => 'Experto', 'discount' => 25.00],
        ];

        // Opcional: Vaciar la tabla antes de sembrar si quieres evitar duplicados
        // DB::table('discounts')->truncate();

        foreach ($levels as $levelData) {
            DB::table('discounts')->insert([
                'level' => $levelData['level'],
                'discount' => $levelData['discount'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}