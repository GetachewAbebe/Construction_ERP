<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\InventoryLoan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryLoanApproved
{
    use Dispatchable, SerializesModels;

    public function __construct(public InventoryLoan $loan)
    {
    }
}
