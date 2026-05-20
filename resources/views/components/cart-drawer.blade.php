{{-- Cart Drawer Component --}}
<div x-data="{ open: false }" 
     @toggle-cart.window="open = !open"
     class="relative">
    
    @php
    $cartItems = [];
    $cartTotal = 0;
    $cartCount = 0;
    
    if(auth()->check()) {
        $cart = \App\Models\Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();
        
        if($cart && !$cart->isEmpty()) {
            $cartItems = $cart->items;
            $cartTotal = $cart->total_amount;
            $cartCount = $cart->getTotalItems();
        }
    }
    @endphp
    
    {{-- Backdrop --}}
    <div x-show="open" 
         @click="open = false"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="cart-drawer-backdrop"
         style="display: none;"
         aria-hidden="true"></div>
    
    {{-- Drawer --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="cart-drawer"
         style="display: none;"
         role="dialog"
         aria-modal="true"
         aria-label="Shopping cart">
        
        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Keranjang ({{ $cartCount }})</h2>
            <button @click="open = false" 
                    class="icon-button text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                    aria-label="Close cart">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        
        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @forelse($cartItems as $item)
                <div class="flex gap-4 p-3 bg-gray-50 rounded-xl">
                    {{-- Image --}}
                    <div class="w-20 h-20 bg-white rounded-lg flex-shrink-0 overflow-hidden">
                        @if($item->product && method_exists($item->product, 'getFirstImage') && $item->product->getFirstImage())
                            <img src="{{ asset($item->product->getFirstImage()) }}" 
                                 alt="{{ $item->product_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <i class="ph ph-package text-2xl text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Details --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 line-clamp-2 mb-1">{{ $item->product_name }}</h4>
                        <p class="text-primary-600 font-bold text-sm">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        
                        {{-- Quantity Controls --}}
                        <div class="flex items-center justify-between mt-2">
                            <form action="{{ route('user.cart.update', $item) }}" 
                                  method="POST" 
                                  class="flex items-center"
                                  x-data="{ qty: {{ $item->quantity }}, submitting: false }"
                                  x-ref="updateForm">
                                @csrf
                                @method('PATCH')
                                <div class="flex items-center border border-gray-200 rounded-lg bg-white overflow-hidden h-8">
                                    <button type="button" 
                                            @click="if(qty > 1 && !submitting) { qty--; submitting = true; $nextTick(() => $refs.updateForm.submit()) }" 
                                            class="w-8 h-full flex items-center justify-center text-gray-500 hover:bg-gray-50 border-r border-gray-200 disabled:opacity-50"
                                            :disabled="qty <= 1 || submitting">
                                        <i class="ph ph-minus text-xs"></i>
                                    </button>
                                    <input type="number" 
                                           name="quantity" 
                                           x-model="qty" 
                                           min="1" 
                                           class="w-10 h-full text-center text-gray-900 font-bold border-none p-0 text-xs"
                                           :disabled="submitting">
                                    <button type="button" 
                                            @click="if(!submitting) { qty++; submitting = true; $nextTick(() => $refs.updateForm.submit()) }" 
                                            class="w-8 h-full flex items-center justify-center text-gray-500 hover:bg-gray-50 border-l border-gray-200 disabled:opacity-50"
                                            :disabled="submitting">
                                        <i class="ph ph-plus text-xs"></i>
                                    </button>
                                </div>
                            </form>
                            
                            {{-- Remove Button --}}
                            <form action="{{ route('user.cart.remove', $item) }}" 
                                  method="POST"
                                  x-data="{ deleting: false }"
                                  @submit.prevent="
                                      deleting = true;
                                      $el.submit();
                                  ">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="icon-button text-gray-400 hover:text-red-500 transition-colors disabled:opacity-50"
                                        :disabled="deleting"
                                        aria-label="Remove item">
                                    <i class="ph ph-trash text-lg" x-show="!deleting"></i>
                                    <i class="ph ph-spinner ph-spin text-lg text-red-500" x-show="deleting"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center h-full text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="ph ph-shopping-cart text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Keranjang Kosong</h3>
                    <p class="text-sm text-gray-500 mb-6">Yuk, mulai belanja produk pertanian berkualitas!</p>
                    <a href="/user/produk" 
                       @click="open = false" 
                       class="btn-primary">
                        Mulai Belanja
                    </a>
                </div>
            @endforelse
        </div>
        
        {{-- Footer --}}
        @if(count($cartItems) > 0)
            <div class="border-t border-gray-100 p-4 space-y-4">
                {{-- Total --}}
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Total</span>
                    <span class="text-xl font-bold text-gray-900">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                </div>
                
                {{-- Actions --}}
                <div class="space-y-2">
                    <a href="/user/checkout" 
                       @click="open = false" 
                       class="btn-primary w-full block text-center">
                        Checkout
                    </a>
                    <a href="/user/keranjang" 
                       @click="open = false" 
                       class="btn-secondary w-full block text-center">
                        Lihat Keranjang
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
