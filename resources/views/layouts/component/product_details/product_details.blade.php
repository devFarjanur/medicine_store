<!--product details start-->
<section class="product-details-area pt-10">
    <div class="container">
        <div class="row g-4">
            <div class="col-xl-9">
                <div class="product-details">
                    <div class="gstore-product-quick-view bg-white rounded-3 py-6 px-4">
                        <div class="row align-items-center g-4">
                            <div class="col-xl-6 align-self-end">
                                <div class="quickview-double-slider">
                                    <div class="quickview-product-slider swiper">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide text-center">
                                                <img src="{{ asset('upload/admin_images/' . $product->photo) }}"
                                                    alt="{{ $product->name }}" class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="product-info">
                                    <h4 class="mt-1 mb-3">{{ $product->name }}</h4>
                                    <div class="pricing mt-2">
                                        <span class="fw-bold fs-xs text-danger">TK {{ $product->price }}</span>
                                        <span class="fw-bold fs-xs deleted ms-1">TK {{ $product->price + 100 }}</span>
                                    </div>
                                    <div class="widget-title d-flex mt-4">
                                        <h6 class="mb-1 flex-shrink-0">Description</h6>
                                        <span
                                            class="hr-line w-100 position-relative d-block align-self-end ms-1"></span>
                                    </div>
                                    <p>{{ $product->description }}</p>

                                    <p> Stock: {{ $product->stock }}</p>

                                    <!-- Check if the product is in stock -->
                                    @if($product->stock > 0)
                                        <h6 class="fs-md mb-2 mt-3">Quantity:</h6>
                                        <div class="d-flex align-items-center gap-4 flex-wrap">
                                            <div class="product-qty d-flex align-items-center">
                                                <button class="decrease">-</button>
                                                <input type="text" value="1" class="quassntity">
                                                <button class="increase">+</button>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-4 flex-wrap pt-3">
                                            <a href="#" class="btn btn-secondary btn-md d-block add-to-cart"
                                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-photo="{{ asset('upload/admin_images/' . $product->photo) }}"
                                                data-stock="{{ $product->stock }}">
                                                Add to Cart
                                            </a>
                                            <a href="#" class="btn btn-outline-secondary d-block btn-md add-to-wishlist"
                                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-photo="{{ asset('upload/admin_images/' . $product->photo) }}"
                                                data-instock="{{ $product->stock > 0 ? 'true' : 'false' }}">
                                                Add to Wishlist
                                            </a>
                                        </div>
                                    @else
                                        <!-- Show Stock Out message if product is out of stock -->
                                        <div class="d-flex align-items-center gap-4 flex-wrap pt-3">
                                            <span class="text-danger fw-bold">Stock Out</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-8">
                <div class="gshop-sidebar">
                    <div class="sidebar-widget info-sidebar bg-white rounded-3 py-3">
                        <div class="sidebar-info-list d-flex align-items-center gap-3 p-4 border-top">
                            <span
                                class="icon-wrapper d-inline-flex align-items-center justify-content-center rounded-circle text-primary">
                                <i class="fa-regular fa-smile"></i>
                            </span>
                            <div class="info-right">
                                <h6 class="mb-1 fs-md">100% Halal and Authentic Product</h6>
                                <span class="fw-medium fs-xs">Guaranteed Product Warranty</span>
                            </div>
                        </div>
                        <div class="sidebar-info-list d-flex align-items-center gap-3 p-4 border-top">
                            <span
                                class="icon-wrapper d-inline-flex align-items-center justify-content-center rounded-circle text-primary">
                                <i class="fa-regular fa-heart"></i>
                            </span>
                            <div class="info-right">
                                <h6 class="mb-1 fs-md">Safety & Secure</h6>
                                <span class="fw-medium fs-xs">We Care More About Your Health</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--product details end-->

<script>
    document.addEventListener('DOMContentLoaded', () => {

        function renderTotalCartItems() {
            const totalItemsSpan = document.getElementById('total-items');
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalQuantity = 0;


            cart.forEach(item => {
                totalQuantity += parseInt(item.quantity);
            });


            if (totalItemsSpan) {
                totalItemsSpan.textContent = totalQuantity;
            }
        }


        function addToCart(button, quantity = 1) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const price = button.dataset.price;
            const photo = button.dataset.photo;
            const stock = parseInt(button.dataset.stock);

            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            let product = cart.find(item => item.id == id);

            if (product) {
                product.quantity += quantity;
                if (product.quantity > stock) {
                    product.quantity = stock;
                }
            } else {
                cart.push({ id, name, price, photo, quantity });
            }

            localStorage.setItem('cart', JSON.stringify(cart));

            button.textContent = 'Added to Cart';
            button.style.backgroundColor = '#28a745';
            button.style.borderColor = '#28a745';
            button.style.color = '#fff';


            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.style.backgroundColor = '';
                button.style.borderColor = '';
                button.style.color = '';
            }, 2000);


            renderTotalCartItems();
        }


        function addToWishlist(button) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const price = button.dataset.price;
            const photo = button.dataset.photo;
            const inStock = button.dataset.instock === 'true';

            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

            if (!wishlist.find(item => item.id == id)) {
                wishlist.push({ id, name, price, photo, inStock });
            }

            localStorage.setItem('wishlist', JSON.stringify(wishlist));


            button.textContent = 'Added to Wishlist';
            button.style.backgroundColor = '#ffc107';
            button.style.borderColor = '#ffc107';
            button.style.color = '#fff';


            setTimeout(() => {
                button.textContent = 'Add to Wishlist';
                button.style.backgroundColor = '';
                button.style.borderColor = '';
                button.style.color = '';
            }, 2000);
        }


        function updateQuantity(input, isIncrease) {
            const stock = parseInt(input.closest('.product-info').querySelector('.add-to-cart').dataset.stock);
            let quantity = parseInt(input.value);

            if (isIncrease) {
                quantity = (quantity < stock) ? quantity + 1 : stock;
            } else {
                quantity = (quantity > 1) ? quantity - 1 : 1;
            }

            input.value = quantity;
            return quantity;
        }

        document.querySelectorAll('.increase').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const input = button.closest('.product-qty').querySelector('input[type="text"]');
                const quantity = updateQuantity(input, true);
                button.closest('.product-info').querySelector('.add-to-cart').dataset.quantity = quantity;
            });
        });

        document.querySelectorAll('.decrease').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const input = button.closest('.product-qty').querySelector('input[type="text"]');
                const quantity = updateQuantity(input, false);
                button.closest('.product-info').querySelector('.add-to-cart').dataset.quantity = quantity;
            });
        });


        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const quantity = parseInt(button.closest('.product-info').querySelector('input[type="text"]').value);
                addToCart(button, quantity);
            });
        });


        document.querySelectorAll('.add-to-wishlist').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                addToWishlist(button);
            });
        });


        renderTotalCartItems();
    });
</script>