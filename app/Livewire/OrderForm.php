<?php

namespace App\Livewire;

use App\Models\File;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaperType;
use App\Models\PrintOption;
use App\Models\User;
use App\Services\FilePageCounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderForm extends Component
{
    use WithFileUploads;

    // User information
    public $name;
    public $email;
    public $phone;
    public $address;

    // Order details
    public $files = [];
    public $filePages = []; // Lưu số trang của từng file
    public $fileTotalPages = 0; // Tổng số trang của tất cả các file
    public $paper_type_id;
    public $print_option_id;
    public $copies = 1; // Số bản in (số lượng bản sao)
    public $quantity = 1; // Tổng số trang cần in (= số trang * số bản)
    public $special_instructions;
    public $pickup_method = 'store';
    public $delivery_address;

    // Calculated fields
    public $total_price = 0;

    // Fetched data
    public $paperTypes = [];
    public $printOptions = [];

    public $showDeliveryModal = false;
    public $isProcessingFiles = false;

    protected $listeners = ['calculatePrice'];

    public function mount()
    {
        // Prepopulate user info if logged in
        if (Auth::check()) {
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->address = $user->address;
        }

        // Load data for dropdowns
        $this->paperTypes = PaperType::where('is_active', true)->get();
        $this->printOptions = PrintOption::where('is_active', true)->get();

        // Set defaults if available
        if ($this->paperTypes->isNotEmpty()) {
            $this->paper_type_id = $this->paperTypes->first()->id;
        }

        if ($this->printOptions->isNotEmpty()) {
            $this->print_option_id = $this->printOptions->first()->id;
        }

        $this->calculatePrice();
    }

    public function calculatePrice()
    {
        $this->total_price = 0;

        // Tính toán số trang dựa trên số trang thực tế và số bản in
        $this->quantity = $this->fileTotalPages * $this->copies;

        if ($this->paper_type_id && $this->print_option_id && $this->quantity > 0) {
            $priceRule = \App\Models\PriceRule::findPriceRule(
                $this->paper_type_id,
                $this->print_option_id,
                $this->quantity
            );

            if ($priceRule) {
                $this->total_price = $priceRule->price_per_page * $this->quantity;
            } else {
                // Nếu không tìm thấy quy tắc giá, hiển thị thông báo hoặc sử dụng giá mặc định
                session()->flash('warning', 'Không tìm thấy quy tắc giá cho tùy chọn in này. Vui lòng liên hệ quản trị viên.');
                $this->total_price = 0;
            }
        }
    }

    public function updatedPaperTypeId()
    {
        $this->calculatePrice();
    }

    public function updatedPrintOptionId()
    {
        $this->calculatePrice();
    }

    public function updatedCopies()
    {
        $this->calculatePrice();
    }

    public function updatedFiles()
    {
        $this->processUploadedFiles();
    }

    /**
     * Xử lý các file được tải lên để đếm số trang
     */
    public function processUploadedFiles()
    {
        $this->isProcessingFiles = true;
        $this->filePages = [];
        $this->fileTotalPages = 0;

        $pageCounter = new FilePageCounter();

        foreach ($this->files as $index => $file) {
            try {
                // Lưu file tạm để đếm số trang
                $tempPath = $file->getRealPath();

                // Đếm số trang
                $pageCount = $pageCounter->count($tempPath);

                // Lưu số trang của file vào mảng
                $this->filePages[$index] = [
                    'name' => $file->getClientOriginalName(),
                    'pages' => $pageCount
                ];

                // Cập nhật tổng số trang
                $this->fileTotalPages += $pageCount;

            } catch (\Exception $e) {
                Log::error('Lỗi khi đếm số trang: ' . $e->getMessage(), [
                    'file' => $file->getClientOriginalName()
                ]);

                // Nếu có lỗi, mặc định là 1 trang
                $this->filePages[$index] = [
                    'name' => $file->getClientOriginalName(),
                    'pages' => 1
                ];
                $this->fileTotalPages += 1;
            }
        }

        $this->isProcessingFiles = false;
        $this->calculatePrice();
    }

    public function removeFile($index)
    {
        if (isset($this->files[$index])) {
            // Giảm tổng số trang khi xóa file
            if (isset($this->filePages[$index])) {
                $this->fileTotalPages -= $this->filePages[$index]['pages'];
                unset($this->filePages[$index]);
            }

            unset($this->files[$index]);
            $this->files = array_values($this->files); // Reindex array
            $this->filePages = array_values($this->filePages); // Reindex array

            $this->calculatePrice();
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:51200|mimes:pdf,doc,docx,jpg,jpeg,png',
            'paper_type_id' => 'required|exists:paper_types,id',
            'print_option_id' => 'required|exists:print_options,id',
            'copies' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
            'pickup_method' => 'required|in:store,delivery',
        ];

        if ($this->pickup_method === 'delivery') {
            $rules['address'] = 'required|string|max:255';
            $rules['email'] = 'required|email|max:255';
            $rules['phone'] = 'required|string|max:20';
        }

        return $rules;
    }

    public function submit()
    {
        $this->validate();

        // Tạo hoặc tìm người dùng
        $userId = null;

        if ($this->email) {
            // Nếu có email, tìm hoặc tạo user
            $user = \App\Models\User::where('email', $this->email)->first();
            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone ?? '',
                    'address' => $this->address ?? '',
                    'password' => bcrypt(uniqid()), // Random password
                ]);
            }
            $userId = $user->id;
        } else {
            // Nếu không có email, tạo user tạm thời với tên + thời gian
            $tempEmail = 'guest_' . time() . '@print.local';
            $user = \App\Models\User::create([
                'name' => $this->name,
                'email' => $tempEmail,
                'phone' => $this->phone ?? '',
                'address' => $this->address ?? '',
                'password' => bcrypt(uniqid()), // Random password
            ]);
            $userId = $user->id;
        }

        // Create order
        $order = \App\Models\Order::create([
            'user_id' => $userId,
            'total_amount' => $this->total_price,
            'status_id' => $this->getDefaultOrderStatusId(),
            'special_instructions' => $this->special_instructions,
            'pickup_method' => $this->pickup_method,
            'delivery_address' => $this->pickup_method === 'delivery' ? $this->delivery_address : null,
        ]);

        $pageCounter = new FilePageCounter();
        $successFiles = [];
        $errorFiles = [];

        // Process uploaded files
        foreach ($this->files as $index => $file) {
            try {
                // Lấy thông tin file
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $size = $file->getSize();
                $mimeType = $file->getMimeType();

                // Tạo tên file an toàn
                $safeFileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;

                // Lưu file vào storage
                $filePath = 'order_files/' . $safeFileName;
                Storage::disk('public')->putFileAs('order_files', $file, $safeFileName);

                // Đếm số trang
                $pageCount = isset($this->filePages[$index]) ? $this->filePages[$index]['pages'] : $pageCounter->count($file->getRealPath());

                // Create order item for each file
                $orderItem = \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'paper_type_id' => $this->paper_type_id,
                    'print_option_id' => $this->print_option_id,
                    'quantity' => $pageCount * $this->copies, // Số trang * số bản in
                    'price' => $this->total_price,
                    'copies' => $this->copies, // Lưu số bản in
                ]);

                // Store file information
                \App\Models\File::create([
                    'order_item_id' => $orderItem->id,
                    'user_id' => $userId,
                    'path' => $filePath,
                    'name' => $originalName,
                    'size' => $size,
                    'type' => $mimeType,
                    'pages' => $pageCount, // Lưu số trang của file
                ]);

                $successFiles[] = $originalName;

            } catch (\Exception $e) {
                Log::error('Lỗi khi xử lý file: ' . $e->getMessage(), [
                    'file' => $file->getClientOriginalName(),
                    'exception' => $e
                ]);

                $errorFiles[] = $file->getClientOriginalName();
            }
        }

        // Nếu không có file nào xử lý thành công, xóa đơn hàng
        if (empty($successFiles)) {
            $order->delete();
            session()->flash('error', 'Đã xảy ra lỗi khi xử lý các file của bạn. Vui lòng thử lại.');
            return;
        }

        // Hiển thị thông báo lỗi nếu có file không xử lý được
        if (!empty($errorFiles)) {
            session()->flash('warning', 'Một số file không thể xử lý: ' . implode(', ', $errorFiles));
        }

        // Reset form
        $this->reset(['name', 'email', 'phone', 'address', 'files', 'filePages', 'fileTotalPages',
            'paper_type_id', 'print_option_id', 'copies', 'quantity', 'special_instructions',
            'pickup_method', 'delivery_address']);
        $this->total_price = 0;

        session()->flash('message', 'Đơn hàng của bạn đã được gửi thành công!');
    }

    public function render()
    {
        return view('livewire.order-form', [
            'paperTypes' => \App\Models\PaperType::all(),
            'printOptions' => \App\Models\PrintOption::all(),
        ])->layout('components.layouts.app');
    }

    public function showDeliveryForm()
    {
        // Chỉ định phương thức giao hàng là 'delivery' khi hiển thị form giao hàng
        $this->pickup_method = 'delivery';
        $this->showDeliveryModal = true;
    }

    public function hideDeliveryForm()
    {
        $this->showDeliveryModal = false;
    }

    /**
     * Lấy ID trạng thái mặc định cho đơn hàng mới
     *
     * @return int ID của trạng thái đơn hàng mặc định
     */
    protected function getDefaultOrderStatusId(): int
    {
        // Thử tìm trạng thái "Đang chờ" (tiếng Việt)
        $pendingStatus = \App\Models\OrderStatus::where('name', 'Đang chờ')->first();

        // Nếu không tìm thấy, thử tìm trạng thái "Pending" (tiếng Anh)
        if (!$pendingStatus) {
            $pendingStatus = \App\Models\OrderStatus::where('name', 'Pending')->first();
        }

        // Nếu vẫn không tìm thấy, lấy trạng thái đầu tiên trong cơ sở dữ liệu
        if (!$pendingStatus) {
            $pendingStatus = \App\Models\OrderStatus::first();
        }

        // Nếu không có trạng thái nào trong cơ sở dữ liệu, tạo một trạng thái mới
        if (!$pendingStatus) {
            $pendingStatus = \App\Models\OrderStatus::create([
                'name' => 'Đang chờ',
                'description' => 'Đơn hàng đang chờ xử lý',
                'color' => 'blue',
            ]);
        }

        return $pendingStatus->id;
    }
}
