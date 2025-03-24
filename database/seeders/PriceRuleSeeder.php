<?php

namespace Database\Seeders;

use App\Models\PaperType;
use App\Models\PriceRule;
use App\Models\PrintOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class PriceRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các loại giấy từ cơ sở dữ liệu
        $a4RegularPaper = PaperType::where('name', 'Giấy A4 thường')->first();
        $a4PremiumPaper = PaperType::where('name', 'Giấy A4 xịn double A')->first() 
                        ?? PaperType::where('name', 'like', '%A4 xịn%')->first(); // Tìm kiếm tên tương tự
        $c160Paper = PaperType::where('name', 'Giấy C160')->first();

        // Lấy các tùy chọn in từ cơ sở dữ liệu
        $bwOneSidedOption = PrintOption::where('name', 'Đen trắng một mặt')->first();
        $bwTwoSidedOption = PrintOption::where('name', 'Đen trắng hai mặt')->first();
        $colorOneSidedOption = PrintOption::where('name', 'Màu một mặt')->first();
        $colorTwoSidedOption = PrintOption::where('name', 'Màu hai mặt')->first();

        // Kiểm tra xem các loại giấy và tùy chọn in có tồn tại không
        if (!$a4RegularPaper) {
            $this->command->warn('Không tìm thấy loại giấy "Giấy A4 thường". Bỏ qua các quy tắc giá liên quan.');
            return;
        }

        // Gán ID loại giấy, mặc định null nếu không tìm thấy loại giấy
        $a4Regular = $a4RegularPaper->id;
        $a4Premium = $a4PremiumPaper ? $a4PremiumPaper->id : null;
        $c160 = $c160Paper ? $c160Paper->id : null;

        // Gán ID tùy chọn in, mặc định null nếu không tìm thấy tùy chọn
        $bwOneSided = $bwOneSidedOption ? $bwOneSidedOption->id : null;
        $bwTwoSided = $bwTwoSidedOption ? $bwTwoSidedOption->id : null;
        $colorOneSided = $colorOneSidedOption ? $colorOneSidedOption->id : null;
        $colorTwoSided = $colorTwoSidedOption ? $colorTwoSidedOption->id : null;

        // Log thông tin về các ID đã tìm thấy
        $this->command->info('Đã tìm thấy các loại giấy và tùy chọn in:');
        $this->command->info('A4 Regular: ' . ($a4Regular ?? 'Không tìm thấy'));
        $this->command->info('A4 Premium: ' . ($a4Premium ?? 'Không tìm thấy'));
        $this->command->info('C160: ' . ($c160 ?? 'Không tìm thấy'));
        $this->command->info('BW One Sided: ' . ($bwOneSided ?? 'Không tìm thấy'));
        $this->command->info('BW Two Sided: ' . ($bwTwoSided ?? 'Không tìm thấy'));
        $this->command->info('Color One Sided: ' . ($colorOneSided ?? 'Không tìm thấy'));
        $this->command->info('Color Two Sided: ' . ($colorTwoSided ?? 'Không tìm thấy'));

        // Mảng dữ liệu price rules
        $priceRules = [];

        // A4 thường + đen trắng một mặt
        if ($a4Regular && $bwOneSided) {
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 1000, // 1,000 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 10,
            ];
            
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 800, // 800 VND/tờ
                'min_quantity' => 11,
                'max_quantity' => 50,
            ];
            
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 600, // 600 VND/tờ
                'min_quantity' => 51,
                'max_quantity' => null, // Không giới hạn số lượng tối đa
            ];
        }

        // A4 thường + đen trắng hai mặt
        if ($a4Regular && $bwTwoSided) {
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwTwoSided,
                'price_per_page' => 1500, // 1,500 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 5,
            ];
            
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwTwoSided,
                'price_per_page' => 1300, // 1,300 VND/tờ
                'min_quantity' => 6,
                'max_quantity' => 20,
            ];
            
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $bwTwoSided,
                'price_per_page' => 1100, // 1,100 VND/tờ
                'min_quantity' => 21,
                'max_quantity' => null,
            ];
        }

        // A4 thường + màu một mặt
        if ($a4Regular && $colorOneSided) {
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $colorOneSided,
                'price_per_page' => 5000, // 5,000 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 10,
            ];
            
            $priceRules[] = [
                'paper_type_id' => $a4Regular,
                'print_option_id' => $colorOneSided,
                'price_per_page' => 4500, // 4,500 VND/tờ
                'min_quantity' => 11,
                'max_quantity' => null,
            ];
        }

        // A4 xịn + đen trắng một mặt
        if ($a4Premium && $bwOneSided) {
            $priceRules[] = [
                'paper_type_id' => $a4Premium,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 1500, // 1,500 VND/tờ
                'min_quantity' => 1,
                'max_quantity' => 10,
            ];
            
            $priceRules[] = [
                'paper_type_id' => $a4Premium,
                'print_option_id' => $bwOneSided,
                'price_per_page' => 1300, // 1,300 VND/tờ
                'min_quantity' => 11,
                'max_quantity' => null,
            ];
        }

        // Thêm tất cả quy tắc giá vào cơ sở dữ liệu
        foreach ($priceRules as $rule) {
            PriceRule::create($rule);
        }

        $this->command->info('Đã tạo ' . count($priceRules) . ' quy tắc giá.');
    }
}
