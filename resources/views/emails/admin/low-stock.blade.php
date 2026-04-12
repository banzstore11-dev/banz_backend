@extends('emails.layouts.email')

@section('content')
    <h2>Low Stock Alert ⚠️</h2>
    <p>A product is running low on stock and may need to be restocked soon.</p>

    <div class="info-box" style="background-color: #fee2e2; border-left-color: #dc2626;">
        <p><strong>Product:</strong> {{ $product->name }}</p>
        <p><strong>Current Stock:</strong> {{ $product->stock_quantity }} units</p>
        <p><strong>Threshold:</strong> {{ $threshold }} units</p>
        <p><strong>SKU:</strong> {{ $product->sku }}</p>
    </div>

    <h3>Product Details</h3>
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
            <tr>
                <td><strong>Current Stock:</strong></td>
                <td><strong style="color: #dc2626;">{{ $product->stock_quantity }} units</strong></td>
            </tr>
            <tr>
                <td><strong>Retail Price:</strong></td>
                <td>${{ number_format($product->retail_price, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard/products/{{ $product->id }}/edit" class="button">Update Stock</a>
    </p>

    <p>Please consider restocking this product to avoid running out of inventory.</p>
@endsection
