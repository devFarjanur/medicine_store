<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Support\Facades\Log;

class SslCommerzPaymentController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Payment request received', $request->all());

        try {
            $post_data = array();
            $post_data['total_amount'] = $request->total_price;
            $post_data['currency'] = "BDT";
            $post_data['tran_id'] = uniqid(); // Transaction ID must be unique

            // CUSTOMER INFORMATION
            $user = User::find($request->user_id);
            if (!$user) {
                Log::error('User not found', ['user_id' => $request->user_id]);
                return response()->json(['success' => false, 'message' => 'User not found']);
            }

            $post_data['cus_name'] = $user->name ?? 'Customer Name';
            $post_data['cus_email'] = $user->email ?? 'customer@example.com';
            $post_data['cus_add1'] = 'Customer Address';
            $post_data['cus_country'] = 'Bangladesh';
            $post_data['cus_phone'] = $user->phone ?? '0000000000';

            // SHIPMENT INFORMATION
            $post_data['ship_name'] = "Store Test";
            $post_data['ship_add1'] = "Dhaka";
            $post_data['ship_country'] = 'Bangladesh';

            // SHIPPING METHOD
            $post_data['shipping_method'] = 'NO';

            // PRODUCT INFORMATION
            $post_data['product_profile'] = 'general';
            $post_data['product_name'] = 'Demo Product';
            $post_data['product_category'] = 'General';
            $post_data['num_of_item'] = count($request->input('items'));

            // Insert order into the database before initiating payment
            $order = Order::create([
                'user_id' => $request->user_id,
                'address_id' => $request->address_id,
                'total_price' => $post_data['total_amount'],
                'payment_method' => 'sslcommerz',
                'transaction_id' => $post_data['tran_id'],
                'currency' => $post_data['currency'],
                'status' => 'Pending',
            ]);

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'address_id' => 'required|exists:addresses,id',
                'total_price' => 'required|numeric',
                'payment_method' => 'required|string',
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['unit_price'] * $item['quantity'],
                ]);
            }

            Log::info('Order created', ['order_id' => $order->id]);
            session()->regenerateToken();

            // Store order ID and items in session to use later in success method
            session(['order_id' => $order->id, 'order_items' => $request->input('items')]);

            $sslc = new SslCommerzNotification();
            Log::info('Initiating payment with SSLCommerz', $post_data);
            $payment_options = $sslc->makePayment($post_data, 'hosted');

            if (!is_array($payment_options)) {
                Log::error('Payment initiation failed', ['options' => $payment_options]);
                return response()->json(['success' => false, 'message' => 'Payment initiation failed']);
            }

            Log::info('Payment initiation successful', $payment_options);
            return response()->json($payment_options);
        } catch (\Exception $e) {
            Log::error('Exception occurred during payment initiation', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Internal Server Error']);
        }
    }

    public function success(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');

        $sslc = new SslCommerzNotification();

        // Check order status in order table against the transaction id or order id.
        $order = Order::where('transaction_id', $tran_id)->first();

        if ($order && $order->status == 'Pending') {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation) {
                // Update order status to Processing
                $order->update(['status' => 'Processing', 'payment_status' => 'paid']);

                // Retrieve order items from session
                $items = session('order_items', []);

                if (!empty($items)) {
                    foreach ($items as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'total_price' => $item['quantity'] * $item['unit_price'],
                        ]);
                    }
                }

                // Clear the session after saving the items
                session()->forget('order_id');
                session()->forget('order_items');

                // Redirect to success page after order is stored
                return redirect()->route('order.success', ['orderNumber' => $order->order_number]);
            } else {
                return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                    ->with('error', 'Validation failed');
            }
        } else if ($order && ($order->status == 'Processing' || $order->status == 'Complete')) {
            // Transaction is already processed
            return redirect()->route('order.success', ['orderNumber' => $order->order_number]);
        } else {
            return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                ->with('error', 'Invalid Transaction');
        }
    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order = Order::where('transaction_id', $tran_id)->first();

        if ($order && $order->status == 'Pending') {
            $order->update(['status' => 'Failed']);
            return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                ->with('error', 'Transaction Failed');
        } else if ($order && ($order->status == 'Processing' || $order->status == 'Complete')) {
            return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                ->with('success', 'Transaction already successful');
        } else {
            return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                ->with('error', 'Invalid Transaction');
        }
    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order = Order::where('transaction_id', $tran_id)->first();

        if ($order && $order->status == 'Pending') {
            $order->update(['status' => 'Canceled']);
            return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                ->with('error', 'Transaction Canceled');
        } else if ($order && ($order->status == 'Processing' || $order->status == 'Complete')) {
            return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                ->with('success', 'Transaction already successful');
        } else {
            return redirect()->route('order.success', ['orderNumber' => $order->order_number])
                ->with('error', 'Invalid Transaction');
        }
    }

    public function ipn(Request $request)
    {
        if ($request->input('tran_id')) {
            $tran_id = $request->input('tran_id');
            $order = Order::where('transaction_id', $tran_id)->first();

            if ($order && $order->status == 'Pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $order->amount, $order->currency);

                if ($validation) {
                    $order->update(['status' => 'Processing', 'payment_status' => 'paid']);
                    echo "Transaction is successfully Completed";
                } else {
                    echo "Validation Failed";
                }
            } else if ($order && ($order->status == 'Processing' || $order->status == 'Complete')) {
                echo "Transaction is already successfully Completed";
            } else {
                echo "Invalid Transaction";
            }
        } else {
            echo "Invalid Data";
        }
    }
}
