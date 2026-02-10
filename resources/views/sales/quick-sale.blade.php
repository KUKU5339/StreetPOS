@extends('layout')

@section('content')
<style>
    .quick-sale-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .product-card {
        background: #fff;
        border: 2px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .product-card:hover {
        border-color: #FFD700;
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .product-card.out-of-stock {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .product-card.out-of-stock:hover {
        transform: none;
        border-color: #ddd;
    }

    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #f0f0f0;
    }

    .product-name {
        font-weight: bold;
        color: #800000;
        margin: 8px 0;
        font-size: 16px;
    }

    .product-price {
        color: #008000;
        font-size: 18px;
        font-weight: bold;
    }

    .product-stock {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .cart-container {
        position: fixed;
        bottom: 0;
        left: 220px;
        right: 0;
        background: #fff;
        border-top: 3px solid #800000;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        z-index: 100;
        transition: left 0.3s ease-in-out;
    }

    .cart-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 15px 20px;
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .cart-items {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .cart-item {
        background: #f8f8f8;
        padding: 10px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 250px;
        border: 2px solid #ddd;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-name {
        font-weight: bold;
        color: #800000;
        font-size: 14px;
    }

    .cart-item-price {
        color: #008000;
        font-size: 13px;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .qty-btn {
        background: #800000;
        color: #fff;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover {
        background: #a00000;
    }

    .qty-display {
        min-width: 30px;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }

    .remove-btn {
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 12px;
    }

    .remove-btn:hover {
        background: #c82333;
    }

    .cart-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 2px solid #eee;
    }

    .cart-total {
        font-size: 24px;
        font-weight: bold;
        color: #800000;
    }

    .cart-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-clear {
        background: #6c757d;
        color: #fff;
    }

    .btn-clear:hover {
        background: #5a6268;
    }

    .btn-checkout {
        background: #FFD700;
        color: #800000;
    }

    .btn-checkout:hover {
        background: #e6c200;
        transform: scale(1.05);
    }

    .empty-cart {
        text-align: center;
        color: #999;
        padding: 20px;
    }

    @media (max-width: 768px) {
        .cart-container {
            left: 0;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }

        .product-image {
            height: 100px;
        }

        .cart-items {
            flex-direction: column;
        }

        .cart-item {
            min-width: 100%;
        }
    }
</style>

<div class="quick-sale-container">
    <div class="page-header">
        <div>
            <h2 style="color:#800000; margin:0;">⚡ Quick Sale Mode</h2>
            <p style="color:#666; margin:5px 0 0 0;">Tap products to add to cart</p>
        </div>
    </div>

    @if($products->isEmpty())
    <div style="text-align:center; padding:40px; background:#fff; border-radius:10px;">
        <i class="fas fa-box-open" style="font-size:48px; color:#ccc;"></i>
        <p style="color:#999; margin-top:15px;">No products available. <a href="{{ route('products.index') }}">Add some products first</a></p>
    </div>
    @else
    <div class="products-grid">
        @foreach($products as $product)
        <div class="product-card {{ $product->stock == 0 ? 'out-of-stock' : '' }}"
            data-product-id="{{ $product->id }}"
            onclick="{{ $product->stock > 0 ? 'addToCart('.$product->id.', \''.$product->name.'\', '.$product->price.', '.$product->stock.')' : '' }}">
            @if($product->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
            @else
            <div class="product-image" style="display:flex; align-items:center; justify-content:center; background:#f0f0f0;">
                <i class="fas fa-utensils" style="font-size:40px; color:#ccc;"></i>
            </div>
            @endif
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
            <div class="product-stock">
                @if($product->stock == 0)
                <span style="color:red;">Out of Stock</span>
                @else
                Stock: {{ $product->stock }}
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Shopping Cart -->
<div class="cart-container">
    <div class="cart-content">
        <div class="cart-header">
            <h3 style="margin:0; color:#800000;">🛒 Current Order</h3>
            <span id="cart-count" style="color:#666;">0 items</span>
        </div>

        <div id="cart-items" class="cart-items">
            <div class="empty-cart">Cart is empty. Tap products above to add items.</div>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                Total: <span id="cart-total">₱0.00</span>
            </div>
            <div class="cart-actions">
                <button class="btn btn-clear" onclick="clearCart()">Clear Cart</button>
                <button class="btn btn-checkout" onclick="checkout()" id="checkout-btn" disabled>Complete Sale</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    // Cache products to IndexedDB on page load for offline use
    window.addEventListener('load', async () => {
        try {
            // Wait a moment for offlineDB to initialize
            if (typeof offlineDB === 'undefined') {
                console.warn('⚠️ Offline database not available - offline mode disabled');
                return;
            }

            // Ensure DB is initialized
            if (!offlineDB.db) {
                await offlineDB.init();
            }

            const products = @json($products);
            if (products && products.length > 0) {
                await offlineDB.saveProducts(products);
                console.log('✅ Products cached for offline use:', products.length, 'products');
            }

            // Show offline ready indicator
            if (navigator.onLine) {
                console.log('✅ Offline mode ready - this page will work without internet');
            }
        } catch (err) {
            console.error('Failed to cache products:', err);
        }
    });

    // Update UI when going offline/online
    window.addEventListener('offline', () => {
        console.log('📴 Gone offline - using cached data');
        showToast('📴 You are offline. Sales will be saved locally.', 'warning');
    });

    window.addEventListener('online', () => {
        console.log('🌐 Back online');
        showToast('🌐 Back online!', 'success');
    });

    function addToCart(id, name, price, maxStock) {
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            if (existingItem.quantity < maxStock) {
                existingItem.quantity++;
            } else {
                alert('Maximum stock reached for ' + name);
                return;
            }
        } else {
            cart.push({
                id,
                name,
                price,
                quantity: 1,
                maxStock
            });
        }

        updateCart();
    }

    function updateQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (!item) return;

        item.quantity += change;

        if (item.quantity <= 0) {
            removeFromCart(id);
        } else if (item.quantity > item.maxStock) {
            item.quantity = item.maxStock;
            alert('Maximum stock reached');
        }

        updateCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        updateCart();
    }

    function updateCart() {
        const cartItemsDiv = document.getElementById('cart-items');
        const cartCount = document.getElementById('cart-count');
        const cartTotal = document.getElementById('cart-total');
        const checkoutBtn = document.getElementById('checkout-btn');

        if (cart.length === 0) {
            cartItemsDiv.innerHTML = '<div class="empty-cart">Cart is empty. Tap products above to add items.</div>';
            cartCount.textContent = '0 items';
            cartTotal.textContent = '₱0.00';
            checkoutBtn.disabled = true;
            return;
        }

        let html = '';
        let total = 0;
        let itemCount = 0;

        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            itemCount += item.quantity;

            html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">₱${item.price.toFixed(2)} × ${item.quantity} = ₱${subtotal.toFixed(2)}</div>
                </div>
                <div class="quantity-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span class="qty-display">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                </div>
                <button class="remove-btn" onclick="removeFromCart(${item.id})">✕</button>
            </div>
        `;
        });

        cartItemsDiv.innerHTML = html;
        cartCount.textContent = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;
        cartTotal.textContent = `₱${total.toFixed(2)}`;
        checkoutBtn.disabled = false;
    }

    function clearCart() {
        if (cart.length === 0) return;

        if (confirm('Clear all items from cart?')) {
            cart = [];
            updateCart();
        }
    }

    // Use the global getFreshCsrfToken from layout.blade.php
    // Falls back to meta tag token if global function not available
    async function getToken() {
        if (typeof window.getFreshCsrfToken === 'function') {
            const token = await window.getFreshCsrfToken();
            if (token) return token;
        }
        return document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    }

    // Helper to ensure offlineDB is ready
    async function ensureOfflineDB() {
        if (typeof offlineDB === 'undefined') {
            return false;
        }
        if (!offlineDB.db) {
            try {
                await offlineDB.init();
            } catch (err) {
                console.error('Failed to init offlineDB:', err);
                return false;
            }
        }
        return true;
    }

    function updateProductStockUI(productId, newStock) {
        const card = document.querySelector(`.product-card[data-product-id="${productId}"]`);
        if (!card) return;

        // Update stock text
        const stockDiv = card.querySelector('.product-stock');
        if (stockDiv) {
            if (newStock <= 0) {
                stockDiv.innerHTML = '<span style="color:red;">Out of Stock</span>';
                card.classList.add('out-of-stock');
                card.setAttribute('onclick', '');
            } else {
                stockDiv.textContent = 'Stock: ' + newStock;
                // Update onclick with new maxStock
                const name = card.querySelector('.product-name').textContent;
                const price = parseFloat(card.querySelector('.product-price').textContent.replace('₱', '').replace(',', ''));
                card.setAttribute('onclick', `addToCart(${productId}, '${name.replace(/'/g, "\\'")}', ${price}, ${newStock})`);
            }
        }
    }

    async function checkout() {
        if (cart.length === 0) return;

        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        // IMPROVED offline detection
        const isOffline = !navigator.onLine;

        console.log('Navigator says online:', navigator.onLine);
        console.log('Is offline:', isOffline);

        // Check if online or offline
        if (isOffline) {
            // OFFLINE MODE - Save to IndexedDB
            console.log('📴 Offline mode - saving sale to IndexedDB');

            // Ensure offlineDB is initialized
            const dbReady = await ensureOfflineDB();
            if (!dbReady) {
                alert('❌ Offline mode not available. Please connect to the internet and refresh the page to enable offline support.');
                return;
            }

            try {
                const saleData = {
                    cart: cart,
                    timestamp: new Date().toISOString(),
                    total: total
                };

                await offlineDB.addPendingSale(saleData);

                // Update local product stock in IndexedDB and DOM
                for (const item of cart) {
                    const products = await offlineDB.getProducts();
                    const product = products.find(p => p.id === item.id);
                    if (product) {
                        const newStock = product.stock - item.quantity;
                        await offlineDB.updateProductStock(item.id, newStock);
                        updateProductStockUI(item.id, newStock);
                    }
                }

                // Show success message
                alert('✅ Sale saved offline!\n\nTotal: ₱' + total.toFixed(2) + '\n\nThis sale will automatically sync when you reconnect to the internet.');

                // Clear cart
                cart = [];
                updateCart();

                // Show toast
                showToast('📦 Sale saved offline. Will sync when online.', 'success');
            } catch (err) {
                console.error('Failed to save offline sale:', err);
                alert('❌ Failed to save sale offline. Please try again.');
            }

            return;
        }

        // ONLINE MODE - Submit via AJAX
        console.log('🌐 Online mode - submitting via AJAX');

        // Get fresh CSRF token to avoid "Page Expired" errors
        const csrfToken = await getToken();

        fetch('{{ route("sales.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    cart: cart
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status + ': ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update stock display for each sold item
                    for (const item of cart) {
                        const card = document.querySelector(`.product-card[data-product-id="${item.id}"]`);
                        if (card) {
                            const newStock = item.maxStock - item.quantity;
                            updateProductStockUI(item.id, newStock);
                        }
                    }

                    // Clear cart
                    cart = [];
                    updateCart();

                    // Show success with receipt option
                    if (confirm('✅ Sale completed!\n\nTotal: ₱' + total.toFixed(2) + '\n\nWould you like to view the receipt?')) {
                        // Open receipt in new tab
                        window.open('/sales/' + data.sale_id + '/receipt', 'streetpos-receipt');
                    }

                    showToast('✅ Sale completed successfully!', 'success');
                } else {
                    alert('❌ Error: ' + (data.message || 'Failed to complete sale'));
                    showToast('❌ Sale failed. Please try again.', 'error');
                }
            })
            .catch(async error => {
                console.error('Sale error:', error);

                // FALLBACK: If fetch fails, try to save to IndexedDB
                console.log('⚠️ Fetch failed - attempting offline save as fallback');

                // Ensure offlineDB is available and initialized
                const dbReady = await ensureOfflineDB();
                if (!dbReady) {
                    alert('❌ Connection failed and offline mode is not available.\n\nPlease check your internet connection and try again.');
                    return;
                }

                try {
                    const saleData = {
                        cart: cart,
                        timestamp: new Date().toISOString(),
                        total: total
                    };

                    await offlineDB.addPendingSale(saleData);

                    // Update local product stock in IndexedDB and DOM
                    for (const item of cart) {
                        const products = await offlineDB.getProducts();
                        const product = products.find(p => p.id === item.id);
                        if (product) {
                            const newStock = product.stock - item.quantity;
                            await offlineDB.updateProductStock(item.id, newStock);
                            updateProductStockUI(item.id, newStock);
                        }
                    }

                    alert('✅ Sale saved offline!\n\nTotal: ₱' + total.toFixed(2) + '\n\nConnection failed. This sale will sync when you reconnect.');

                    cart = [];
                    updateCart();
                    showToast('📦 Sale saved offline due to connection error', 'success');
                } catch (err) {
                    console.error('Failed to save offline sale:', err);
                    alert('❌ Failed to save sale. Please check your connection and try again.');
                }
            });
    }

</script>

@endsection