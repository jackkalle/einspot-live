@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-700">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">
            Hi {{ Auth::user()->firstName ?? Auth::user()->name }}!
            <span class="inline-block w-6 h-6 bg-einspot-red-500 text-white rounded-full flex items-center justify-center text-xs align-middle ml-1">
                {{ strtoupper(substr(Auth::user()->firstName ?? Auth::user()->name, 0, 1)) }}
            </span>
            This is your store analytics.
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @php
            $statsCards = [
                ['label' => 'Total Orders', 'value' => $stats['totalOrders'] ?? 0, 'icon' => '🛒', 'color' => 'bg-blue-500'],
                ['label' => 'Pending Orders', 'value' => $stats['pendingOrders'] ?? 0, 'icon' => '⏳', 'color' => 'bg-yellow-500'],
                ['label' => 'Confirmed Orders', 'value' => $stats['confirmedOrders'] ?? 0, 'icon' => '✅', 'color' => 'bg-green-500'],
                ['label' => 'Shipped Orders', 'value' => $stats['shippedOrders'] ?? 0, 'icon' => '🚚', 'color' => 'bg-indigo-500'],
                ['label' => 'Delivered Orders', 'value' => $stats['deliveredOrders'] ?? 0, 'icon' => '📦', 'color' => 'bg-teal-500'],
                ['label' => 'Cancelled Orders', 'value' => $stats['cancelledOrders'] ?? 0, 'icon' => '❌', 'color' => 'bg-red-500'],
                ['label' => 'Returned Orders', 'value' => $stats['returnedOrders'] ?? 0, 'icon' => '↩️', 'color' => 'bg-orange-500'],
                ['label' => 'Total Customers', 'value' => $stats['totalCustomers'] ?? 0, 'icon' => '👥', 'color' => 'bg-purple-500'],
                ['label' => 'Total Quote Requests', 'value' => $stats['totalQuoteRequests'] ?? 0, 'icon' => '💬', 'color' => 'bg-pink-500'],
                ['label' => 'Total Revenue', 'value' => '₦'.number_format($stats['totalRevenue'] ?? 0, 2), 'icon' => '💰', 'color' => 'bg-green-600'],
            ];
        @endphp

        @foreach($statsCards as $card)
        <div class="{{ $card['color'] }} text-white p-6 rounded-xl shadow-lg flex items-center justify-between">
            <div>
                <div class="text-4xl font-bold">{{ $card['value'] }}</div>
                <div class="text-sm opacity-90">{{ $card['label'] }}</div>
            </div>
            <div class="text-4xl opacity-70">{{ $card['icon'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Order Status Distribution</h2>
            <canvas id="orderStatusChart" height="200"></canvas> {{-- Placeholder for Pie/Doughnut Chart --}}
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Sales Overview (Last 7 Days)</h2>
            <canvas id="salesOverviewChart" height="200"></canvas> {{-- Placeholder for Bar Chart --}}
        </div>
    </div>

    {{-- Top Categories & Latest Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Top Category By Sales --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Top Categories by Sales</h2>
                {{-- TODO: Implement time period filter dropdown --}}
                <select id="salesPeriodFilter" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="all" @if($selectedSalesPeriod == 'all') selected @endif>All Time</option>
                    <option value="today" @if($selectedSalesPeriod == 'today') selected @endif>Today</option>
                    <option value="week" @if($selectedSalesPeriod == 'week') selected @endif>This Week</option>
                    <option value="month" @if($selectedSalesPeriod == 'month') selected @endif>This Month</option>
                    <option value="year" @if($selectedSalesPeriod == 'year') selected @endif>This Year</option>
                </select>
            </div>
            @if(isset($topCategories) && $topCategories->count() > 0)
                <ul class="space-y-3">
                    @foreach($topCategories as $index => $category)
                    <li class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">{{ $index + 1 }}. {{ $category->category_name }}</span>
                        <span class="font-semibold text-gray-800">₦{{ number_format($category->total_sales, 2) }}</span>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-sm">No sales data available for the selected period.</p>
            @endif
        </div>

        {{-- Latest Products --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Latest Products</h2>
            @if(isset($latestProducts) && $latestProducts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Image</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Product</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($latestProducts as $product)
                        <tr>
                            <td class="px-4 py-2">
                                <img src="{{ $product->images && count($product->images) > 0 ? Storage::url($product->images[0]) : 'https://via.placeholder.com/50x50' }}" alt="{{$product->name}}" class="h-10 w-10 object-cover rounded">
                            </td>
                            <td class="px-4 py-2 text-gray-700 hover:text-einspot-red-600"><a href="{{ route('admin.products.edit', $product->id) }}">{{ Str::limit($product->name, 40) }}</a></td>
                            <td class="px-4 py-2 text-gray-600">₦{{ number_format($product->price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500">No products found.</p>
            @endif
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Recent Orders</h2>
        @if(isset($recentOrders) && $recentOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Order ID</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Customer</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Date</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Total</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                    <tr>
                        <td class="px-4 py-2 text-einspot-red-600 hover:underline"><a href="{{-- route('admin.orders.show', $order->id) --}}">#{{ $order->id }}</a></td>
                        <td class="px-4 py-2 text-gray-700">{{ $order->user->name ?? 'Guest' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-2 text-gray-600">₦{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @switch($order->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('processing') bg-blue-100 text-blue-800 @break
                                    @case('shipped') bg-indigo-100 text-indigo-800 @break
                                    @case('delivered') bg-green-100 text-green-800 @break
                                    @case('cancelled') bg-red-100 text-red-800 @break
                                    @case('returned') bg-orange-100 text-orange-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500">No recent orders.</p>
        @endif
    </div>

@endsection

@push('scripts')
{{-- Script for Chart.js integration --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mock data for charts - replace with data from controller
        const orderStatusData = {
            labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Returned'],
            datasets: [{
                label: 'Order Statuses',
                data: [
                    {{ $stats['pendingOrders'] ?? 0 }},
                    {{ ($stats['confirmedOrders'] ?? 0) - ($stats['shippedOrders'] ?? 0) - ($stats['deliveredOrders'] ?? 0) }}, // processing = confirmed - shipped - delivered (approx)
                    {{ $stats['shippedOrders'] ?? 0 }},
                    {{ $stats['deliveredOrders'] ?? 0 }},
                    {{ $stats['cancelledOrders'] ?? 0 }},
                    {{ $stats['returnedOrders'] ?? 0 }}
                ],
                backgroundColor: ['#FBBF24', '#60A5FA', '#818CF8', '#34D399', '#F87171', '#F97316'],
            }]
        };
        const salesOverviewData = {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], // Last 7 days
            datasets: [{
                label: 'Sales (₦)',
                data: [12000, 19000, 3000, 5000, 2000, 30000, 15000], // Example data
                backgroundColor: 'rgba(220, 38, 38, 0.6)', // einspot-red-600 with opacity
                borderColor: 'rgba(185, 28, 28, 1)', // einspot-red-700
                borderWidth: 1
            }]
        };

        // Order Status Chart (Pie)
        const orderStatusCtx = document.getElementById('orderStatusChart');
        if (orderStatusCtx && typeof Chart !== 'undefined') {
            new Chart(orderStatusCtx, {
                type: 'doughnut',
                data: orderStatusData,
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom'}}}
            });
        } else if(orderStatusCtx) {
            orderStatusCtx.parentElement.innerHTML = '<p class=\"text-gray-500 text-center py-10\">Chart.js not loaded. Please include it.</p>';
        }

        // Sales Overview Chart (Bar)
        const salesOverviewCtx = document.getElementById('salesOverviewChart');
        if (salesOverviewCtx && typeof Chart !== 'undefined') {
            new Chart(salesOverviewCtx, {
                type: 'bar',
                data: salesOverviewData,
                options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false }} }
            });
        } else if(salesOverviewCtx) {
            salesOverviewCtx.parentElement.innerHTML = '<p class=\"text-gray-500 text-center py-10\">Chart.js not loaded. Please include it.</p>';
        }

        // Sales period filter change
        const salesPeriodFilter = document.getElementById('salesPeriodFilter');
        if(salesPeriodFilter) {
            salesPeriodFilter.addEventListener('change', function() {
                // Construct URL with the new filter value and redirect
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('sales_period', this.value);
                window.location.href = currentUrl.toString();
            });
        }
    });
</script>
@endpush
