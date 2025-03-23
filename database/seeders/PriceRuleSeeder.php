<?php

namespace Database\Seeders;

use App\Models\PaperType;
use App\Models\PriceRule;
use App\Models\PrintOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy ID của các loại giấy
        $a4Regular = PaperType::where('name', 'Giấy A4 thường')->first()->id;
        $a4Premium = PaperType::where('name', 'Giấy A4 xịn')->first()->id;
        $c160 = PaperType::where('name', 'Giấy C160')->first()->id;

        // Lấy ID của các tùy chọn in
        $bwOneSided = PrintOption::where('name', 'Đen trắng một mặt')->first()->id;
        $bwTwoSided = PrintOption::where('name', 'Đen trắng hai mặt')->first()->id;
        $colorOneSided = PrintOption::where('name', 'Màu một mặt')->first()->id;
        $colorTwoSided = PrintOption::where('name', 'Màu hai mặt')->first()->id;

        // Mảng dữ liệu price rules
        $priceRules = [
            // A4 thường + đen trắng một mặt
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 1000, // 1,000 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 10,
            ],
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 800, // 800 VND/tờ
                'min_quantity' => 11,
                'max_quantity' => 50,
            ],
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 600, // 600 VND/tờ
                'min_quantity' => 51,
                'max_quantity' => null, // Không giới hạn số lượng tối đa
            ],

            // A4 thường + đen trắng hai mặt
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwTwoSided,
                'price_per_page' => 1500, // 1,500 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 5,
            ],
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwTwoSided,
                'price_per_page' => 1300, // 1,300 VND/tờ
                'min_quantity' => 6,
                'max_quantity' => 20,
            ],
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwTwoSided,
                'price_per_page' => 1100, // 1,100 VND/tờ
                'min_quantity' => 21,
                'max_quantity' => null,
            ],

            // A4 thường + màu một mặt
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $colorOneSided,
                'price_per_page' => 5000, // 5,000 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 10,
            ],
            [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $colorOneSided,
                'price_per_page' => 4500, // 4,500 VND/tờ
                'min_quantity' => 11,
                'max_quantity' => null,
            ],

            // A4 xịn + đen trắng một mặt
            [
                'paper_type_id' => $a4Premium,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 1500, // 1,500 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 10,
            ],
            [
                'paper_type_id' => $a4Premium,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 1300, // 1,300 VND/tờ
                'min_quantity' => 11,
                'max_quantity' => null,
            ],

            // Tiếp tục thêm các quy tắc giá khác...
        ];

        // Thêm tất cả quy tắc giá vào cơ sở dữ liệu
        foreach ($priceRules as $rule) {
            PriceRule::create($rule);
        }
    }
}
