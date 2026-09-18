<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | When an inventory item's stock reaches or falls below this number,
    | the system will automatically trigger low-stock alerts and notifications
    | for administrators and inventory managers.
    |
    */
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 5),
];
