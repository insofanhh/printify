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
                'name' => 'Đen trắng một mặt',
                'sides' => 'one_sided',
                'color' => 'black_white',
                'description' => 'In đen trắng một mặt',
                'is_active' => true,
            ],
            [
                'name' => 'Đen trắng hai mặt',
                'sides' => 'two_sided',
                'color' => 'black_white',
                'description' => 'In đen trắng hai mặt',
                'is_active' => true,
            ],
            [
                'name' => 'Màu một mặt',
                'sides' => 'one_sided',
                'color' => 'color',
                'description' => 'In màu một mặt',
                'is_active' => true,
            ],
            [
                'name' => 'Màu hai mặt',
                'sides' => 'two_sided',
                'color' => 'color',
                'description' => 'In màu hai mặt',
                'is_active' => true,
            ],
        ];

        foreach ($printOptions as $option) {
            PrintOption::create($option);
        }
    }
}
