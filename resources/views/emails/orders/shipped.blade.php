@extends('emails.layouts.email')

@section('content')
    <h2>Your Order Has Been Shipped! 🚚</h2>
    <p>Great news! Your order is on its way to you.</p>

    <div class="info-box">
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Shipped Date:</strong> {{ $order->shipped_at ? $order->shipped_at->format('F d, Y h:i A') : now()->format('F d, Y h:i A') }}</p>
        @if($trackingNumber)
        <p><strong>Tracking Number:</strong> {{ $trackingNumber }}</p>
        @endif
    </div>

    <h3>Shipping Address</h3>
    <div class="info-box">
        @if(is_array($order->shipping_address))
            <p><strong>{{ $order->shipping_address['name'] ?? 'N/A' }}</strong></p>
            <p>{{ $order->shipping_address['address'] ?? '' }}</p>
            <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
            <p>{{ $order->shipping_address['country'] ?? '' }}</p>
        @else
            <p>{{ $order->shipping_address }}</p>
        @endif
    </div>

    <h3>Items Shipped</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>You can expect to receive your order within the estimated delivery timeframe. We'll send you another notification when your order is delivered.</p>

    <p>Thank you for your purchase!</p>
@endsection
