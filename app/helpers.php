<?php

/**
 * File helpers.php chứa các hàm trợ giúp chung cho ứng dụng
 * 
 * Các hàm này có thể được sử dụng ở bất kỳ đâu trong ứng dụng
 */

if (!function_exists('format_currency')) {
    /**
     * Định dạng số tiền theo tiền tệ VND
     *
     * @param int|float $amount Số tiền cần định dạng
     * @return string Chuỗi đã được định dạng (ví dụ: 1.500 ₫)
     */
    function format_currency($amount)
    {
        return number_format($amount, 0, ',', '.') . ' ₫';
    }
}

if (!function_exists('format_bytes')) {
    /**
     * Định dạng kích thước file từ bytes sang đơn vị dễ đọc
     *
     * @param int $bytes Kích thước file tính bằng bytes
     * @param int $precision Số chữ số thập phân
     * @return string Chuỗi đã được định dạng (ví dụ: 1.5 MB)
     */
    function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
