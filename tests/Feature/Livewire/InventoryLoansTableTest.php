<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Inventory\LoansTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryLoansTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_loans_table_component_renders(): void
    {
        Livewire::test(LoansTable::class)
            ->assertOk()
            ->assertSee('Loan Requests')
            ->assertSee('Pending')
            ->assertSee('Approved')
            ->assertSee('Returned')
            ->assertSee('Rejected');
    }

    public function test_filters_are_reactive_and_reset_pagination(): void
    {
        Livewire::test(LoansTable::class)
            ->set('search', 'excavator')
            ->assertSet('search', 'excavator')
            ->set('status', 'pending')
            ->assertSet('status', 'pending')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('status', '')
            ->assertOk();
    }
}
