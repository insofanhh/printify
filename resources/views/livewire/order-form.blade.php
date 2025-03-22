<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Đặt in tài liệu</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Thông tin khách hàng -->
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b text-gray-700">Thông tin khách hàng</h3>
            </div>

            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Họ và tên <span class="text-red-500">*</span></label>
                <input type="text" id="name" wire:model="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" wire:model="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="text" id="phone" wire:model="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="address" class="block mb-2 text-sm font-medium text-gray-700">Địa chỉ</label>
                <input type="text" id="address" wire:model="address" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Upload file -->
            <div class="col-span-1 md:col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b text-gray-700">File cần in</h3>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label for="files" class="block mb-2 text-sm font-medium text-gray-700">Chọn file <span class="text-red-500">*</span></label>
                <input type="file" wire:model="files" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="mt-1 text-sm text-gray-500">Hỗ trợ PDF, Word, và hình ảnh (JPG, PNG). Kích thước tối đa 10MB.</div>
                <div wire:loading wire:target="files" class="mt-2 text-sm text-blue-600">
                    Đang tải file...
                </div>
                @error('files.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            @if (count($files) > 0)
                <div class="col-span-1 md:col-span-2">
                    <h4 class="font-medium mb-2 text-gray-700">File đã chọn:</h4>
                    <div class="space-y-2">
                        @foreach ($files as $index => $file)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded border">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>{{ $file->getClientOriginalName() }}</span>
                                </div>
                                <button type="button" wire:click="removeFile({{ $index }})" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tùy chọn in -->
            <div class="col-span-1 md:col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b text-gray-700">Tùy chọn in</h3>
            </div>

            <div>
                <label for="paper_type_id" class="block mb-2 text-sm font-medium text-gray-700">Loại giấy <span class="text-red-500">*</span></label>
                <select id="paper_type_id" wire:model="paper_type_id" wire:change="calculatePrice" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn loại giấy --</option>
                    @foreach ($paperTypes as $paperType)
                        <option value="{{ $paperType->id }}">{{ $paperType->name }}</option>
                    @endforeach
                </select>
                @error('paper_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="print_option_id" class="block mb-2 text-sm font-medium text-gray-700">Kiểu in <span class="text-red-500">*</span></label>
                <select id="print_option_id" wire:model="print_option_id" wire:change="calculatePrice" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn kiểu in --</option>
                    @foreach ($printOptions as $printOption)
                        <option value="{{ $printOption->id }}">{{ $printOption->name }} ({{ $printOption->sides == 'one_sided' ? 'Một mặt' : 'Hai mặt' }})</option>
                    @endforeach
                </select>
                @error('print_option_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="quantity" class="block mb-2 text-sm font-medium text-gray-700">Số lượng <span class="text-red-500">*</span></label>
                <input type="number" id="quantity" wire:model="quantity" wire:change="calculatePrice" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="total_price" class="block mb-2 text-sm font-medium text-gray-700">Thành tiền</label>
                <div class="w-full px-3 py-2 border border-gray-300 bg-gray-50 rounded-md">
                    {{ number_format($total_price, 0, ',', '.') }} VND
                </div>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label for="special_instructions" class="block mb-2 text-sm font-medium text-gray-700">Yêu cầu đặc biệt</label>
                <textarea id="special_instructions" wire:model="special_instructions" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- Phương thức nhận hàng -->
            <div class="col-span-1 md:col-span-2 mt-6">
                <h3 class="text-lg font-semibold mb-4 pb-2 border-b text-gray-700">Phương thức nhận hàng</h3>
            </div>

            <div class="col-span-1 md:col-span-2">
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="pickup_method" value="store" class="form-radio h-5 w-5 text-blue-600">
                        <span class="ml-2 text-gray-700">Nhận tại cửa hàng</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model="pickup_method" value="delivery" class="form-radio h-5 w-5 text-blue-600">
                        <span class="ml-2 text-gray-700">Giao hàng tận nơi</span>
                    </label>
                </div>
            </div>

            @if ($pickup_method === 'delivery')
                <div class="col-span-1 md:col-span-2">
                    <label for="delivery_address" class="block mb-2 text-sm font-medium text-gray-700">Địa chỉ giao hàng <span class="text-red-500">*</span></label>
                    <textarea id="delivery_address" wire:model="delivery_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('delivery_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Submit button -->
            <div class="col-span-1 md:col-span-2 mt-6">
                <button type="submit" wire:loading.attr="disabled" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <span wire:loading.remove wire:target="submit">Gửi đơn hàng</span>
                    <span wire:loading wire:target="submit" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang xử lý...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
