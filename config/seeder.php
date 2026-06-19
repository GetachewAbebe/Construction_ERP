<?php

/**
 * Seeder credentials configuration.
 *
 * Values are read from environment variables so they can be overridden
 * in production without changing code. Using config() here (rather than
 * env() directly in seeder files) keeps Larastan happy and ensures the
 * values survive config:cache.
 */
return [
    // Admin account
    'admin_email'    => env('ADMIN_DEFAULT_EMAIL', 'administrator@natanemengineering.com'),
    'admin_password' => env('ADMIN_DEFAULT_PASSWORD', 'AdminNatanem@123'),

    // HR Manager account
    'hr_email'    => env('HR_DEFAULT_EMAIL', 'humanresource@natanemengineering.com'),
    'hr_password' => env('HR_DEFAULT_PASSWORD', 'HumanResource@123'),

    // Inventory Manager account
    'inventory_email'    => env('INVENTORY_DEFAULT_EMAIL', 'inventorymanager@natanemengineering.com'),
    'inventory_password' => env('INVENTORY_DEFAULT_PASSWORD', 'InventoryManager@123'),

    // Finance Manager account
    'finance_email'    => env('FINANCE_DEFAULT_EMAIL', 'financialmanager@natanemengineering.com'),
    'finance_password' => env('FINANCE_DEFAULT_PASSWORD', 'FinancialManager@123'),
];
