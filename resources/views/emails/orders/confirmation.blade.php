@extends('emails.layouts.email')

@section('content')
    <h2>Order Confirmation</h2>
    <p>Thank you for your order! We've received your order and it's being processed.</p>

    <div class="info-box">
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
        <p><strong>Status:</strong> <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>
    </div>

    <h3>Order Details</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Price</th>
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
            @if(isset($order->shipping_address['email']))
            <p><strong>Email:</strong> {{ $order->shipping_address['email'] }}</p>
            @endif
            @if(isset($order->shipping_address['phone']))
            <p><strong>Phone:</strong> {{ $order->shipping_address['phone'] }}</p>
            @endif
        @else
            <p>{{ $order->shipping_address }}</p>
        @endif
    </div>

    <p>We'll send you another email when your order ships. If you have any questions, please contact our support team.</p>

    <p>Thank you for shopping with us!</p>
@endsection
