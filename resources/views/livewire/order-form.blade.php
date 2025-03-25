<div class="min-h-screen flex items-center justify-center py-12">
    <div class="form-container p-8 rounded-xl shadow-lg max-w-2xl w-full" style="background: linear-gradient(to bottom right, #e0f2fe, #fff);">
        <!-- Tiêu đề -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-blue-600">PRINTIFY</h1>
            <p class="text-xl text-gray-600 mt-2">Đặt tài liệu</p>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="submit">
            <!-- Thông tin khách hàng -->
            <div class="mb-6 bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Thông tin khách hàng</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Họ và tên <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập tên của bạn">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4 italic">Lưu ý: Nếu bạn chọn giao hàng tận nơi, bạn sẽ cần cung cấp đầy đủ thông tin liên hệ.</p>
            </div>

            <!-- File in -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">File in</h2>
                <div class="border-2 border-dashed border-blue-400 bg-blue-50 p-6 text-center rounded-md">
                    <input type="file" wire:model="files" class="hidden" id="file-upload" multiple>
                    <label for="file-upload" class="cursor-pointer text-blue-600 hover:text-blue-800 font-medium">Chọn file <span class="text-red-500">*</span></label>
                    <p class="text-sm text-gray-500 mt-2">Hỗ trợ PDF, WORD, và hình ảnh (JPG, PNG). Kích thước tối đa 10MB.</p>
                    <div wire:loading wire:target="files" class="mt-2 text-sm text-blue-600">
                        Đang tải file...
                    </div>
                    @error('files.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if (count($files) > 0)
                    <div class="mt-4">
                        <h4 class="font-medium mb-2 text-gray-700">File đã chọn:</h4>
                        <div class="space-y-2">
                            @foreach ($files as $index => $file)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded border">
                                    <div class="flex items-center flex-grow">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <span class="block">{{ $file->getClientOriginalName() }}</span>
                                            @if($isProcessingFiles)
                                                <span class="text-xs text-blue-600">Đang đếm số trang...</span>
                                            @elseif(isset($filePages[$index]))
                                                <span class="text-xs text-gray-500">{{ $filePages[$index]['pages'] }} trang</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removeFile({{ $index }})" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        @if($fileTotalPages > 0)
                            <div class="mt-3 p-3 bg-blue-50 rounded border border-blue-200">
                                <p class="text-sm text-blue-700">
                                    <span class="font-semibold">Tổng số trang:</span> {{ $fileTotalPages }} trang
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Tùy chọn in -->
            <div class="mb-6 bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Tùy chọn in</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Loại giấy <span class="text-red-500">*</span></label>
                        <select wire:model="paper_type_id" wire:change="calculatePrice" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Chọn loại giấy --</option>
                            @foreach ($paperTypes as $paperType)
                                <option value="{{ $paperType->id }}">{{ $paperType->name }}</option>
                            @endforeach
                        </select>
                        @error('paper_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kiểu in <span class="text-red-500">*</span></label>
                        <select wire:model="print_option_id" wire:change="calculatePrice" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Chọn kiểu in --</option>
                            @foreach ($printOptions as $printOption)
                                <option value="{{ $printOption->id }}">{{ $printOption->name }} ({{ $printOption->sides == 'one_sided' ? 'Một mặt' : 'Hai mặt' }})</option>
                            @endforeach
                        </select>
                        @error('print_option_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Số bản in <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="copies" wire:change="calculatePrice" min="1" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('copies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Thành tiền -->
            <div class="mb-6 bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Thông tin in</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                    <div>
                        <p class="text-sm text-gray-600">Tổng số trang</p>
                        <p class="text-lg font-semibold">{{ $fileTotalPages }} trang</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Số bản in</p>
                        <p class="text-lg font-semibold">{{ $copies }} bản</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tổng số trang in</p>
                        <p class="text-lg font-semibold">{{ $quantity }} trang</p>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-700">Thành tiền</h3>
                    <p class="text-2xl font-bold text-green-600 mt-2 bg-green-50 inline-block px-4 py-1 rounded-md">
                        {{ number_format($total_price, 0, ',', '.') }} VND
                    </p>
                </div>
            </div>

            <!-- Yêu cầu đặc biệt -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Yêu cầu đặc biệt</label>
                <textarea wire:model="special_instructions" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Nhập yêu cầu đặc biệt (nếu có)"></textarea>
            </div>

            <!-- Nút submit -->
            <div class="flex flex-col md:flex-row justify-center space-y-4 md:space-y-0 md:space-x-4">
                <button type="submit" wire:click="$set('pickup_method', 'store')" wire:loading.attr="disabled" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-300 flex items-center justify-center">
                    <span class="mr-2" wire:loading.remove wire:target="submit">🛒</span>
                    <span wire:loading.remove wire:target="submit">ĐẶT NGAY CỬA HÀNG</span>
                    <span wire:loading wire:target="submit" class="inline-flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Đang xử lý...
                    </span>
                </button>
                <button type="button" wire:click="showDeliveryForm" class="border border-blue-600 text-blue-600 px-6 py-3 rounded-md hover:bg-blue-100 transition duration-300 flex items-center justify-center">
                    <span class="mr-2">🚚</span> GIAO HÀNG TẬN NƠI
                </button>
            </div>

            <!-- Modal giao hàng tận nơi -->
            @if($showDeliveryModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                    <h3 class="text-lg font-bold mb-4">Thông tin giao hàng</h3>

                    <!-- Thông tin liên hệ cho giao hàng -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" wire:model="email" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập email của bạn">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="tel" wire:model="phone" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nhập số điện thoại">
                            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Địa chỉ giao hàng <span class="text-red-500">*</span></label>
                        <textarea wire:model="address" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Nhập địa chỉ giao hàng"></textarea>
                        @error('delivery_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" wire:click="hideDeliveryForm" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Hủy</button>
                        <button type="submit" wire:click="$set('pickup_method', 'delivery')" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <span wire:loading.remove wire:target="submit">Xác nhận giao hàng</span>
                            <span wire:loading wire:target="submit">Đang xử lý...</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>
