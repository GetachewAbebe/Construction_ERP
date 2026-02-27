@extends('emails.layout')

@section('content')
    <h2>New Expense Request</h2>
    <p>Hello Administrator,</p>
    <p>A new expense request has been submitted by <strong>{{ $user->name }}</strong>.</p>
    
    <table class="info-table">
        <tr>
            <td>Requested By</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>Title</td>
            <td>{{ $expense->title }}</td>
        </tr>
        <tr>
            <td>Amount</td>
            <td>ETB {{ number_format($expense->amount, 2) }}</td>
        </tr>
        <tr>
            <td>Project</td>
            <td>{{ $expense->project ? $expense->project->name : 'N/A' }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>{{ $expense->expense_date }}</td>
        </tr>
    </table>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
            <tr>
                <td align="center">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td> <a href="{{ route('admin.requests.finance') }}" target="_blank">Review Request</a> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
