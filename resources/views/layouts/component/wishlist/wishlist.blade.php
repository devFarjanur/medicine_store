<!-- Wishlist Section Start -->
<section class="wishlist-section pt-10">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="wishlist-table bg-white">
                    <table class="w-100">
                        <thead>
                            <tr>
                                <th class="text-center">Image</th>
                                <th class="text-center">Product Name</th>
                                <th class="text-center">Stock Status</th>
                                <th class="text-center">Unit Price</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="wishlist-items">
                            <!-- Wishlist items will be dynamically inserted here by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Wishlist Section End -->

<!-- Include CSRF Token for Fetch Requests -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- JavaScript for Wishlist Management -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Retrieve the wishlist from localStorage.
        let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

        // Extract product IDs from the wishlist.
        const productIds = wishlist.map(item => item.id);

        // Fetch the latest stock information from the server.
        fetch('/wishlist/stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ productIds })
        })
            .then(response => response.json())
            .then(stockStatus => {
                // Update the stock information in the localStorage.
                wishlist = wishlist.map(item => {
                    item.stock = stockStatus[item.id] || 0;
                    item.inStock = item.stock > 0;
                    return item;
                });

                localStorage.setItem('wishlist', JSON.stringify(wishlist));

                // Now render the updated wishlist items.
                renderWishlistItems();
            });

        function renderWishlistItems() {
            const wishlistItemsContainer = document.getElementById('wishlist-items');
            wishlistItemsContainer.innerHTML = ''; // Clear the container.

            wishlist.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center thumbnail">
                        <img src="${item.photo}" alt="${item.name}" class="img-fluid">
                    </td>
                    <td>
                        <h6 class="mb-1 mt-1">${item.name}</h6>
                    </td>
                    <td class="text-center">
                        <span class="stock-status ${item.inStock ? 'text-success' : 'text-danger'} fw-bold">
                            ${item.inStock ? 'In Stock' : 'Out of Stock'}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="price fw-bold text-dark">TK. ${item.price}</span>
                    </td>
                    <td class="text-center">
                        ${item.inStock ? `
                            <a href="#" class="btn btn-secondary btn-sm add-to-cart"
                               data-id="${item.id}" data-name="${item.name}" 
                               data-price="${item.price}" data-photo="${item.photo}">
                               Add to Cart
                            </a>
                        ` : `<span class="text-danger fw-bold">Out of Stock</span>`}
                        <a href="#" class="btn btn-danger btn-sm remove-from-wishlist" data-id="${item.id}">
                            <i class="fas fa-close"></i>
                        </a>
                    </td>
                `;
                wishlistItemsContainer.appendChild(tr);

                // Add event listeners for adding to cart and removing from wishlist.
                tr.querySelector('.add-to-cart')?.addEventListener('click', (e) => {
                    e.preventDefault();
                    addToCart(item);
                });

                tr.querySelector('.remove-from-wishlist').addEventListener('click', (e) => {
                    e.preventDefault();
                    removeFromWishlist(item.id);
                });
            });
        }

        function addToCart(item) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const existingItem = cart.find(cartItem => cartItem.id === item.id);

            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ ...item, quantity: 1 });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            alert('Item added to cart!');
        }

        function removeFromWishlist(id) {
            wishlist = wishlist.filter(item => item.id !== id);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            renderWishlistItems();
        }
    });
</script>