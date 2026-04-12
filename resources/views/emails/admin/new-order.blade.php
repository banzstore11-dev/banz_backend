@extends('emails.layouts.email')

@section('content')
    <h2>New Order Received</h2>
    <p>A new order has been placed and requires your attention.</p>

    <div class="info-box" style="background-color: #fef3c7; border-left-color: #f59e0b;">
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
        <p><strong>Status:</strong> <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>
        <p><strong>Total Amount:</strong> ${{ number_format($order->total, 2) }}</p>
    </div>

    <h3>Customer Information</h3>
    <div class="info-box">
        @if($order->user)
            <p><strong>Name:</strong> {{ $order->user->name }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
        @else
            <p><strong>Guest Order</strong></p>
            @if(is_array($order->shipping_address) && isset($order->shipping_address['email']))
            <p><strong>Email:</strong> {{ $order->shipping_address['email'] }}</p>
            @endif
        @endif
    </div>

    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->product_sku }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>${{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px;">
        <p><strong>Subtotal:</strong> ${{ number_format($order->subtotal, 2) }}</p>
        @if($order->tax > 0)
        <p><strong>Tax:</strong> ${{ number_format($order->tax, 2) }}</p>
        @endif
        @if($order->shipping_cost > 0)
        <p><strong>Shipping:</strong> ${{ number_format($order->shipping_cost, 2) }}</p>
        @endif
        @if($order->discount > 0)
        <p><strong>Discount:</strong> -${{ number_format($order->discount, 2) }}</p>
        @endif
        <p style="font-size: 18px; font-weight: bold; margin-top: 10px;">
            <strong>Total:</strong> ${{ number_format($order->total, 2) }}
        </p>
    </div>

    <h3>Shipping Address</h3>
    <div class="info-box">
        @if(is_array($order->shipping_address))
            <p><strong>{{ $order->shipping_address['name'] ?? 'N/A' }}</strong></p>
            <p>{{ $order->shipping_address['address'] ?? '' }}</p>
            <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
            <p>{{ $order->shipping_address['country'] ?? '' }}</p>
            @if(isset($order->shipping_address['phone']))
            <p><strong>Phone:</strong> {{ $order->shipping_address['phone'] }}</p>
            @endif
        @else
            <p>{{ $order->shipping_address }}</p>
        @endif
    </div>

    <h3>Payment Information</h3>
    <div class="info-box">
        <p><strong>Payment Method:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
        <p><strong>Payment Status:</strong> <span class="status-badge status-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span></p>
    </div>

    <p style="margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard/orders/{{ $order->id }}" class="button">View Order Details</a>
    </p>
@endsection
