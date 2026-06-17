<?php

declare(strict_types=1);

namespace App\Livewire\Inventory;

use App\Models\AssetClassification;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

/**
 * POC: reactive Inventory Items table with Mary UI modal CRUD.
 *
 * Mirrors InventoryItemController + StoreInventoryItemRequest:
 *  - create() logs an "Initial Entry" InventoryLog (like the controller's store()),
 *  - editing a quantity routes through InventoryService::adjustQuantity (like update()).
 */
#[Layout('components.layouts.poc')]
class ItemsTable extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public ?int $classificationId = null;

    // ---- Modal / form state ----
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $item_no = '';

    public string $name = '';

    public ?string $description = null;

    public ?int $classification_id = null;

    public string $unit_of_measurement = '';

    public int $quantity = 0;

    public string $store_location = '';

    public ?string $in_date = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedClassificationId(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'classificationId');
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'item_no' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'classification_id' => ['nullable', 'exists:asset_classifications,id'],
            'unit_of_measurement' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:0'],
            'store_location' => ['required', 'string', 'max:255'],
            'in_date' => ['required', 'date'],
        ];
    }

    public function create(): void
    {
        $this->reset([
            'editingId', 'item_no', 'name', 'description', 'classification_id',
            'unit_of_measurement', 'quantity', 'store_location', 'in_date',
        ]);
        $this->in_date = now()->toDateString();
        $this->resetValidation();
        $this->showForm = true;
    }

    public function editItem(int $id): void
    {
        $item = InventoryItem::findOrFail($id);

        $this->editingId = $item->id;
        $this->item_no = (string) $item->item_no;
        $this->name = (string) $item->name;
        $this->description = $item->description;
        $this->classification_id = $item->classification_id;
        $this->unit_of_measurement = (string) $item->unit_of_measurement;
        $this->quantity = (int) $item->quantity;
        $this->store_location = (string) $item->store_location;
        $this->in_date = optional($item->in_date)->toDateString();

        $this->resetValidation();
        $this->showForm = true;
    }

    public function save(InventoryService $service): void
    {
        $data = $this->validate();

        if ($this->editingId) {
            $item = InventoryItem::findOrFail($this->editingId);
            $newQuantity = (int) $data['quantity'];
            unset($data['quantity']); // quantity goes through the adjustment service

            DB::transaction(function () use ($item, $data, $newQuantity, $service) {
                $item->update($data);

                if ((int) $item->quantity !== $newQuantity) {
                    $service->adjustQuantity($item, $newQuantity, 'Manual Adjustment', 'Updated via items POC');
                }
            });

            $this->success('Inventory record updated.');
        } else {
            $exists = InventoryItem::where('item_no', $data['item_no'])
                ->where('store_location', $data['store_location'])
                ->exists();

            if ($exists) {
                $this->addError('item_no', 'An item with this ID already exists in this location.');

                return;
            }

            DB::transaction(function () use ($data) {
                $item = InventoryItem::create($data);

                InventoryLog::create([
                    'inventory_item_id' => $item->id,
                    'user_id' => auth()->id(),
                    'change_amount' => $item->quantity,
                    'previous_quantity' => 0,
                    'new_quantity' => $item->quantity,
                    'reason' => 'Initial Entry',
                    'remarks' => 'New inventory item created (POC)',
                ]);
            });

            $this->success('Inventory item added successfully.');
        }

        $this->showForm = false;
    }

    public function deleteItem(int $id): void
    {
        InventoryItem::findOrFail($id)->delete();
        $this->success('Item deleted.');
    }

    public function render(): View
    {
        $items = InventoryItem::query()
            ->with('classification')
            ->when($this->search !== '', function ($query) {
                $term = trim($this->search);
                $query->where(function ($sub) use ($term) {
                    $sub->where('item_no', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($this->classificationId, fn ($q) => $q->where('classification_id', $this->classificationId))
            ->when($this->status === 'in_stock', fn ($q) => $q->where('quantity', '>', 5))
            ->when($this->status === 'low_stock', fn ($q) => $q->where('quantity', '<=', 5)->where('quantity', '>', 0))
            ->when($this->status === 'out_of_stock', fn ($q) => $q->where('quantity', '<=', 0))
            ->latest('created_at')
            ->paginate(10);

        return view('livewire.inventory.items-table', [
            'items' => $items,
            'classifications' => AssetClassification::orderBy('name')->get(),
            'totals' => [
                'total' => InventoryItem::count(),
                'low' => InventoryItem::where('quantity', '>', 0)->where('quantity', '<=', 5)->count(),
                'out' => InventoryItem::where('quantity', '<=', 0)->count(),
            ],
        ]);
    }
}
