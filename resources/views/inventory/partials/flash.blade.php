{{-- resources/views/partials/flash.blade.php --}}

@if (session('status'))
    <div class="alert alert-success mb-3">
        {{ session('status') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success mb-3">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mb-3">
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning mb-3">
        {{ session('warning') }}
    </div>
@endif
