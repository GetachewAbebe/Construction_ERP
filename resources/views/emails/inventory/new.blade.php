@extends('emails.layout')

@section('content')
    <h2>New Inventory Loan Request</h2>
    <p>Hello Administrator,</p>
    <p>A new inventory loan request has been submitted by <strong>{{ $user->name }}</strong>.</p>
    
    <table class="info-table">
        <tr>
            <td>Employee</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>Item</td>
            <td>{{ $loan->item ? $loan->item->name : 'Unknown Item' }}</td>
        </tr>
        <tr>
            <td>Quantity</td>
            <td>{{ $loan->quantity }}</td>
        </tr>
        <tr>
            <td>Expected Return</td>
            <td>{{ $loan->expected_return_date ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Purpose</td>
            <td>{{ $loan->notes }}</td>
        </tr>
    </table>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
            <tr>
                <td align="center">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td> <a href="{{ route('admin.requests.items') }}" target="_blank">Review Request</a> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
