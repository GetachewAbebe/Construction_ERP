@extends('emails.layout')

@section('content')
    <h2>Leave Request Update</h2>
    <p>Hello {{ $user->name }},</p>
    <p>The status of your leave request has been updated.</p>
    
    <div style="text-align: center; margin: 20px 0;">
        <span class="badge badge-{{ strtolower($leave->status) }}">
            {{ strtoupper($leave->status) }}
        </span>
    </div>

    <table class="info-table">
        <tr>
            <td>Leave Type</td>
            <td>{{ $leave->type }}</td>
        </tr>
        <tr>
            <td>Dates</td>
            <td>{{ $leave->start_date }} to {{ $leave->end_date }}</td>
        </tr>
        @if($leave->admin_remark)
        <tr>
            <td>Admin Remarks</td>
            <td>{{ $leave->admin_remark }}</td>
        </tr>
        @endif
    </table>

    <p>Please log in to your dashboard for more details.</p>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
            <tr>
                <td align="center">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td> <a href="{{ route('hr.leaves.index') }}" target="_blank">View Dashboard</a> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
