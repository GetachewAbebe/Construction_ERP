@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form action="{{ $route }}" method="POST">
  @csrf
  @if($method !== 'POST') @method($method) @endif

  <div class="row">
    <div class="col-md-3">
      <div class="form-group">
        <label>Item No</label>
        <input type="text" name="item_no" class="form-control" value="{{ old('item_no', $item->item_no ?? '') }}">
      </div>
    </div>
    <div class="col-md-9">
      <div class="form-group">
        <label>Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required
               value="{{ old('name', $item->name ?? '') }}">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label>Description</label>
    <textarea name="description" class="form-control" rows="2">{{ old('description', $item->description ?? '') }}</textarea>
  </div>

  <div class="row">
    <div class="col-md-2">
      <div class="form-group">
        <label>Unit</label>
        <input type="text" name="unit" class="form-control" value="{{ old('unit', $item->unit ?? 'pcs') }}">
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <label>Qty</label>
        <input type="number" name="qty" class="form-control" min="0" value="{{ old('qty', $item->qty ?? 0) }}">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>Store Location</label>
        <input type="text" name="store_location" class="form-control"
               value="{{ old('store_location', $item->store_location ?? '') }}">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>Transfer Location</label>
        <input type="text" name="transfer_location" class="form-control"
               value="{{ old('transfer_location', $item->transfer_location ?? '') }}">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label>In Date</label>
        <input type="date" name="in_date" class="form-control"
               value="{{ old('in_date', optional($item->in_date ?? null)->format('Y-m-d')) }}">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>Out Date</label>
        <input type="date" name="out_date" class="form-control"
               value="{{ old('out_date', optional($item->out_date ?? null)->format('Y-m-d')) }}">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>Transfer Person</label>
        <input type="text" name="transfer_person" class="form-control"
               value="{{ old('transfer_person', $item->transfer_person ?? '') }}">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5">
      <div class="form-group">
        <label>Driver Name</label>
        <input type="text" name="driver_name" class="form-control"
               value="{{ old('driver_name', $item->driver_name ?? '') }}">
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label>Plate Number</label>
        <input type="text" name="plate_number" class="form-control"
               value="{{ old('plate_number', $item->plate_number ?? '') }}">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Approved By</label>
        <input type="text" name="approved_by" class="form-control"
               value="{{ old('approved_by', $item->approved_by ?? '') }}">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label>Remark</label>
    <textarea name="remark" class="form-control" rows="2">{{ old('remark', $item->remark ?? '') }}</textarea>
  </div>

  {{-- Optional fields row --}}
  <div class="row">
    <div class="col-md-3">
      <div class="form-group">
        <label>SKU</label>
        <input type="text" name="sku" class="form-control" value="{{ old('sku', $item->sku ?? '') }}">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" class="form-control" value="{{ old('category', $item->category ?? '') }}">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Purchase Price</label>
        <input type="number" step="0.01" min="0" name="purchase_price" class="form-control"
               value="{{ old('purchase_price', $item->purchase_price ?? '') }}">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Purchase Date</label>
        <input type="date" name="purchase_date" class="form-control"
               value="{{ old('purchase_date', optional($item->purchase_date ?? null)->format('Y-m-d')) }}">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label>Location (Alt.)</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $item->location ?? '') }}">
      </div>
    </div>
    <div class="col-md-5">
      <div class="form-group">
        <label>Supplier</label>
        <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $item->supplier ?? '') }}">
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
          @php
            $status = old('status', $item->status ?? 'active');
          @endphp
          <option value="active"   {{ $status==='active'?'selected':'' }}>active</option>
          <option value="inactive" {{ $status==='inactive'?'selected':'' }}>inactive</option>
          <option value="retired"  {{ $status==='retired'?'selected':'' }}>retired</option>
        </select>
      </div>
    </div>
  </div>

  <div class="submit-section">
    <button class="btn btn-primary submit-btn">{{ $submitText }}</button>
    <a href="{{ route('inventory.items.index') }}" class="btn btn-outline-secondary ml-2">Cancel</a>
  </div>
</form>
