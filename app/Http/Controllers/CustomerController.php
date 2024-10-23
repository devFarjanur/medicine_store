<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Library\SslCommerz\SslCommerzNotification;
use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{

    // ------------------ home page

    public function CustomerIndex()
    {

        $categories = Category::with('products')->get();


        return view('layouts.pages.home', compact('categories'));
    }


    public function CustomerCategoryProduct($id)
    {
        $category = Category::with('products')->findOrFail($id);
        $categories = Category::with('products')->get();
        $products = $category->products;

        // Debugging: Log the fetched data
        \Log::info('Category: ' . $category);
        \Log::info('Products: ' . $products);

        return view('layouts.pages.categoryproductlist', compact('category', 'products', 'categories'));
    }



    // ----------------- product page


    public function CustomerProduct()
    {
        $categories = Category::with('products')->get();
        $products = Product::with('category')->get();
        return view('layouts.pages.product', compact('categories', 'products'));
    }


    // ------------------ customer products details


    public function CustomerProductDetials($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::with('products')->get();
        return view('layouts.pages.product_details', compact('categories', 'product'));
    }

    // ------------------ about page

    public function CustomerAbout()
    {
        $categories = Category::with('products')->get();
        return view('layouts.pages.about', compact('categories'));
    }

    // ------------------- contact page

    public function CustomerContact()
    {
        $categories = Category::with('products')->get();
        return view('layouts.pages.contact', compact('categories'));
    }


    public function CustomerWishList()
    {

        $categories = Category::with('products')->get();
        return view('layouts.pages.wishlist', compact('categories'));
    }

    public function getProductStock(Request $request)
    {
        $productIds = $request->input('productIds', []);
        $products = Product::whereIn('id', $productIds)->get(['id', 'stock']);
        $stockStatus = $products->mapWithKeys(function ($product) {
            return [$product->id => $product->stock];
        });

        return response()->json($stockStatus);
    }


    // ------------------ cart page

    public function CustomerCart()
    {

        $categories = Category::with('products')->get();
        return view('layouts.pages.cart', compact('categories'));
    }

    // ------------------ checkout page

    public function CustomerCheckout()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        $addresses = Address::where('user_id', Auth::id())->with('user')->get();
        $categories = Category::with('products')->get();
        return view('layouts.pages.checkout', compact('categories', 'profileData', 'addresses'));
    }


    public function addAddress(Request $request)
    {
        Address::create([
            'user_id' => Auth::id(),
            'division' => $request->division,
            'city' => $request->city,
            'road_no' => $request->road_no,
            'house_no' => $request->house_no,
        ]);

        return redirect()->back()->with('success', 'Address added successfully');
    }

    public function editAddress(Request $request, $id)
    {
        $request->validate([
            'division' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'road_no' => 'required|string|max:255',
            'house_no' => 'required|string|max:255',
        ]);

        $address = Address::findOrFail($id);
        $address->update([
            'division' => $request->division,
            'city' => $request->city,
            'road_no' => $request->road_no,
            'house_no' => $request->house_no,
        ]);

        return redirect()->back()->with('success', 'Address updated successfully');
    }

    public function deleteAddress($id)
    {
        $address = Address::findOrFail($id);
        if ($address) {
            $address->delete();
            return redirect()->back()->with('success', 'Address deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete address');
    }


    public function OrderStore(Request $request)
    {
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

        $order = Order::create([
            'user_id' => $validated['user_id'],
            'address_id' => $validated['address_id'],
            'total_price' => $validated['total_price'],
            'payment_method' => $validated['payment_method'],
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_PENDING,
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

        // Make sure to pass the 'orderNumber' to the route
        return redirect()->route('order.success', ['orderNumber' => $order->order_number]);
    }



    public function OrderSuccess($orderNumber)
    {

        $categories = Category::with('products')->get();
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('layouts.pages.OrderSuccess', compact('order', 'categories'));
    }




    // ------------------------- invoice page

    public function CustomerInvoice()
    {

        $categories = Category::with('products')->get();
        return view('layouts.pages.invoice', compact('categories'));
    }


    // ----------------------- customer account page  


    public function CustomerMyaccount()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        $categories = Category::with('products')->get();

        // Fetch all addresses of the user
        $shippingAddresses = Address::where('user_id', $id)->get(); // This will return all addresses

        // Fetch the user's orders with associated products
        $orders = Order::where('user_id', $id)->with('items.product')->get();

        // Group orders by status
        $ordersPending = $orders->where('status', 'Pending');
        $ordersProcessing = $orders->where('status', 'Processing');
        $ordersShipped = $orders->where('status', 'Shipped');
        $ordersDelivered = $orders->where('status', 'Delivered');
        $ordersCancelled = $orders->where('status', 'Cancelled');
        $ordersReturned = $orders->where('status', 'Returned');

        // Calculate the total number of orders
        $totalOrders = $orders->count();

        return view(
            'layouts.pages.myaccount',
            compact(
                'orders',
                'shippingAddresses',
                'profileData',
                'categories',
                'ordersPending',
                'ordersProcessing',
                'ordersShipped',
                'ordersDelivered',
                'ordersCancelled',
                'ordersReturned',
                'totalOrders'
            )
        );
    }



    // -------------------------- customer update profile

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->firstname = $request->firstname;
        $data->lastname = $request->lastname;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->birthday = $request->birthday;
        $data->username = $request->username;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/admin_images/' . $data->photo));
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'), $filename);
            $data->photo = $filename;
        }

        $data->save();

        $notification = array(
            'message' => 'Profile Updated Successfully',
            'alter-type' => 'success'
        );


        return redirect()->back()->with($notification);
    }

    // --------------------- customer change account password

    public function changePassword(Request $request)
    {

        $rules = [
            'email' => 'required|string|email|max:255',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ];

        $request->validate($rules);


        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return redirect()->back()->withErrors(['old_password' => 'The provided current password does not match your password']);
        }


        Auth::user()->update(['password' => Hash::make($request->new_password)]);

        $notification = array(
            'message' => 'Password changed successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }









}
