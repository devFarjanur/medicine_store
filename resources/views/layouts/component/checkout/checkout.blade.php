<!-- Checkout Section Start -->
<div class="checkout-section ptb-120">
    <div class="container">
        <div class="row g-4">
            <div class="col-xl-8">
                <!-- Address Section -->
                <div class="checkout-steps">
                    <div class="d-flex justify-content-between">
                    <h4 class="mb-5">Shipment Address</h4>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#addAddressModal" class="fw-semibold">
                            <i class="fas fa-plus me-1"></i> Add Address
                        </a>
                    </div>

                    <div class="row g-4">
                        @foreach ($addresses as $address)
                            <div class="col-lg-6 col-sm-6">
                                <div class="tt-address-content">
                                    <input type="radio" class="tt-custom-radio" name="shipment_address"
                                        id="shipment-address-{{ $loop->index }}" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }}>
                                    <label for="shipment-address-{{ $loop->index }}"
                                        class="tt-address-info bg-white rounded p-4 pb-5 position-relative"
                                        data-address-id="{{ $address->id }}">
                                        <strong>{{ $address->division }}, {{ $address->city }}</strong>
                                        <address class="fs-sm mb-0">
                                            Road No: {{ $address->road_no }}<br>
                                            House No: {{ $address->house_no }}
                                        </address>

                                        <!-- Edit Address Button -->
                                        <a href="#" class="tt-edit-address checkout-radio-link position-absolute"
                                            data-bs-toggle="modal" data-bs-target="#editAddressModal"
                                            data-address="{{ json_encode($address) }}">Edit</a>
                                        <!-- Delete Address Form -->
                                        <form action="{{ route('address.delete', $address->id) }}" method="POST"
                                            class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger tt-delete-address"
                                                onclick="return confirm('Are you sure you want to delete this address?');">
                                                Delete
                                            </button>
                                        </form>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Add Address Modal -->
                <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('address.add') }}" method="POST" id="addAddressForm">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="division" class="form-label">Division</label>
                                        <input type="text" class="form-control" id="division" name="division" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="road_no" class="form-label">Road No</label>
                                        <input type="text" class="form-control" id="road_no" name="road_no" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="house_no" class="form-label">House No</label>
                                        <input type="text" class="form-control" id="house_no" name="house_no" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Address</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Address Modal -->
                <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="POST" id="editAddressForm">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="edit_address_id" name="id">
                                    <div class="mb-3">
                                        <label for="edit_division" class="form-label">Division</label>
                                        <input type="text" class="form-control" id="edit_division" name="division"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="edit_city" name="city" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_road_no" class="form-label">Road No</label>
                                        <input type="text" class="form-control" id="edit_road_no" name="road_no"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_house_no" class="form-label">House No</label>
                                        <input type="text" class="form-control" id="edit_house_no" name="house_no"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Address</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary and Payment Section -->
            <div class="col-xl-4">
                <div class="checkout-sidebar">
                    <div class="sidebar-widget checkout-sidebar py-6 px-4 bg-white rounded-2">
                        <div class="widget-title d-flex">
                            <h5 class="mb-0 flex-shrink-0">Order Summary</h5>
                            <span class="hr-line w-100 position-relative d-block align-self-end ms-1"></span>
                        </div>
                        <table class="sidebar-table w-100 mt-5">
                            <tr>
                                <td id="total-cart-items">Items(0):</td>
                                <td id="total-item-price" class="text-end">TK. 0.00</td>
                            </tr>
                            <tr>
                                <td>Delivery Charge:</td>
                                <td class="text-end" id="delivery-charge">TK. 0.00</td>
                            </tr>
                            <tr>
                                <td>Total:</td>
                                <td class="text-end" id="grand-total">TK. 0.00</td>
                                <input type="hidden" name="total_amount" id="total_amount" value="">
                            </tr>
                        </table>

                        <!-- Order Form Integration -->
                        <form action="{{ route('order.store') }}" method="POST" id="orderForm">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="address_id" id="address_id" value="">
                            <input type="hidden" name="total_price" id="total_price" value="">
                            <input type="hidden" id="payment_method" name="payment_method" value="cashondelivery">

                            <!-- Dynamic Items -->
                            <div id="dynamic-items"></div>

                            <div class="mb-4">
                                <h5>Select Payment Method</h5>
                                <div>
                                    <input type="radio" id="cashondelivery" name="payment_method" value="cashondelivery"
                                        checked>
                                    <label for="cashondelivery">Cash on Delivery</label>
                                </div>
                                <div>
                                    <input type="radio" id="sslcommerz" name="payment_method" value="sslcommerz">
                                    <label for="sslcommerz">Online Payment</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-md rounded mt-6 w-100">Place Order</button>
                            <p class="mt-3 mb-0 fs-xs">By placing your order you agree to our company <a
                                    href="#">Privacy-policy</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Checkout Section End -->

<!-- JavaScript for Edit Modal and Cart Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Populate dynamic items in the form from the cart
        function populateDynamicItems() {
            const dynamicItemsContainer = document.getElementById('dynamic-items');
            dynamicItemsContainer.innerHTML = '';

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

                dynamicItemsContainer.appendChild(productIdField);
                dynamicItemsContainer.appendChild(quantityField);
                dynamicItemsContainer.appendChild(unitPriceField);
            });

            updateTotalItems();
            calculateTotals();
        }

        // Update the total items count in the cart
        function updateTotalItems() {
            const totalItems = cart.filter(item => item.selected).reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('total-cart-items').textContent = `Items(${totalItems}):`;
        }

        // Calculate totals including item price and delivery charge
        function calculateTotals() {
            let totalItemPrice = cart.filter(item => item.selected).reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const deliveryCharge = 120;
            const grandTotal = totalItemPrice + deliveryCharge;

            document.getElementById('total-item-price').textContent = `TK. ${totalItemPrice.toFixed(2)}`;
            document.getElementById('delivery-charge').textContent = `TK. ${deliveryCharge.toFixed(2)}`;
            document.getElementById('grand-total').textContent = `TK. ${grandTotal.toFixed(2)}`;
            document.getElementById('total_price').value = grandTotal.toFixed(2);
            document.getElementById('total_amount').value = grandTotal.toFixed(2);
        }

        // Handle the selection of the shipment address
        document.querySelectorAll('input[name="shipment_address"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('address_id').value = this.value;
            });
        });

        const initialSelectedAddress = document.querySelector('input[name="shipment_address"]:checked');
        if (initialSelectedAddress) {
            document.getElementById('address_id').value = initialSelectedAddress.value;
        }

        populateDynamicItems();

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

        form.addEventListener('submit', function () {
            localStorage.removeItem('cart');
        });

        // Edit Address Modal Population
        document.querySelectorAll('.tt-edit-address').forEach(editBtn => {
            editBtn.addEventListener('click', function () {
                const address = JSON.parse(this.dataset.address);
                document.getElementById('edit_address_id').value = address.id;
                document.getElementById('edit_division').value = address.division;
                document.getElementById('edit_city').value = address.city;
                document.getElementById('edit_road_no').value = address.road_no;
                document.getElementById('edit_house_no').value = address.house_no;

                // Update the form action with the correct ID for the PUT request
                document.getElementById('editAddressForm').action = `/checkout/addresses/edit/${address.id}`;
            });
        });
    });
</script>