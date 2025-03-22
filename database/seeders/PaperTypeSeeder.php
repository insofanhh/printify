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
                'description' => 'Khổ giấy A4 (210x297mm), định lượng 80gsm',
                'is_active' => true,
                'price' => 500, // Giá mỗi tờ (VND)
            ],
            [
                'name' => 'Giấy A4 cao cấp',
                'description' => 'Khổ giấy A4 (210x297mm), định lượng 100gsm',
                'is_active' => true,
                'price' => 1000,
            ],
            [
                'name' => 'Giấy A3 thường',
                'description' => 'Khổ giấy A3 (297x420mm), định lượng 80gsm',
                'is_active' => true,
                'price' => 1500,
            ],
            [
                'name' => 'Giấy A3 cao cấp',
                'description' => 'Khổ giấy A3 (297x420mm), định lượng 100gsm',
                'is_active' => true,
                'price' => 2000,
            ],
            [
                'name' => 'Giấy photo màu',
                'description' => 'Khổ giấy A4 (210x297mm), định lượng 100gsm, dành cho in màu',
                'is_active' => true,
                'price' => 2500,
            ],
        ];

        foreach ($paperTypes as $paperType) {
            PaperType::create($paperType);
        }
    }
}
