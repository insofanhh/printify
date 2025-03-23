<?php

namespace Database\Seeders;

use App\Models\PaperType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaperTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paperTypes = [
            [
                'name' => 'Giấy A4 thường',
                'description' => 'Giấy in thông thường khổ A4, 70gsm',
                'is_active' => true,
            ],
            [
                'name' => 'Giấy A4 xịn double A',
                'description' => 'Giấy in cao cấp khổ A4, 100gsm',
                'is_active' => true,
            ],
            [
                'name' => 'Giấy C160',
                'description' => 'Giấy in thường khổ C160',
                'is_active' => true,
            ],
        ];

        foreach ($paperTypes as $type) {
            PaperType::create($type);
        }
    }
}
