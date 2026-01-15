@extends('layouts.app')
@section('title','Approved Leaves')

@section('content')
<h1 class="text-2xl font-bold text-slate-900">Approved Leaves</h1>

<div class="mt-4 overflow-x-auto rounded-2xl border bg-white">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
            <tr>
                <th class="px-4 py-3 text-left">Employee</th>
                <th class="px-4 py-3 text-left">Start</th>
                <th class="px-4 py-3 text-left">End</th>
                <th class="px-4 py-3 text-left">Approved By</th>
                <th class="px-4 py-3 text-left">Approved At</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($approved as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3">
                        {{ optional($row->employee)->first_name }} {{ optional($row->employee)->last_name }}
                    </td>
                    <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($row->start_date)->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($row->end_date)->format('Y-m-d') }}</td>
                    <td class="px-4 py-3">
                        {{ optional($row->approver)->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        {{ optional($row->approved_at)->format('Y-m-d H:i') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">No approved leaves yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $approved->links() }}</div>
@endsection
