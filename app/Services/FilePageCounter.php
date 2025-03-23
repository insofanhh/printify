<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Illuminate\Support\Facades\Log;

class FilePageCounter
{
    /**
     * Đếm số trang trong một file dựa trên loại file
     *
     * @param string $filePath Đường dẫn đến file cần đếm trang
     * @return int Số trang của file, mặc định là 1 nếu không thể đếm
     */
    public function count(string $filePath): int
    {
        // Xác định loại file dựa vào phần mở rộng
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        try {
            switch ($extension) {
                case 'pdf':
                    return $this->countPdfPages($filePath);
                case 'docx':
                    return $this->countDocxPages($filePath);
                case 'doc':
                    // Đối với file DOC cũ, có thể ước tính số trang dựa trên kích thước
                    $fileSize = filesize($filePath);
                    return max(1, ceil($fileSize / 20000)); // Ước tính: 20KB cho mỗi trang
                default:
                    // Đối với các loại file khác (jpg, png,...), mặc định là 1 trang
                    return 1;
            }
        } catch (Exception $e) {
            Log::error('Lỗi khi đếm số trang: ' . $e->getMessage(), [
                'file' => $filePath,
                'exception' => $e
            ]);
            return 1; // Mặc định là 1 trang nếu có lỗi
        }
    }
    
    /**
     * Đếm số trang trong file PDF
     *
     * @param string $filePath Đường dẫn đến file PDF
     * @return int Số trang của file PDF
     */
    private function countPdfPages(string $filePath): int
    {
        // Đọc nội dung file PDF thành chuỗi nhưng chỉ đọc một phần để tiết kiệm bộ nhớ
        $content = file_get_contents($filePath, false, null, 0, 50000); // Đọc 50KB đầu tiên
        
        // Sử dụng regex để tìm số trang từ cấu trúc PDF
        // Pattern 1: Tìm "/Pages x 0 R" trong file PDF
        if (preg_match('/\/Pages\s+(\d+)\s+0\s+R/i', $content, $matches)) {
            return $this->parsePdfObj($filePath, $matches[1]);
        }
        
        // Pattern 2: Tìm "/Count x" trong file PDF
        if (preg_match('/\/Count\s+(\d+)/i', $content, $matches)) {
            return (int) $matches[1];
        }
        
        // Nếu không tìm thấy, đọc toàn bộ file để tìm kiếm
        $content = file_get_contents($filePath);
        
        // Đếm số lần xuất hiện của chuỗi "/Page"
        $pageCount = substr_count($content, '/Page');
        if ($pageCount > 0) {
            return $pageCount;
        }
        
        // Nếu không thể xác định, mặc định là 1 trang
        return 1;
    }
    
    /**
     * Phân tích đối tượng PDF để tìm số trang
     */
    private function parsePdfObj(string $filePath, string $objId): int
    {
        $content = file_get_contents($filePath);
        
        // Tìm đối tượng Pages trong file PDF
        if (preg_match('/' . $objId . '\s+0\s+obj.*?\/Count\s+(\d+)/s', $content, $matches)) {
            return (int) $matches[1];
        }
        
        return 1;
    }
    
    /**
     * Đếm số trang trong file DOCX
     *
     * @param string $filePath Đường dẫn đến file DOCX
     * @return int Số trang của file DOCX
     */
    private function countDocxPages(string $filePath): int
    {
        // Mở file DOCX như một file nén ZIP
        $zip = new ZipArchive();
        if ($zip->open($filePath) === true) {
            // Đọc file đếm trang từ [Content_Types].xml hoặc docProps/app.xml
            if (($xml = $zip->getFromName('docProps/app.xml')) !== false) {
                $zip->close();
                
                // Tìm thẻ <Pages> trong XML
                if (preg_match('/<Pages>(\d+)<\/Pages>/i', $xml, $matches)) {
                    return (int) $matches[1];
                }
            }
            
            // Nếu không tìm thấy, thử đếm số đoạn văn từ document.xml
            if (($xml = $zip->getFromName('word/document.xml')) !== false) {
                $zip->close();
                
                // Đếm số đoạn văn và ước tính số trang (trung bình 10 đoạn/trang)
                $paragraphCount = substr_count($xml, '<w:p ');
                $paragraphCount += substr_count($xml, '<w:p>');
                
                return max(1, ceil($paragraphCount / 10));
            }
            
            $zip->close();
        }
        
        // Nếu không thể mở file hoặc không tìm thấy thông tin số trang
        // Ước tính dựa trên kích thước file
        $fileSize = filesize($filePath);
        return max(1, ceil($fileSize / 15000)); // Ước tính: 15KB cho mỗi trang
    }
} 