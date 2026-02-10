@extends('layout')

@section('content')
<style>
    .sales-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 25px;
    }

    .page-header h2 {
        color: #800000;
        margin: 0 0 5px 0;
        font-size: 28px;
    }

    .page-header p {
        color: #666;
        margin: 0;
        font-size: 14px;
    }

    .toolbar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-add {
        background: #FFD700;
        color: #800000;
    }

    .btn-add:hover {
        background: #e6c200;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 215, 0, 0.3);
    }

    .section-card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table thead tr {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .modern-table th {
        padding: 12px;
        text-align: left;
        color: #800000;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
    }

    .modern-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
    }

    .modern-table tbody tr:hover {
        background: #f8f9fa;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    .product-name {
        font-weight: 600;
        color: #333;
    }

    .amount {
        color: #4CAF50;
        font-weight: 600;
    }

    .date-time {
        color: #666;
        font-size: 13px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .modern-table {
            font-size: 13px;
        }

        .modern-table th,
        .modern-table td {
            padding: 8px;
        }
    }
</style>

<div class="sales-container">
    <div class="page-header">
        <h2>📊 Sales History</h2>
        <p>Track your daily sales and transactions</p>
    </div>

    <div class="toolbar">
        <button onclick="openAddSaleSidebar()" class="btn btn-add">
            <i class="fas fa-plus"></i> New Sale
        </button>
    </div>

    <div class="section-card">
        @if($sales->isEmpty())
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <h3 style="color:#666; margin:0 0 10px 0;">No Sales Yet</h3>
            <p>Start by recording your first sale or use Quick Sale Mode</p>
        </div>
        @else
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td class="product-name">{{ $sale->product->name }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td class="amount">₱{{ number_format($sale->total, 2) }}</td>
                    <td class="date-time">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($sales->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $sales->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

<!-- Overlay -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:900;" onclick="closeSidebars()"></div>

<!-- Add Sale Sidebar -->
<div id="addSaleSidebar" style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff; border-left:4px solid #800000; box-shadow:-2px 0 8px rgba(0,0,0,0.3); padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">➕ Record New Sale</h3>
    <form method="POST" action="{{ route('sales.store') }}" id="addSaleForm">
        @csrf
        <div style="margin-bottom:12px;">
            <label><b>Product:</b></label>
            <select name="product_id" id="productSelect" required
                style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
                <option value="" disabled selected>-- Select Product --</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                    {{ $product->name }} (₱{{ number_format($product->price, 2) }}) - {{ $product->stock }} in stock
                </option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Quantity:</b></label>
            <input type="number" id="quantityInput" name="quantity" min="1" required
                style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
            <small id="stockWarning" style="color:#e65100; font-size:12px; display:none;"></small>
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Total:</b></label>
            <input type="text" id="totalInput" readonly style="width:100%; padding:10px; background:#f8f8f8; border:1px solid #ccc; border-radius:5px; font-weight:bold; color:#4CAF50;">
        </div>
        <button type="submit"
            style="padding:10px 20px; background:#800000; color:#fff; border:none;
                   border-radius:5px; cursor:pointer; font-weight:600;">
            <i class="fas fa-save"></i> Save Sale
        </button>
        <button type="button" onclick="closeSidebars()"
            style="padding:10px 20px; background:#ccc; color:#000; border:none;
                   border-radius:5px; cursor:pointer; margin-left:10px;">
            Cancel
        </button>
    </form>
</div>

<script>
    function openAddSaleSidebar() {
        document.getElementById("addSaleSidebar").style.right = "0";
        document.getElementById("overlay").style.display = "block";
    }

    function closeSidebars() {
        document.getElementById("addSaleSidebar").style.right = "-100%";
        document.getElementById("overlay").style.display = "none";
    }

    const productSelect = document.getElementById("productSelect");
    const quantityInput = document.getElementById("quantityInput");
    const totalInput = document.getElementById("totalInput");
    const stockWarning = document.getElementById("stockWarning");

    function calculateTotal() {
        const selected = productSelect.options[productSelect.selectedIndex];
        const price = selected ? parseFloat(selected.dataset.price) : 0;
        const stock = selected ? parseInt(selected.dataset.stock) : 0;
        const quantity = parseInt(quantityInput.value) || 0;

        // Show stock warning
        if (quantity > stock) {
            stockWarning.textContent = `⚠️ Only ${stock} items available`;
            stockWarning.style.display = 'block';
            quantityInput.style.borderColor = '#e65100';
        } else {
            stockWarning.style.display = 'none';
            quantityInput.style.borderColor = '#ccc';
        }

        // Calculate total
        if (price && quantity) {
            totalInput.value = "₱" + (price * quantity).toFixed(2);
        } else {
            totalInput.value = "";
        }
    }

    if (productSelect) {
        productSelect.addEventListener("change", function() {
            quantityInput.value = "1";
            calculateTotal();
        });
        quantityInput.addEventListener("input", calculateTotal);
    }
</script>
<script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('addSaleForm');

        if (!form) {
            console.error('Form not found!');
            return;
        }

        console.log('✅ Form found, adding submit handler');

        // Intercept form submission
        form.addEventListener('submit', function(e) {
            console.log('📝 Form submitted');
            e.preventDefault();
            e.stopPropagation();

            const formData = new FormData(this);
            const productId = formData.get('product_id');
            const quantity = parseInt(formData.get('quantity'));

            if (!productId || !quantity) {
                alert('❌ Please fill all fields');
                return false;
            }

            // Get product details
            const productSelect = document.getElementById('productSelect');
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const productName = selectedOption.text.split(' (')[0];
            const price = parseFloat(selectedOption.dataset.price);
            const total = price * quantity;

            const isOffline = !navigator.onLine;
            console.log('Navigator says online:', navigator.onLine);
            console.log('Is offline:', isOffline);

            // Check if offline
            if (isOffline) {
                console.log('📴 Offline mode - saving sale locally');

                const pendingSales = JSON.parse(localStorage.getItem('pendingSales') || '[]');

                const saleData = {
                    cart: [{
                        id: productId,
                        name: productName,
                        price: price,
                        quantity: quantity,
                        maxStock: 999
                    }],
                    timestamp: new Date().toISOString(),
                    total: total
                };

                pendingSales.push(saleData);
                localStorage.setItem('pendingSales', JSON.stringify(pendingSales));

                // Show success message
                alert('✅ Sale saved offline!\n\n' + productName + '\nQty: ' + quantity + '\nTotal: ₱' + total.toFixed(2) + '\n\nWill sync when online.');

                // Close sidebar and reset form
                closeSidebars();
                form.reset();
                document.getElementById('totalInput').value = '';

                return false;
            }

            // ONLINE MODE - Submit via AJAX
            console.log('🌐 Online mode - submitting via AJAX');

            fetch('{{ route("sales.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => {
                    console.log('Response received:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data:', data);
                    if (data.success) {
                        closeSidebars();
                        form.reset();
                        document.getElementById('totalInput').value = '';

                        // Add new sale to table without page reload
                        const tableBody = document.querySelector('.modern-table tbody');
                        const emptyState = document.querySelector('.empty-state');

                        if (emptyState) {
                            // Replace empty state with table
                            emptyState.parentElement.innerHTML = `
                                <table class="modern-table">
                                    <thead><tr>
                                        <th>Product</th><th>Quantity</th><th>Total</th><th>Date</th>
                                    </tr></thead>
                                    <tbody></tbody>
                                </table>`;
                        }

                        const tbody = document.querySelector('.modern-table tbody');
                        const now = new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td class="product-name">${productName}</td>
                            <td>${quantity}</td>
                            <td class="amount">₱${total.toFixed(2)}</td>
                            <td class="date-time">${now}</td>
                        `;
                        newRow.style.animation = 'fadeIn 0.3s';
                        tbody.insertBefore(newRow, tbody.firstChild);

                        // Show receipt inline
                        showReceiptModal(data.sale_id);
                    } else {
                        alert('❌ Error: ' + (data.message || 'Failed to save sale'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);

                    // FALLBACK: If fetch fails, save offline
                    console.log('⚠️ Fetch failed - saving offline as fallback');

                    const pendingSales = JSON.parse(localStorage.getItem('pendingSales') || '[]');

                    const saleData = {
                        cart: [{
                            id: productId,
                            name: productName,
                            price: price,
                            quantity: quantity,
                            maxStock: 999
                        }],
                        timestamp: new Date().toISOString(),
                        total: total
                    };

                    pendingSales.push(saleData);
                    localStorage.setItem('pendingSales', JSON.stringify(pendingSales));

                    alert('✅ Sale saved offline!\n\nConnection failed. Will sync when online.');

                    closeSidebars();
                    form.reset();
                    document.getElementById('totalInput').value = '';
                });

            return false;
        }, false);
    });
</script>
<script>
    function showReceiptModal(saleId) {
        let modal = document.getElementById('receiptModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'receiptModal';
            modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:2000;display:flex;align-items:center;justify-content:center;';
            const inner = document.createElement('div');
            inner.style.cssText = 'width:95%;max-width:600px;max-height:90%;overflow:auto;background:#fff;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.3)';
            inner.id = 'receiptContent';
            const header = document.createElement('div');
            header.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:10px 15px;border-bottom:1px solid #eee;';
            const title = document.createElement('div');
            title.textContent = 'Receipt';
            title.style.cssText = 'font-weight:600;color:#800000';
            const back = document.createElement('button');
            back.textContent = '← Back';
            back.style.cssText = 'padding:8px 14px;background:#FFD700;color:#800000;border:none;border-radius:6px;font-weight:600;cursor:pointer';
            back.onclick = () => { modal.remove(); };
            header.appendChild(title);
            header.appendChild(back);
            inner.appendChild(header);
            const body = document.createElement('div');
            body.style.cssText = 'padding:10px 15px';
            inner.appendChild(body);
            modal.appendChild(inner);
            document.body.appendChild(modal);
        }
        fetch('/sales/' + saleId + '/receipt', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                document.getElementById('receiptContent').children[2].innerHTML = html;
            })
            .catch(err => {
                alert('Failed to load receipt');
                console.error(err);
            });
    }
</script>
@endsection
