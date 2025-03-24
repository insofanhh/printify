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
                'name' => 'Pending',
                'description' => 'Đơn hàng đang chờ xử lý',
                'color' => 'blue',
            ],
            [
                'name' => 'Processing',
                'description' => 'Đơn hàng đang được xử lý',
                'color' => 'yellow',
            ],
            [
                'name' => 'Ready',
                'description' => 'Đơn hàng đã sẵn sàng để lấy',
                'color' => 'green',
            ],
            [
                'name' => 'Completed',
                'description' => 'Đơn hàng đã hoàn thành',
                'color' => 'green',
            ],
            [
                'name' => 'Cancelled',
                'description' => 'Đơn hàng đã bị hủy',
                'color' => 'red',
            ],
        ];

        foreach ($statuses as $status) {
            OrderStatus::create($status);
        }
    }
}
