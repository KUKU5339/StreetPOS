@extends('layout')

@section('content')
<style>
    .products-container {
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

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .toolbar {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
        display: flex;
        gap: 10px;
    }

    .search-input {
        flex: 1;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #800000;
        color: #fff;
    }

    .btn-primary:hover {
        background: #a00000;
    }

    .btn-add {
        background: #FFD700;
        color: #800000;
    }

    .btn-add:hover {
        background: #e6c200;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .product-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .product-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: #f5f5f5;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
        color: #999;
        font-size: 48px;
    }

    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.95);
    }

    .stock-low {
        color: #c62828;
        border: 2px solid #c62828;
    }

    .stock-medium {
        color: #e65100;
        border: 2px solid #e65100;
    }

    .stock-good {
        color: #2e7d32;
        border: 2px solid #2e7d32;
    }

    .product-info {
        padding: 20px;
    }

    .product-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0 0 10px 0;
    }

    .product-price {
        font-size: 22px;
        font-weight: bold;
        color: #4CAF50;
        margin-bottom: 15px;
    }

    .product-actions {
        display: flex;
        gap: 8px;
    }

    .btn-edit {
        flex: 1;
        background: #FFD700;
        color: #800000;
        padding: 10px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #e6c200;
    }

    .btn-delete {
        background: #dc3545;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    .empty-state {
        background: #fff;
        padding: 60px 20px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-state i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        }
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
</style>

<div class="products-container">
    <div class="page-header">
        <h2>📦 Products Management</h2>
        <p>Control your menu, stock, and pricing</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error">
        <strong>Validation Errors:</strong>
        <ul style="margin: 5px 0 0 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        <strong>{{ session('success') }}</strong>
    </div>
    @endif

    <div class="toolbar">
        <form method="GET" action="{{ route('products.index') }}" class="search-box">
            <input type="text" name="search" placeholder="🔍 Search products..."
                value="{{ request('search') }}" class="search-input">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <button onclick="openAddSidebar()" class="btn btn-add">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>

    @if($products->isEmpty())
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>No Products Found</h3>
        <p>
            @if(request('search'))
            No results for "{{ request('search') }}"
            @else
            Start by adding your first product
            @endif
        </p>
    </div>
    @else
    <div class="products-grid">
        @foreach($products as $product)
        <div class="product-card">
            <div class="product-image-container">
                @if($product->image)
                <img src="{{ $product->image_url }}"
                    alt="{{ $product->name }}" class="product-image" loading="lazy">
                @else
                <div class="product-image-placeholder">
                    <i class="fas fa-utensils"></i>
                </div>
                @endif

                <span class="stock-badge {{ $product->stock <= 5 ? 'stock-low' : ($product->stock <= 15 ? 'stock-medium' : 'stock-good') }}">
                    {{ $product->stock }} in stock
                </span>
            </div>

            <div class="product-info">
                <h3 class="product-name">{{ $product->name }}</h3>
                <div class="product-price">₱{{ number_format($product->price, 2) }}</div>

                <div class="product-actions">
                    <button onclick="openEditSidebar({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }}, '{{ $product->image_url }}')"
                        class="btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete"
                            onclick="return confirm('Delete {{ $product->name }}?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($products->hasPages())
    <div style="margin-top: 25px; display: flex; justify-content: center;">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @endif
    @endif
</div>

<!-- Overlay -->
<div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:900;" onclick="closeSidebars()"></div>

<!-- Add Sidebar -->
<div id="addSidebar" style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff; border-left:4px solid #800000; box-shadow:-2px 0 8px rgba(0,0,0,0.3); padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">➕ Add Product</h3>
    <form id="addProductForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:12px;">
            <label><b>Street Food Item:</b></label>
            <select name="name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px; background:#fff;">
                <option value="">-- Select Street Food --</option>
                <optgroup label="Grilled/BBQ">
                    <option value="Isaw (Chicken Intestine)">Isaw (Chicken Intestine)</option>
                    <option value="Betamax (Blood Cake)">Betamax (Blood Cake)</option>
                    <option value="Chicken Barbecue">Chicken Barbecue</option>
                    <option value="Pork Barbecue">Pork Barbecue</option>
                    <option value="Adidas (Chicken Feet)">Adidas (Chicken Feet)</option>
                    <option value="Helmet (Chicken Head)">Helmet (Chicken Head)</option>
                    <option value="IUD (Chicken Butt)">IUD (Chicken Butt)</option>
                </optgroup>
                <optgroup label="Fried">
                    <option value="Kwek-Kwek (Orange Quail Eggs)">Kwek-Kwek (Orange Quail Eggs)</option>
                    <option value="Tokneneng (Orange Chicken Eggs)">Tokneneng (Orange Chicken Eggs)</option>
                    <option value="Fish Ball">Fish Ball</option>
                    <option value="Squid Ball">Squid Ball</option>
                    <option value="Kikiam">Kikiam</option>
                    <option value="Chicken Skin">Chicken Skin</option>
                    <option value="Banana Cue">Banana Cue</option>
                    <option value="Camote Cue">Camote Cue</option>
                    <option value="Turon">Turon</option>
                </optgroup>
                <optgroup label="Noodles & Rice">
                    <option value="Pancit Canton">Pancit Canton</option>
                    <option value="Palabok">Palabok</option>
                    <option value="Siomai">Siomai</option>
                    <option value="Lugaw">Lugaw</option>
                    <option value="Goto">Goto</option>
                </optgroup>
                <optgroup label="Snacks">
                    <option value="Taho">Taho</option>
                    <option value="Dirty Ice Cream">Dirty Ice Cream</option>
                    <option value="Mais (Corn)">Mais (Corn)</option>
                    <option value="Balut">Balut</option>
                    <option value="Penoy">Penoy</option>
                </optgroup>
                <optgroup label="Drinks">
                    <option value="Sago't Gulaman">Sago't Gulaman</option>
                    <option value="Buko Juice">Buko Juice</option>
                    <option value="Melon Juice">Melon Juice</option>
                </optgroup>
                <optgroup label="Other">
                    <option value="Custom Item">Custom Item (Type your own)</option>
                </optgroup>
            </select>
        </div>
        <div id="customNameField" style="margin-bottom:12px; display:none;">
            <label><b>Custom Item Name:</b></label>
            <input type="text" id="customNameInput" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Price:</b></label>
            <input type="number" step="0.01" name="price" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Stock:</b></label>
            <input type="number" name="stock" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Image:</b></label>
            <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#800000; color:#fff; border:none; border-radius:5px; cursor:pointer;">Save</button>
        <button type="button" onclick="closeSidebars()" style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Cancel</button>
    </form>
</div>

<!-- Edit Sidebar -->
<div id="editSidebar" style="position:fixed; top:0; right:-100%; width:90%; max-width:400px; height:100%; background:#fff; border-left:4px solid #FFD700; box-shadow:-2px 0 8px rgba(0,0,0,0.3); padding:20px; transition:right 0.3s ease; overflow-y:auto; z-index:1000;">
    <h3 style="color:#800000; margin-bottom:15px;">✏ Edit Product</h3>
    <form id="editForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div style="margin-bottom:12px;">
            <label><b>Name:</b></label>
            <input type="text" id="editName" name="name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Price:</b></label>
            <input type="number" step="0.01" id="editPrice" name="price" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Stock:</b></label>
            <input type="number" id="editStock" name="stock" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Current Image:</b></label><br>
            <img id="editImagePreview" src="" alt="No image" style="width:100px; height:100px; object-fit:cover; margin-bottom:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <div style="margin-bottom:12px;">
            <label><b>Change Image:</b></label>
            <input type="file" name="image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:5px;">
        </div>
        <button type="submit" style="padding:10px 20px; background:#FFD700; color:#800000; border:none; border-radius:5px; cursor:pointer;">Update</button>
        <button type="button" onclick="closeSidebars()" style="padding:10px 20px; background:#ccc; color:#000; border:none; border-radius:5px; cursor:pointer; margin-left:10px;">Cancel</button>
    </form>
</div>

<script>
    function openAddSidebar() {
        document.getElementById("addSidebar").style.right = "0";
        document.getElementById("overlay").style.display = "block";
    }

    function openEditSidebar(id, name, price, stock, image) {
        document.getElementById("editSidebar").style.right = "0";
        document.getElementById("overlay").style.display = "block";

        document.getElementById("editName").value = name;
        document.getElementById("editPrice").value = price;
        document.getElementById("editStock").value = stock;
        document.getElementById("editForm").action = "/products/" + id;

        const preview = document.getElementById("editImagePreview");
        if (image && image !== 'null') {
            preview.src = "/storage/" + image;
        } else {
            preview.src = "";
            preview.alt = "No image";
        }
    }

    function closeSidebars() {
        document.getElementById("addSidebar").style.right = "-100%";
        document.getElementById("editSidebar").style.right = "-100%";
        document.getElementById("overlay").style.display = "none";
    }

    // Show banner for pending products
    function showPendingProductsBanner(count) {
        // Remove existing banner if any
        const existingBanner = document.getElementById('pendingProductsBanner');
        if (existingBanner) {
            existingBanner.remove();
        }

        // Create new banner
        const banner = document.createElement('div');
        banner.id = 'pendingProductsBanner';
        banner.style.cssText = `
            position: fixed;
            top: 70px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9998;
            font-weight: 600;
            animation: slideDown 0.3s;
            cursor: pointer;
            text-align: center;
            max-width: 90%;
        `;
        banner.innerHTML = `
            <i class="fas fa-clock"></i>
            ${count} product${count > 1 ? 's' : ''} pending sync
            <br>
            <small style="font-size: 12px; font-weight: normal;">Will sync when you're back online</small>
        `;

        banner.onclick = function() {
            alert('You have ' + count + ' product(s) waiting to sync.\n\nThey will automatically sync when you connect to the internet.');
        };

        document.body.appendChild(banner);
    }

    // Check for pending products on page load and show banner
    function checkPendingProducts() {
        try {
            const pendingProducts = JSON.parse(localStorage.getItem('pendingProducts') || '[]');
            if (pendingProducts.length > 0 && !navigator.onLine) {
                showPendingProductsBanner(pendingProducts.length);
            }
        } catch (err) {
            console.error('Failed to check pending products:', err);
        }
    }

    // Call on page load
    checkPendingProducts();

    // Handle custom item selection
    const nameSelect = document.querySelector('select[name="name"]');
    const customNameField = document.getElementById('customNameField');
    const customNameInput = document.getElementById('customNameInput');

    if (nameSelect) {
        nameSelect.addEventListener('change', function() {
            if (this.value === 'Custom Item') {
                customNameField.style.display = 'block';
                customNameInput.required = true;
            } else {
                customNameField.style.display = 'none';
                customNameInput.required = false;
            }
        });
    }

    // Function to initialize form handlers
    function initializeFormHandlers() {
        console.log('🔧 Initializing form handlers...');
        console.log('Current online status on page load:', navigator.onLine);

        // Handle offline product creation
        const addForm = document.getElementById('addProductForm');
        if (addForm) {
            console.log('✅ Found add product form');
            console.log('Form action:', addForm.action);
            console.log('Form method:', addForm.method);

            addForm.addEventListener('submit', async function(e) {
                // ALWAYS prevent default FIRST - critical!
                e.preventDefault();
                e.stopPropagation();

                console.log('🔔 SUBMIT EVENT FIRED!');
                console.log('📝 Form submitted, online status:', navigator.onLine);
                console.log('Event default prevented?', e.defaultPrevented);

                const formData = new FormData(this);
                let name = formData.get('name');
                const price = parseFloat(formData.get('price'));
                const stock = parseInt(formData.get('stock'));

                // If "Custom Item" is selected, use the custom name instead
                if (name === 'Custom Item') {
                    const customName = document.getElementById('customNameInput').value.trim();
                    if (!customName) {
                        alert('❌ Please enter a custom item name');
                        return false;
                    }
                    name = customName;
                    // Update the select value to the custom name for submission
                    this.querySelector('select[name="name"]').value = name;
                    // Or better, add it as a hidden field
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'name';
                    hiddenInput.value = name;
                    this.appendChild(hiddenInput);
                    this.querySelector('select[name="name"]').removeAttribute('name');
                }

                console.log('Form data:', { name, price, stock });

                // Validate input
                if (!name || !price || !stock || isNaN(price) || isNaN(stock)) {
                    console.log('❌ Validation failed - preventing submission');
                    alert('❌ Please fill all required fields correctly');
                    return false;
                }

                // Check offline status
                if (!navigator.onLine) {
                    console.log('📴 Offline detected - saving to localStorage');

                    try {
                        // Save to localStorage when offline
                        const pendingProducts = JSON.parse(localStorage.getItem('pendingProducts') || '[]');

                        const productData = {
                            name: name,
                            price: price,
                            stock: stock,
                            timestamp: new Date().toISOString()
                        };

                        pendingProducts.push(productData);
                        localStorage.setItem('pendingProducts', JSON.stringify(pendingProducts));

                        console.log('✅ Product saved offline:', productData);
                        console.log('📦 Total pending products:', pendingProducts.length);

                        // Show success message
                        alert('✅ Product saved offline!\n\nProduct: ' + name + '\nPrice: ₱' + price.toFixed(2) + '\nStock: ' + stock + '\n\n' + pendingProducts.length + ' product(s) waiting to sync.\n\nThey will automatically sync when you\'re back online.');

                        closeSidebars();
                        this.reset();

                        // Show a banner about pending products
                        showPendingProductsBanner(pendingProducts.length);
                    } catch (err) {
                        console.error('Failed to save to localStorage:', err);
                        alert('❌ Failed to save product offline.\n\nError: ' + err.message + '\n\nPlease check if your browser allows local storage.');
                    }

                    return false;
                }

                // When online, submit via AJAX and update UI instantly
                console.log('✅ Online - submitting via fetch');
                try {
                    if (name === 'Custom Item') {
                        const customName = (document.getElementById('customNameInput')?.value || '').trim();
                        if (customName) {
                            formData.set('name', customName);
                        }
                    }

                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        body: formData
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Failed to add product');
                    }

                    const p = data.product;
                    const grid = document.querySelector('.products-grid');
                    if (grid) {
                        const card = document.createElement('div');
                        card.className = 'product-card';
                        card.innerHTML = `
                            <div class="product-image-container">
                                ${p.image_url ? `<img src="${p.image_url}" alt="${p.name}" class="product-image" loading="lazy">` : `
                                    <div class="product-image-placeholder"><i class="fas fa-utensils"></i></div>`}
                                <span class="stock-badge ${p.stock <= 5 ? 'stock-low' : (p.stock <= 15 ? 'stock-medium' : 'stock-good')}">
                                    ${p.stock} in stock
                                </span>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name">${p.name}</h3>
                                <div class="product-price">₱${Number(p.price).toFixed(2)}</div>
                                <div class="product-actions">
                                    <button onclick="openEditSidebar(${p.id}, '${p.name.replace(/'/g, \"\\\\'\")}', ${p.price}, ${p.stock}, '${p.image_url || ''}')" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="/products/${p.id}" method="POST" style="display:inline;">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn-delete" onclick="return confirm('Delete ${p.name}?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        `;
                        grid.insertBefore(card, grid.firstChild);
                    }

                    closeSidebars();
                    this.reset();
                    alert('✅ Product added!');
                } catch (err) {
                    console.error('Add product failed:', err);
                    alert('❌ ' + err.message);
                }
            });
        } else {
            console.error('❌ Add product form not found!');
        }

        // Handle offline product editing
        const editForm = document.querySelector('#editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const isOffline = !navigator.onLine;

                if (isOffline) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('❌ Cannot edit products while offline\n\nProduct updates require an internet connection.\n\nPlease connect to WiFi and try again.');
                    return false;
                }

                return true;
            });
        }
    }

    // Initialize handlers when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeFormHandlers);
    } else {
        // DOM is already loaded
        initializeFormHandlers();
    }

    // Handle image errors when offline
    document.addEventListener('DOMContentLoaded', function() {
        const productImages = document.querySelectorAll('.product-image');

        productImages.forEach(img => {
            img.addEventListener('error', function() {
                // Replace failed image with offline message
                const container = this.parentElement;
                container.innerHTML = `
                    <div style="width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f5f5f5; color:#999; padding:20px; text-align:center;">
                        <i class="fas fa-wifi-slash" style="font-size:32px; margin-bottom:10px;"></i>
                        <p style="margin:0; font-size:12px;">Connect to view image</p>
                    </div>
                `;
            });
        });
    });
</script>

@endsection
