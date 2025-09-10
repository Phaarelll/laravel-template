@extends('layouts/contentNavbarLayout')

@section('title', 'Shop')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Shop Header -->
    <div class="card mb-4">
      <div class="card-body text-center">
        <h2 class="card-title mb-3">
          <i class="bx bx-store me-2 text-primary"></i>
          eltro store
        </h2>
        <p class="card-text text-muted">
        Pilihan Cerdas untuk Elektronik Berkualitas
        </p>
      </div>
    </div>

    <!-- Filter and Search -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="btn-group" role="group" aria-label="Category filter">
              @foreach($categories as $category)
                <button type="button" class="btn btn-outline-primary category-filter {{ $loop->first ? 'active' : '' }}" data-category="{{ $category }}">
                  {{ $category }}
                </button>
              @endforeach
            </div>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text">
                <i class="bx bx-search"></i>
              </span>
              <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="row" id="productsGrid">
      @foreach($products as $product)
        <div class="col-lg-4 col-md-6 mb-4 product-item" data-category="{{ $product['category'] }}" data-name="{{ strtolower($product['name']) }}">
          <div class="card h-100 product-card">
            <div class="card-img-top position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
              <i class="bx bx-package" style="font-size: 4rem; color: rgba(255,255,255,0.8);"></i>
              @if(!$product['in_stock'])
                <div class="position-absolute top-0 end-0 m-2">
                  <span class="badge bg-danger">Out of Stock</span>
                </div>
              @endif
            </div>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">{{ $product['name'] }}</h5>
              <p class="card-text text-muted flex-grow-1">{{ $product['description'] }}</p>

              <!-- Rating -->
              <div class="mb-2">
                @for($i = 1; $i <= 5; $i++)
                  @if($i <= floor($product['rating']))
                    <i class="bx bxs-star text-warning"></i>
                  @elseif($i <= $product['rating'])
                    <i class="bx bxs-star-half text-warning"></i>
                  @else
                    <i class="bx bx-star text-muted"></i>
                  @endif
                @endfor
                <small class="text-muted ms-1">({{ $product['rating'] }})</small>
              </div>

              <!-- Price and Category -->
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-primary mb-0">Rp {{ number_format($product['price'], 0, ',', '.') }}</h4>
                <span class="badge bg-label-info">{{ $product['category'] }}</span>
              </div>

              <!-- Action Buttons -->
              <div class="d-grid gap-2">
                @if($product['in_stock'])
                  <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                    <i class="bx bx-cart-add me-1"></i>Add to Cart
                  </button>
                @endif
                <button class="btn btn-outline-primary" onclick="viewProduct({{ $product['id'] }})">
                  <i class="bx bx-show me-1"></i>
                  View Details
                </button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- No Products Found Message -->
    <div class="row d-none" id="noProductsMessage">
      <div class="col-12">
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="bx bx-search-alt-2 display-1 text-muted mb-3"></i>
            <h4 class="text-muted">No products found</h4>
            <p class="text-muted">Try adjusting your search or filter criteria.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Shopping Cart Summary (Fixed Position) -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
  <div class="card shadow" id="cartSummary" style="display: none;">
    <div class="card-body">
      <h6 class="card-title">
        <i class="bx bx-cart me-1"></i>
        Cart (<span id="cartCount">0</span>)
      </h6>
      <button class="btn btn-primary btn-sm" onclick="viewCart()">
        View Cart
      </button>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
// Shopping cart functionality
let cart = [];

// Category filter functionality
document.querySelectorAll('.category-filter').forEach(button => {
    button.addEventListener('click', function() {
        // Update active button
        document.querySelectorAll('.category-filter').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        const category = this.dataset.category;
        filterProducts(category);
    });
});

// Search functionality
document.getElementById('productSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const activeCategory = document.querySelector('.category-filter.active').dataset.category;
    filterProducts(activeCategory, searchTerm);
});

function filterProducts(category, searchTerm = '') {
    const products = document.querySelectorAll('.product-item');
    let visibleCount = 0;

    products.forEach(product => {
        const productCategory = product.dataset.category;
        const productName = product.dataset.name;

        const categoryMatch = category === 'All' || productCategory === category;
        const searchMatch = searchTerm === '' || productName.includes(searchTerm);

        if (categoryMatch && searchMatch) {
            product.style.display = 'block';
            visibleCount++;
        } else {
            product.style.display = 'none';
        }
    });

    // Show/hide no products message
    const noProductsMessage = document.getElementById('noProductsMessage');
    if (visibleCount === 0) {
        noProductsMessage.classList.remove('d-none');
    } else {
        noProductsMessage.classList.add('d-none');
    }
}

function addToCart(productId) {
    // Add product to cart
    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ id: productId, quantity: 1 });
    }

    updateCartDisplay();

    // Show success message
    showToast('Product added to cart!', 'success');
}

function updateCartDisplay() {
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    document.getElementById('cartCount').textContent = cartCount;

    const cartSummary = document.getElementById('cartSummary');
    if (cartCount > 0) {
        cartSummary.style.display = 'block';
    } else {
        cartSummary.style.display = 'none';
    }
}

function viewProduct(productId) {
    showToast('Product details feature coming soon!', 'info');
}

function viewCart() {
    showToast('Shopping cart feature coming soon!', 'info');
}

function showToast(message, type = 'success') {
    // Create toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
    toast.innerHTML = `
        <i class="bx bx-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
});
</script>
@endsection
