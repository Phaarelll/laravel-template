@extends('layouts/contentNavbarLayout')

@section('title', 'Shopping Cart')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Shop /</span> Shopping Cart
  </h4>

  <div class="row">
    <!-- Cart Items -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Cart Items</h5>
          <span class="badge bg-primary">{{ $cartItems->count() }}</span>
        </div>
        <div class="card-body">
          @if($cartItems->count() > 0)
            @foreach($cartItems as $item)
            <div class="d-flex align-items-center border-bottom pb-3 mb-3" id="cart-item-{{ $item->product_id }}">
              <div class="flex-shrink-0">
                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="rounded" width="80" height="80" style="object-fit: cover;">
              </div>
              <div class="flex-grow-1 ms-3">
                <h6 class="mb-1">{{ $item->product->name }}</h6>
                <p class="text-muted mb-2 small">{{ Str::limit($item->product->description, 100) }}</p>
                <div class="d-flex align-items-center">
                  <span class="badge bg-label-info me-2">{{ $item->product->category }}</span>
                  <div class="rating">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="bx bx{{ $i <= $item->product->rating ? 's' : '' }}-star text-warning"></i>
                    @endfor
                    <span class="text-muted ms-1">({{ $item->product->rating }})</span>
                  </div>
                </div>
              </div>
              <div class="flex-shrink-0 text-end">
                <div class="mb-2">
                  <strong class="text-primary">Rp {{ number_format($item->price, 0, ',', '.') }}</strong>
                </div>
                <div class="input-group input-group-sm mb-2" style="width: 120px;">
                  <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})">-</button>
                  <input type="text" class="form-control text-center" value="{{ $item->quantity }}" readonly>
                  <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})">+</button>
                </div>
                <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart({{ $item->product_id }})">
                  <i class="bx bx-trash"></i> Remove
                </button>
              </div>
            </div>
            @endforeach
          @else
            <div class="text-center py-5">
              <i class="bx bx-cart bx-lg text-muted mb-3"></i>
              <h5 class="text-muted">Your cart is empty</h5>
              <p class="text-muted">Add some products to get started!</p>
              <a href="{{ route('shop') }}" class="btn btn-primary">
                <i class="bx bx-store me-1"></i>Continue Shopping
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Cart Summary -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Order Summary</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Shipping:</span>
            <span>{{ $shipping == 0 ? 'Gratis' : 'Rp ' . number_format($shipping, 0, ',', '.') }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Tax (8%):</span>
            <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between mb-3">
            <strong>Total:</strong>
            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
          </div>
          
          <div class="d-grid gap-2">
            @if($cartItems->count() > 0)
              <button class="btn btn-primary" onclick="checkout()">
                <i class="bx bx-credit-card me-1"></i>
                Proceed to Checkout
              </button>
            @endif
            <a href="{{ route('shop') }}" class="btn btn-outline-primary">
              <i class="bx bx-arrow-back me-1"></i>
              Continue Shopping
            </a>
          </div>
        </div>
      </div>

      <!-- Promo Code -->
      <div class="card mt-3">
        <div class="card-header">
          <h6 class="mb-0">Promo Code</h6>
        </div>
        <div class="card-body">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Enter promo code" id="promoCode">
            <button class="btn btn-outline-primary" onclick="applyPromo()">Apply</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="{{ asset('resources/js/cart.js') }}"></script>
<script>
function applyPromo() {
    const promoCode = document.getElementById('promoCode').value.trim();
    if (promoCode) {
        if (promoCode.toUpperCase() === 'SAVE10') {
            window.cartManager.showToast('Promo code applied! 10% discount', 'success');
        } else {
            window.cartManager.showToast('Invalid promo code', 'error');
        }
    }
}

function checkout() {
    window.cartManager.showToast('Checkout feature coming soon!', 'info');
}
</script>
@endsection
