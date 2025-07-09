<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\ActivityLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // For transactions if needed

class OrderController extends Controller
{
    // PUBLIC FACING - E.g., after a checkout process
    /**
     * Store a newly created order in storage.
     * This would be called after a successful payment in a real scenario.
     * For now, it can be used to simulate order creation.
     */
    public function store(Request $request)
    {
        // This is a simplified version. A real checkout would have more validation
        // and would likely get cart items from session or a cart table.
        $request->validate([
            'user_id' => 'nullable|exists:users,id', // Nullable for guest checkout
            'shipping_address' => 'required|string', // Simplified, could be JSON
            'billing_address' => 'nullable|string',  // Simplified
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            // Payment details would come from payment gateway callback typically
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $sub_total = 0;
            $orderItemsData = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product || $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Product {$product->name} is out of stock or insufficient quantity.");
                }
                $priceAtPurchase = $product->price;
                $totalItemPrice = $priceAtPurchase * $item['quantity'];
                $sub_total += $totalItemPrice;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $priceAtPurchase,
                    'total_price' => $totalItemPrice,
                ];

                // Decrement stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Example VAT and shipping (can be more dynamic)
            $vat_rate = 0.075; // 7.5%
            $vat_amount = $sub_total * $vat_rate;
            $shipping_cost = 0; // Example: Free shipping or calculate based on address/weight
            $total_amount = $sub_total + $vat_amount + $shipping_cost;

            $order = Order::create([
                'user_id' => $request->user_id ?? Auth::id(), // Use authenticated user if no user_id provided
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'sub_total' => $sub_total,
                'vat_amount' => $vat_amount,
                'shipping_cost' => $shipping_cost,
                'total_amount' => $total_amount,
                'status' => 'pending', // Initial status
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending', // Assume pending until payment gateway confirms
                'payment_reference' => $request->payment_reference,
                'notes' => $request->notes,
            ]);

            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData);
            }

            DB::commit();

            // TODO: Send order confirmation email to user
            // TODO: Send new order notification to admin (if different from quote/contact)

            // return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
            return response()->json(['message' => 'Order created successfully', 'order' => $order->load('items')], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // return redirect()->back()->with('error', 'Error placing order: ' . $e->getMessage())->withInput();
            return response()->json(['message' => 'Error placing order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified order for the authenticated user.
     */
    public function show(Order $order)
    {
        // Basic authorization: user can only see their own orders, or admin can see any
        // if (Auth::id() !== $order->user_id && !(Auth::check() && Auth::user()->isAdmin)) {
        //     abort(403);
        // }
        // For now, let's assume this route is for users viewing their own order.
        // Admin viewing will be via adminShow.
        if (Auth::id() !== $order->user_id) {
             abort(403, 'Unauthorized action.');
        }

        $order->load(['items.product', 'user']);
        // return view('pages.orders.show', compact('order'));
        return response()->json(['order' => $order]); // Placeholder
    }

    /**
     * Display a listing of orders for the authenticated user.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('items')->latest()->paginate(10);
        // return view('pages.orders.index', compact('orders'));
        return response()->json(['orders' => $orders]); // Placeholder
    }


    // === ADMIN FACING METHODS ===

    public function adminIndex(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('customer_id')) {
            $query->where('user_id', $request->customer_id);
        }
        // Add date range filters, search by order ID, etc.

        $orders = $query->paginate(15);
        $usersWithOrders = User::whereHas('orders')->pluck('name', 'id'); // For filtering

        // return view('admin.orders.index', compact('orders', 'usersWithOrders'));
        return response()->json(['orders' => $orders, 'users_with_orders' => $usersWithOrders]); // Placeholder
    }

    public function adminShow(Order $order)
    {
        $order->load(['user', 'items.product']);
        // return view('admin.orders.show', compact('order'));
        return response()->json(['order' => $order]); // Placeholder
    }

    public function adminUpdateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled,returned',
            // Optionally validate payment_status if it can be changed here too
            'payment_status' => 'nullable|string|in:pending,paid,failed,refunded',
        ]);

        $originalStatus = $order->status;
        $originalPaymentStatus = $order->payment_status;

        $order->status = $request->status;
        if ($request->filled('payment_status')) {
            $order->payment_status = $request->payment_status;
        }
        $order->save();

        ActivityLog::record(
            action: 'updated_order_status',
            loggable: $order,
            description: "User " . (Auth::user()->name ?? 'N/A') . " updated order #{$order->id} status from '{$originalStatus}' to '{$order->status}'" .
                         ($request->filled('payment_status') ? " and payment status from '{$originalPaymentStatus}' to '{$order->payment_status}'." : "."),
            properties: [
                'old' => ['status' => $originalStatus, 'payment_status' => $originalPaymentStatus],
                'new' => ['status' => $order->status, 'payment_status' => $order->payment_status]
            ]
        );

        // TODO: Send order status update notification to user

        // return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.');
        return response()->json(['message' => 'Order status updated', 'order' => $order]); // Placeholder
    }

    // No adminCreate or adminStore, as orders are created by users through checkout.
    // No adminEdit for full order details, only status updates primarily.
    // AdminDestroy might be needed but should be handled carefully (soft deletes?).
    public function adminDestroy(Order $order)
    {
        // Consider implications: stock adjustment, user notification, etc.
        // For now, simple delete. Soft deletes might be better.
        $orderId = $order->id;
        $orderAttributes = $order->load('items')->toArray(); // Load items for logging

        $order->items()->delete(); // Delete related items first if not using cascading deletes in DB (or if DB doesn't cascade)
        $order->delete();

        ActivityLog::record(
            action: 'deleted_order',
            loggable: $order, // For soft deletes
            description: "User " . (Auth::user()->name ?? 'N/A') . " deleted order #{$orderId}.",
            properties: ['attributes' => $orderAttributes]
        );

        // return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
        return response()->json(['message' => 'Order deleted'], 200); // Placeholder
    }
}
