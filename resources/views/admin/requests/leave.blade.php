@extends('layouts.app')
@section('title','Leave Requests')

@section('content')
<div class="gap-4" style="display:flex; align-items:flex-start;">
    <div style="flex:0 0 10%; min-width:140px; max-width:240px;">
        @include('admin.partials.sidebar')
    </div>

    <section class="rounded-2xl border border-white/70 bg-white/90 backdrop-blur-xl shadow-md p-6"
             style="flex:1 1 auto; min-height:60vh;">
        <h1 class="text-xl font-bold text-slate-800">Leave Requests</h1>
        <p class="mt-2 text-slate-600">List and process leave requests here.</p>
        {{-- TODO: table --}}
    </section>
</div>
@endsection
