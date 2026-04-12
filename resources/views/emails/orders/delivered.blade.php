@extends('emails.layouts.email')

@section('content')
    <h2>Order Delivered! ✅</h2>
    <p>Your order has been successfully delivered!</p>

    <div class="info-box">
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Delivered Date:</strong> {{ $order->delivered_at ? $order->delivered_at->format('F d, Y h:i A') : now()->format('F d, Y h:i A') }}</p>
    </div>

    <h3>Delivered Items</h3>
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

    <p>We hope you're happy with your purchase! If you have any questions or concerns, please don't hesitate to contact our support team.</p>

    <p>Thank you for shopping with us. We look forward to serving you again!</p>
@endsection
