@extends('layouts.customer_index')
@section('layouts')

<div class="container pt-80">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-center rounded-top">
                    <h3 class="text-white py-5">Order Successful</h3>
                </div>

                <div class="card-body p-3">
                    <div class="text-center">
                        <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                        <h4 class="mb-3">Thank you for your order!</h4>
                        <p class="mb-4">Your order has been placed successfully.</p>
                    </div>

                    <div class="order-details px-5">
                        <h5 class="text-muted">Order Summary:</h5>
                        <hr>
                        <p class="mb-2"><strong>Order Number:</strong> <span
                                class="text-primary">{{ $order->order_number }}</span></p>
                        <p class="mb-2"><strong>Order Date:</strong>
                            <span>{{ $order->created_at->format('F j, Y') }}</span>
                        </p>
                        <p class="mb-2"><strong>Total Amount:</strong> <span>TK.
                                {{ number_format($order->total_price, 2) }}</span></p>
                        <p class="mb-2"><strong>Payment Method:</strong>
                            <span>{{ ucfirst($order->payment_method) }}</span>
                        </p>
                    </div>


                    <div class="text-center pt-3 pb-5">
                        <p class="mb-4">You will receive a confirmation email shortly with the details of your order.
                        </p>
                        <a href="{{ route('customer.index') }}" class="btn btn-success btn-lg px-4 text-white">Return to Home</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection