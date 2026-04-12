@extends('emails.layouts.email')

@section('content')
    <h2>Product Activity Notification</h2>
    <p>A product has been {{ $action }} in your store.</p>

    <div class="info-box" style="background-color: {{ $action === 'deleted' ? '#fee2e2' : '#dbeafe' }}; border-left-color: {{ $action === 'deleted' ? '#dc2626' : '#2563eb' }};">
        <p><strong>Action:</strong> {{ ucfirst($action) }}</p>
        <p><strong>Date:</strong> {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <h3>Product Information</h3>
    <table>
        <tbody>
            <tr>
                <td><strong>Product Name:</strong></td>
                <td>{{ $product->name }}</td>
            </tr>
            <tr>
                <td><strong>SKU:</strong></td>
                <td>{{ $product->sku }}</td>
            </tr>
            <tr>
                <td><strong>Category:</strong></td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
            </tr>
            @if($action !== 'deleted')
            <tr>
                <td><strong>Retail Price:</strong></td>
                <td>${{ number_format($product->retail_price, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Stock Quantity:</strong></td>
                <td>{{ $product->stock_quantity }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
            <tr>
                <td><strong>Featured:</strong></td>
                <td>{{ $product->is_featured ? 'Yes' : 'No' }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    @if($action !== 'deleted')
    <p style="margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard/products/{{ $product->id }}/edit" class="button">View Product</a>
    </p>
    @endif
@endsection
