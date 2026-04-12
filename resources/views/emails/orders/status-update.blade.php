@extends('emails.layouts.email')

@section('content')
    <h2>Order Status Update</h2>
    <p>Your order status has been updated.</p>

    <div class="info-box">
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Previous Status:</strong> <span class="status-badge status-{{ $oldStatus }}">{{ ucfirst($oldStatus) }}</span></p>
        <p><strong>Current Status:</strong> <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>
        <p><strong>Updated:</strong> {{ $order->updated_at->format('F d, Y h:i A') }}</p>
    </div>

    @if($order->status === 'processing')
        <p>Your order is now being processed. We'll notify you once it ships.</p>
    @elseif($order->status === 'cancelled')
        <p>Your order has been cancelled. If you have any questions, please contact our support team.</p>
    @elseif($order->status === 'refunded')
        <p>Your order has been refunded. The refund should appear in your account within 5-10 business days.</p>
    @endif

    <h3>Order Summary</h3>
    <table>
        <tbody>
            <tr>
                <td><strong>Total Amount:</strong></td>
                <td>${{ number_format($order->total, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Payment Status:</strong></td>
                <td>{{ ucfirst($order->payment_status) }}</td>
            </tr>
            <tr>
                <td><strong>Payment Method:</strong></td>
                <td>{{ $order->payment_method ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <p>If you have any questions about this update, please don't hesitate to contact us.</p>
@endsection
