@extends('emails.layout')

@section('content')
    <h2>Inventory Request Update</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Your inventory loan request status has been updated.</p>
    
    <div style="text-align: center; margin: 20px 0;">
        <span class="badge badge-{{ strtolower($loan->status) }}">
            {{ strtoupper($loan->status) }}
        </span>
    </div>

    <table class="info-table">
        <tr>
            <td>Item</td>
            <td>{{ $loan->item ? $loan->item->name : 'Unknown Item' }}</td>
        </tr>
        <tr>
            <td>Quantity</td>
            <td>{{ $loan->quantity }}</td>
        </tr>
    </table>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
            <tr>
                <td align="center">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td> <a href="{{ route('inventory.loans.index') }}" target="_blank">View Loans</a> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
