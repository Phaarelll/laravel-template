// Global cart management
class CartManager {
    constructor() {
        this.updateCartBadge();
    }

    async addItem(productId, quantity = 1) {
        try {
            const response = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateCartBadge(data.cart_count);
                this.showToast(data.message, 'success');
            } else {
                this.showToast('Failed to add item to cart', 'error');
            }
        } catch (error) {
            console.error('Error adding item to cart:', error);
            this.showToast('Error adding item to cart', 'error');
        }
    }

    async removeItem(productId) {
        try {
            const response = await fetch('/cart/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateCartBadge(data.cart_count);
                this.showToast(data.message, 'info');
                // Refresh cart page if we're on it
                if (window.location.pathname === '/cart') {
                    location.reload();
                }
            } else {
                this.showToast('Failed to remove item from cart', 'error');
            }
        } catch (error) {
            console.error('Error removing item from cart:', error);
            this.showToast('Error removing item from cart', 'error');
        }
    }

    async updateQuantity(productId, quantity) {
        try {
            const response = await fetch('/cart/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateCartBadge(data.cart_count);
                this.showToast(data.message, 'success');
                // Refresh cart page if we're on it
                if (window.location.pathname === '/cart') {
                    location.reload();
                }
            } else {
                this.showToast('Failed to update cart', 'error');
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            this.showToast('Error updating cart', 'error');
        }
    }

    async getCartItems() {
        try {
            const response = await fetch('/cart/items');
            const data = await response.json();
            
            if (data.success) {
                return data.items;
            }
            return [];
        } catch (error) {
            console.error('Error fetching cart items:', error);
            return [];
        }
    }

    async updateCartBadge(count = null) {
        const badge = document.querySelector('.cart-badge');
        
        if (!badge) return;

        if (count === null) {
            try {
                const response = await fetch('/cart/items');
                const data = await response.json();
                count = data.cart_count || 0;
            } catch (error) {
                console.error('Error fetching cart count:', error);
                count = 0;
            }
        }
        
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 1060; min-width: 300px;';
        toast.innerHTML = `
            <i class="bx bx-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 3000);
    }
}

// Initialize global cart manager
window.cartManager = new CartManager();

// Global functions for backward compatibility
window.addToCart = function(productId, quantity = 1) {
    window.cartManager.addItem(productId, quantity);
};

window.removeFromCart = function(productId) {
    window.cartManager.removeItem(productId);
};

window.updateCartQuantity = function(productId, quantity) {
    window.cartManager.updateQuantity(productId, quantity);
};

window.viewCart = function() {
    window.location.href = '/cart';
};

// Initialize cart display on page load
document.addEventListener('DOMContentLoaded', function() {
    if (window.cartManager) {
        window.cartManager.updateCartBadge();
    }
});
