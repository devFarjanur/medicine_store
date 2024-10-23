<!-- Related product slider start -->
@foreach ($categories as $category)
    @if ($category->products->isNotEmpty())
        <section class="related-product-slider ptb-120">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-sm-8">
                        <div class="section-title text-center text-sm-start">
                            <h2 class="mb-0">{{ $category->category_name }}</h2>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="rl-slider-btns text-center text-sm-end mt-3 mt-sm-0">
                            <button class="rl-slider-btn slider-btn-prev"><i class="fas fa-arrow-left"></i></button>
                            <button class="rl-slider-btn slider-btn-next ms-3"><i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
                <div class="rl-products-slider swiper mt-8">
                    <div class="">
                        @foreach ($category->products as $product)

                            <a href="{{ route('customer.product.details', $product->id) }}">
                                <div class="vertical-product-card rounded-2 position-relative swiper-slide">
                                    <div class="thumbnail position-relative text-center p-4">
                                        <img src="{{ asset('upload/admin_images/' . $product->photo) }}" alt="{{ $product->name }}"
                                            class="product-image">
                                    </div>
                                    <div class="card-content">
                                        <a class="mb-2 d-inline-block text-secondary fw-semibold fs-xxs">{{ $category->category_name }}</a>
                                        <a href="{{ route('customer.product.details', $product->id) }}" class="card-title fw-bold d-block mb-2">{{ $product->name }}</a>
                                        <div class="pricing mb-2 d-flex gap-2">
                                            <del class="mb-0 h6 text-gray">TK {{ $product->price + 100 }}</del>
                                            <h6 class="price text-danger mb-0">TK {{ $product->price }}</h6>
                                        </div>
                                        <a href="{{ route('customer.product.details', $product->id) }}" class="btn btn-outline-secondary d-block btn-md">
                                            View Product
                                        </a>
                                    </div>
                                </div>
                            </a>

                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        <!-- Related products slider end -->
    @endif
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Function to render total cart items count
        function renderTotalCartItems() {
            const totalItemsSpan = document.getElementById('total-items');
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalQuantity = 0;

            // Calculate total quantity
            cart.forEach(item => {
                totalQuantity += parseInt(item.quantity);
            });

            // Update total cart items count in the header
            if (totalItemsSpan) {
                totalItemsSpan.textContent = totalQuantity;
            }
        }

        // Function to handle adding product to cart
        function addToCart(button) {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const price = button.dataset.price;
            const photo = button.dataset.photo;

            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            let product = cart.find(item => item.id == id);

            if (product) {
                product.quantity += 1;
            } else {
                cart.push({ id, name, price, photo, quantity: 1 });
            }

            localStorage.setItem('cart', JSON.stringify(cart));

            // Change the button text after adding to cart
            button.textContent = 'Added to Cart';
            button.style.backgroundColor = '#28a745'; // Bootstrap success color: green
            button.style.borderColor = '#28a745'; // Bootstrap success color: green
            button.style.color = '#fff'; // White text color

            // Optionally, revert the button text back to "Add to Cart" after a few seconds
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.style.backgroundColor = ''; // Reverting to default background color
                button.style.borderColor = ''; // Reverting to default border color
                button.style.color = ''; // Reverting to default text color
            }, 2000); // 2 seconds delay

            // Render total cart items count
            renderTotalCartItems();
        }

        // Add event listeners to all add-to-cart buttons
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent the default action
                addToCart(button); // Add product to cart
            });
        });

        // Render total cart items count on page load
        renderTotalCartItems();
    });
</script>