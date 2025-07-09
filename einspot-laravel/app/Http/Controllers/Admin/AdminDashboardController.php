<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\QuoteRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- Core Stats ---
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        // Assuming 'confirmed' is not a direct status, but 'processing' or 'paid' payment might imply it.
        // For "Confirmed Order", let's use 'processing' or 'shipped' or 'delivered'
        $confirmedOrders = Order::whereIn('status', ['processing', 'shipped', 'delivered'])->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();

        // "Order Return" - This needs a specific status like 'returned' or a separate returns table.
        // Assuming 'returned' is a status in the orders table for now.
        $returnedOrders = Order::where('status', 'returned')->count();

        $totalCustomers = User::where('isAdmin', false)->count(); // Count non-admin users
        $totalQuoteRequests = QuoteRequest::count();

        // --- Sales Data (Example: Total Revenue) ---
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');

        // --- Top Category By Sales ---
        // This requires joining OrderItems with Products, then Products with Categories.
        // And filtering by a time period.
        $timePeriod = $request->input('sales_period', 'all'); // 'today', 'week', 'month', 'year', 'all'

        $topCategoriesQuery = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid') // Only count paid orders for sales
            ->where('categories.type', 'product')   // Only product categories
            ->select('categories.name as category_name', DB::raw('SUM(order_items.total_price) as total_sales'))
            ->groupBy('categories.name')
            ->orderBy('total_sales', 'desc');

        switch ($timePeriod) {
            case 'today':
                $topCategoriesQuery->whereDate('orders.created_at', Carbon::today());
                break;
            case 'week':
                $topCategoriesQuery->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $topCategoriesQuery->whereMonth('orders.created_at', Carbon::now()->month)->whereYear('orders.created_at', Carbon::now()->year);
                break;
            case 'year':
                $topCategoriesQuery->whereYear('orders.created_at', Carbon::now()->year);
                break;
        }
        $topCategories = $topCategoriesQuery->take(5)->get();


        // --- Latest Products ---
        $latestProducts = Product::latest()->take(10)->get(['id', 'name', 'price', 'images']);


        // --- Recent Orders ---
        $recentOrders = Order::with('user')->latest()->take(10)->get(['id', 'user_id', 'total_amount', 'status', 'created_at']);


        $stats = [
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'confirmedOrders' => $confirmedOrders, // This is an aggregation
            'cancelledOrders' => $cancelledOrders,
            'shippedOrders' => $shippedOrders,
            'deliveredOrders' => $deliveredOrders,
            'returnedOrders' => $returnedOrders, // Needs 'returned' status
            'totalCustomers' => $totalCustomers,
            'totalQuoteRequests' => $totalQuoteRequests,
            'totalRevenue' => $totalRevenue, // Example additional stat
        ];

        // This data would be passed to the admin dashboard view
        // return view('admin.dashboard', compact('stats', 'topCategories', 'latestProducts', 'recentOrders', 'timePeriod'));

        return response()->json([ // Placeholder response
            'message' => 'Admin Dashboard Data',
            'stats' => $stats,
            'topCategories' => $topCategories,
            'latestProducts' => $latestProducts,
            'recentOrders' => $recentOrders,
            'selectedSalesPeriod' => $timePeriod,
        ]);
    }
}
