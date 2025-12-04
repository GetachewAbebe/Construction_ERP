@extends('layouts.app') {{-- or your actual layout --}}

@section('content')
<div class="container py-4" style="max-width: 900px;">

  {{-- Flash messages (in case of error) --}}
  @includeWhen(session('status') || session('error'), 'partials.flash')

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h3 class="mb-0">Add Inventory Item</h3>
      <small class="text-muted">Register a new item</small>
    </div>
    <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary">Back</a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('inventory.items.store') }}" class="row g-3">
        @csrf

        <div class="col-md-4">
          <label class="form-label">Item No <span class="text-danger">*</span></label>
          <input type="text" name="item_no" value="{{ old('item_no') }}" class="form-control @error('item_no') is-invalid @enderror">
          @error('item_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label">Name <span class="text-danger">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Electrical Mixer 750 liter">
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" rows="2" class="form-control @error('description') is-invalid @enderror" placeholder="Optional short description">{{ old('description') }}</textarea>
          @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">Unit of Measurement <span class="text-danger">*</span></label>
          <input type="text" name="unit_of_measurement" value="{{ old('unit_of_measurement') }}" class="form-control @error('unit_of_measurement') is-invalid @enderror" placeholder="e.g. pcs / m / box">
          @error('unit_of_measurement') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">Quantity <span class="text-danger">*</span></label>
          <input type="number" min="0" name="quantity" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror">
          @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">Store Location <span class="text-danger">*</span></label>
          <input type="text" name="store_location" value="{{ old('store_location') }}" class="form-control @error('store_location') is-invalid @enderror" placeholder="e.g. Waserbi site">
          @error('store_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">In Date <span class="text-danger">*</span></label>
          <input type="date" name="in_date" value="{{ old('in_date') }}" class="form-control @error('in_date') is-invalid @enderror">
          @error('in_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary"
                  onclick="this.disabled=true; this.innerText='Saving...'; this.form.submit();">
            Save Item
          </button>
          <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
