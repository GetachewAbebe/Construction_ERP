@extends('emails.layout')

@section('content')
    <h2>New Leave Request</h2>
    <p>Hello Administrator,</p>
    <p>A new leave request has been submitted by <strong>{{ $user->name }}</strong> and requires your attention.</p>
    
    <table class="info-table">
        <tr>
            <td>Employee</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>Leave Type</td>
            <td>{{ $leave->type }}</td>
        </tr>
        <tr>
            <td>Dates</td>
            <td>{{ $leave->start_date }} to {{ $leave->end_date }}</td>
        </tr>
        <tr>
            <td>Duration</td>
            <td>{{ $leave->days }} days</td>
        </tr>
        <tr>
            <td>Reason</td>
            <td>{{ $leave->reason }}</td>
        </tr>
    </table>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
            <tr>
                <td align="center">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td> <a href="{{ route('admin.requests.leave-approvals.index') }}" target="_blank">Review Request</a> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
