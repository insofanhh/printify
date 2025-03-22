<?php

namespace App\Livewire;

use App\Models\File;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaperType;
use App\Models\PrintOption;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
    public $paper_type_id;
    public $print_option_id;
    public $quantity = 1;
    public $special_instructions;
    public $pickup_method = 'store';
    public $delivery_address;
    
    // Calculated fields
    public $total_price = 0;
    
    // Fetched data
    public $paperTypes = [];
    public $printOptions = [];
    
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

        if ($this->paper_type_id && $this->print_option_id && $this->quantity > 0) {
            $paperType = \App\Models\PaperType::find($this->paper_type_id);
            $printOption = \App\Models\PrintOption::find($this->print_option_id);
            
            if ($paperType && $printOption) {
                $this->total_price = ($paperType->price + $printOption->price) * $this->quantity;
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
    
    public function updatedQuantity()
    {
        $this->calculatePrice();
    }
    
    public function removeFile($index)
    {
        if (isset($this->files[$index])) {
            unset($this->files[$index]);
            $this->files = array_values($this->files); // Reindex array
        }
    }
    
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'paper_type_id' => 'required|exists:paper_types,id',
            'print_option_id' => 'required|exists:print_options,id',
            'quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
            'pickup_method' => 'required|in:store,delivery',
        ];

        if ($this->pickup_method === 'delivery') {
            $rules['delivery_address'] = 'required|string|max:255';
        }

        return $rules;
    }
    
    public function submit()
    {
        $this->validate();
        
        // Find or create user
        $user = \App\Models\User::where('email', $this->email)->first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'password' => bcrypt(uniqid()), // Random password
            ]);
        }
        
        // Create order
        $order = \App\Models\Order::create([
            'user_id' => $user->id,
            'total_amount' => $this->total_price,
            'status_id' => \App\Models\OrderStatus::where('name', 'Pending')->first()->id,
            'special_instructions' => $this->special_instructions,
            'pickup_method' => $this->pickup_method,
            'delivery_address' => $this->pickup_method === 'delivery' ? $this->delivery_address : null,
        ]);
        
        // Process uploaded files
        foreach ($this->files as $file) {
            $filePath = $file->store('order_files', 'public');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();
            
            // Create order item for each file
            $orderItem = \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'paper_type_id' => $this->paper_type_id,
                'print_option_id' => $this->print_option_id,
                'quantity' => $this->quantity,
                'price' => $this->total_price,
            ]);
            
            // Store file information
            \App\Models\File::create([
                'order_item_id' => $orderItem->id,
                'path' => $filePath,
                'name' => $fileName,
                'size' => $fileSize,
                'type' => $fileType,
            ]);
        }
        
        // Reset form
        $this->reset(['name', 'email', 'phone', 'address', 'files', 'paper_type_id', 
            'print_option_id', 'quantity', 'special_instructions', 'pickup_method', 
            'delivery_address']);
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
}
