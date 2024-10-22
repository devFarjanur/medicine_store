<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- Cart Section Start -->
<section class="cart-section pt-10">
    <div class="container">
        <div class="rounded-2 overflow-hidden">
            <table class="cart-table w-100 mt-4 bg-white">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cart-items">
                    <!-- Cart items will be dynamically generated here -->
                </tbody>
            </table>
        </div>
        <div class="row g-4">
            <div class="col-xl-7"></div>
            <div class="col-xl-5">
                <div class="cart-summery bg-white rounded-2 pt-4 pb-6 px-5 mt-4">
                    <table class="w-100">
                        <tr>
                            <td class="py-3">
                                <h5 class="mb-0 fw-medium">Subtotal</h5>
                            </td>
                            <td class="py-3">
                                <h5 class="mb-0 fw-semibold text-end" id="subtotal">TK. 0.00</h5>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="py-3">
                                <h5 class="mb-0">Total</h5>
                            </td>
                            <td class="text-end py-3">
                                <h5 class="mb-0" id="total">TK. 0.00</h5>
                            </td>
                        </tr>
                    </table>
                    <p class="mb-5 mt-2">Shipping options will be updated during checkout.</p>
                    <div class="btns-group d-flex gap-3">
                        @auth
                            <a href="{{ route('customer.checkout') }}" class="btn btn-primary btn-md rounded-1"
                                id="checkout-button">Checkout</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-md rounded-1">Checkout</a>
                        @endauth
                        <a href="{{ route('customer.product') }}"
                            class="btn btn-outline-secondary border-secondary btn-md rounded-1">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Cart Section End -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    function renderCartItems() {
        const cartItemsContainer = document.getElementById('cart-items');
        cartItemsContainer.innerHTML = ''; // Clear previous items

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let totalQuantity = 0;

        cart.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>
                <input type="checkbox" class="cart-item-checkbox" data-id="${item.id}" ${item.selected ? 'checked' : ''}>
            </td>
            <td>
                <img src="${item.photo}" alt="${item.name}" class="product-image">
            </td>
            <td class="text-start product-title">
                <h6 class="mb-0">${item.name}</h6>
            </td>
            <td>
                <div class="product-qty d-inline-flex align-items-center" data-product-id="${item.id}" data-stock="${item.stock}">
                    <div class="product-qty d-flex align-items-center">
                                                <h6 class="mb-0">${item.quantity}</h6>
                                            </div>
                </div>
            </td>
            <td>
                <span class="text-dark fw-bold me-2 d-lg-none">Unit Price:</span>
                <span class="text-dark fw-bold">TK. ${item.price}</span>
            </td>
            <td class="total-price">
                <span class="text-dark fw-bold me-2 d-lg-none">Total Price:</span>
                <span class="text-dark fw-bold">TK. ${(item.quantity * item.price).toFixed(2)}</span>
            </td>
            <td>
                <button class="btn btn-danger text-white delete-button" data-id="${item.id}">Delete</button>
            </td>
            
        `;
            cartItemsContainer.appendChild(row);

            // Calculate total quantity
            totalQuantity += parseInt(item.quantity);
        });

        // Update total quantity in the header
        const totalItemsElement = document.getElementById('total-items');
        if (totalItemsElement) {
            totalItemsElement.textContent = totalQuantity;
        }

        attachEventListeners(); // Attach event listeners after rendering items
        updateSelectedSubtotal(); // Calculate and update subtotal on page load
    }

    function attachEventListeners() {
        // Add event listeners to delete buttons
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.id;
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart = cart.filter(item => item.id !== id);
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCartItems(); // Re-render cart items after deletion
            });
        });

        // Add event listeners to increase quantity buttons
        document.querySelectorAll('.increase').forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.closest('.product-qty').dataset.productId;
                const stock = parseInt(button.closest('.product-qty').dataset.stock);
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                const item = cart.find(item => item.id === productId);

                if (item && item.quantity < stock) {
                    item.quantity++;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    renderCartItems(); // Re-render cart items after quantity update
                    updateSelectedSubtotal();
                }
            });
        });

        // Add event listeners to decrease quantity buttons
        document.querySelectorAll('.decrease').forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.closest('.product-qty').dataset.productId;
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                const item = cart.find(item => item.id === productId);

                if (item && item.quantity > 1) {
                    item.quantity--;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    renderCartItems(); // Re-render cart items after quantity update
                    updateSelectedSubtotal();
                }
            });
        });

        // Add event listeners to checkboxes to update the subtotal
        document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const id = checkbox.dataset.id;
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                const item = cart.find(item => item.id === id);
                if (item) {
                    item.selected = checkbox.checked; // Save selection state
                    localStorage.setItem('cart', JSON.stringify(cart));
                }
                updateSelectedSubtotal();
                populateDynamicItems(); // Update dynamic items in form
            });
        });
    }

    function updateSelectedSubtotal() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        let subtotal = 0;
        let anySelected = false;

        document.querySelectorAll('.cart-item-checkbox:checked').forEach(checkbox => {
            const id = checkbox.dataset.id;
            const item = cart.find(item => item.id === id);
            if (item) {
                subtotal += item.price * item.quantity;
                anySelected = true;
            }
        });

        document.getElementById('subtotal').textContent = `TK. ${subtotal.toFixed(2)}`;
        document.getElementById('total').textContent = `TK. ${subtotal.toFixed(2)}`;

        // Enable or disable the checkout button based on whether any items are selected
        const checkoutButton = document.getElementById('checkout-button');
        if (anySelected) {
            checkoutButton.classList.remove('disabled');
        } else {
            checkoutButton.classList.add('disabled');
        }
    }

    renderCartItems(); // Render cart items on page load

    function populateDynamicItems() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const dynamicItemsContainer = document.getElementById('dynamic-items');
        dynamicItemsContainer.innerHTML = '';

        // Only add selected items to the form
        cart.filter(item => item.selected).forEach((item, index) => {
            const productIdField = document.createElement('input');
            productIdField.type = 'hidden';
            productIdField.name = `items[${index}][product_id]`;
            productIdField.value = item.id;

            const quantityField = document.createElement('input');
            quantityField.type = 'hidden';
            quantityField.name = `items[${index}][quantity]`;
            quantityField.value = item.quantity;

            const unitPriceField = document.createElement('input');
            unitPriceField.type = 'hidden';
            unitPriceField.name = `items[${index}][unit_price]`;
            unitPriceField.value = item.price;

            const productNameField = document.createElement('input');
            productNameField.type = 'hidden';
            productNameField.name = `items[${index}][product_name]`;
            productNameField.value = item.name;

            const productCategoryField = document.createElement('input');
            productCategoryField.type = 'hidden';
            productCategoryField.name = `items[${index}][product_category]`;
            productCategoryField.value = item.category || 'General';

            dynamicItemsContainer.appendChild(productIdField);
            dynamicItemsContainer.appendChild(quantityField);
            dynamicItemsContainer.appendChild(unitPriceField);
            dynamicItemsContainer.appendChild(productNameField);
            dynamicItemsContainer.appendChild(productCategoryField);
        });

        updateTotalItems(); // Update the total number of items
        calculateTotals();  // Calculate totals on page load
    }

    function updateTotalItems() {
        const totalItems = cart.filter(item => item.selected).reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById('total-cart-items').textContent = `Items(${totalItems}):`;
    }

    function calculateTotals() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        let totalItemPrice = cart.filter(item => item.selected).reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const deliveryCharge = 120;
        const grandTotal = totalItemPrice + deliveryCharge;

        document.getElementById('total-item-price').textContent = `TK. ${totalItemPrice.toFixed(2)}`;
        document.getElementById('delivery-charge').textContent = `TK. ${deliveryCharge.toFixed(2)}`;
        document.getElementById('grand-total').textContent = `TK. ${grandTotal.toFixed(2)}`;
        document.getElementById('total_price').value = grandTotal.toFixed(2);
        document.getElementById('total_amount').value = grandTotal.toFixed(2);
    }

    // Update address ID when selecting different shipment addresses
    document.querySelectorAll('input[name="shipment_address"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.getElementById('address_id').value = this.value;
        });
    });

    const initialSelectedAddress = document.querySelector('input[name="shipment_address"]:checked');
    if (initialSelectedAddress) {
        document.getElementById('address_id').value = initialSelectedAddress.value;
    }

    populateDynamicItems(); // Populate dynamic items on page load

    const form = document.getElementById('orderForm');
    const cashOnDeliveryRadio = document.getElementById('cashondelivery');
    const sslCommerzRadio = document.getElementById('sslcommerz');

    function updateFormAction() {
        if (cashOnDeliveryRadio.checked) {
            form.action = "{{ route('order.store') }}";
        } else if (sslCommerzRadio.checked) {
            form.action = "{{ url('/pay') }}";
        }
    }

    updateFormAction();

    cashOnDeliveryRadio.addEventListener('change', updateFormAction);
    sslCommerzRadio.addEventListener('change', updateFormAction);

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent form submission for validation
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const selectedItems = cart.filter(item => item.selected);
        if (selectedItems.length === 0) {
            alert('Please select at least one item to proceed.');
            return;
        }

        // Submit the form after validation
        this.submit();

        // Clear the cart after successful submission
        localStorage.removeItem('cart'); 
    });
});


</script>
