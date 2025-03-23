<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Đang chờ',
                'description' => 'Đơn hàng đang chờ xử lý',
                'color' => 'blue',
            ],
            [
                'name' => 'Đang xử lý',
                'description' => 'Đơn hàng đang được xử lý',
                'color' => 'yellow',
            ],
            [
                'name' => 'Sẵn sàng',
                'description' => 'Đơn hàng đã sẵn sàng để lấy',
                'color' => 'green',
            ],
            [
                'name' => 'Hoàn thành',
                'description' => 'Đơn hàng đã hoàn thành',
                'color' => 'green',
            ],
            [
                'name' => 'Hủy bỏ',
                'description' => 'Đơn hàng đã bị hủy',
                'color' => 'red',
            ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::create($status);
        }
    }
}
