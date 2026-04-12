@extends('emails.layouts.email')

@section('content')
    <h2>New User Registration</h2>
    <p>A new user has registered on your store.</p>

    <div class="info-box" style="background-color: #dbeafe; border-left-color: #2563eb;">
        <p><strong>Registration Date:</strong> {{ $user->created_at->format('F d, Y h:i A') }}</p>
    </div>

    <h3>User Information</h3>
    <table>
        <tbody>
            <tr>
                <td><strong>Name:</strong></td>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <td><strong>User ID:</strong></td>
                <td>#{{ $user->id }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="button">View Dashboard</a>
    </p>
@endsection
