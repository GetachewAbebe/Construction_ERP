@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Edit Item</h4>
    <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <strong>Fix the following:</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('inventory.items.update', $item) }}">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Item No <span class="text-danger">*</span></label>
            <input type="text" name="item_no" class="form-control" value="{{ old('item_no', $item->item_no) }}" required>
          </div>

          <div class="col-md-8">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control">{{ old('description', $item->description) }}</textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Unit of Measurement <span class="text-danger">*</span></label>
            <input type="text" name="unit_of_measurement" class="form-control" value="{{ old('unit_of_measurement', $item->unit_of_measurement) }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity) }}" min="0" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Store Location <span class="text-danger">*</span></label>
            <input type="text" name="store_location" class="form-control" value="{{ old('store_location', $item->store_location) }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">In Date <span class="text-danger">*</span></label>
            <input type="date" name="in_date" class="form-control" value="{{ old('in_date', optional($item->in_date)->format('Y-m-d')) }}" required>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-primary">Update Item</button>
          <a href="{{ route('inventory.items.index') }}" class="btn btn-light">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
