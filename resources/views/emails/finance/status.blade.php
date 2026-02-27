@extends('emails.layout')

@section('content')
    <h2>Expense Request Update</h2>
    <p>Hello {{ $user->name }},</p>
    <p>Your expense request has been updated.</p>
    
    <div style="text-align: center; margin: 20px 0;">
        <span class="badge badge-{{ strtolower($expense->status) }}">
            {{ strtoupper($expense->status) }}
        </span>
    </div>

    <table class="info-table">
        <tr>
            <td>Request</td>
            <td>{{ $expense->title }}</td>
        </tr>
        <tr>
            <td>Amount</td>
            <td>ETB {{ number_format($expense->amount, 2) }}</td>
        </tr>
        @if($expense->rejection_reason)
        <tr>
            <td>Reason</td>
            <td>{{ $expense->rejection_reason }}</td>
        </tr>
        @endif
    </table>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
            <tr>
                <td align="center">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td> <a href="{{ route('finance.expenses.index') }}" target="_blank">View Expenses</a> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
