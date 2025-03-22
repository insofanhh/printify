<?php

namespace Database\Seeders;

use App\Models\PrintOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrintOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $printOptions = [
            [
                'name' => 'In trắng đen một mặt',
                'sides' => 'one_sided',
                'description' => 'In một mặt với mực đen',
                'is_active' => true,
                'price' => 1000, // Giá mỗi tờ (VND)
            ],
            [
                'name' => 'In trắng đen hai mặt',
                'sides' => 'two_sided',
                'description' => 'In hai mặt với mực đen',
                'is_active' => true,
                'price' => 1800,
            ],
            [
                'name' => 'In màu một mặt',
                'sides' => 'one_sided',
                'description' => 'In một mặt với mực màu',
                'is_active' => true,
                'price' => 3000,
            ],
            [
                'name' => 'In màu hai mặt',
                'sides' => 'two_sided',
                'description' => 'In hai mặt với mực màu',
                'is_active' => true,
                'price' => 5500,
            ],
            [
                'name' => 'In chất lượng cao',
                'sides' => 'one_sided',
                'description' => 'In một mặt với chất lượng cao, phù hợp với ảnh và đồ họa',
                'is_active' => true,
                'price' => 8000,
            ],
        ];

        foreach ($printOptions as $printOption) {
            PrintOption::create($printOption);
        }
    }
}
