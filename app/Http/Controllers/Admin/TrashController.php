<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\LeaveRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /**
     * Whitelist of models permitted for restoration from the vault.
     */
    private const ALLOWED_MODELS = [
        User::class,
        Employee::class,
        Expense::class,
        InventoryItem::class,
        \App\Models\InventoryLoan::class,
        Project::class,
        LeaveRequest::class,
    ];

    public function index()
    {
        $trashedItems = collect();

        // Collect trashed items from core models
        $models = [
            'User' => User::onlyTrashed()->get(),
            'Employee' => Employee::onlyTrashed()->get(),
            'Expense' => Expense::onlyTrashed()->get(),
            'InventoryItem' => InventoryItem::onlyTrashed()->get(),
            'InventoryLoan' => \App\Models\InventoryLoan::onlyTrashed()->get(),
            'Project' => Project::onlyTrashed()->get(),
            'LeaveRequest' => LeaveRequest::onlyTrashed()->get(),
        ];

        foreach ($models as $type => $collection) {
            foreach ($collection as $item) {
                $trashedItems->push([
                    'id' => $item->id,
                    'type' => $type,
                    'name' => $item->name ?? $item->email ?? $item->id,
                    'deleted_at' => $item->deleted_at,
                    'model' => get_class($item),
                ]);
            }
        }

        $trashedItems = $trashedItems->sortByDesc('deleted_at');

        return \Inertia\Inertia::render('Admin/Trash/Index', [
            'trashedItems' => $trashedItems->values(),
        ]);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'model' => ['required', 'string'],
            'id' => ['required', 'integer'],
        ]);

        $modelClass = (string) $request->input('model');
        $id = (int) $request->input('id');

        if (! in_array($modelClass, self::ALLOWED_MODELS, true) || ! class_exists($modelClass)) {
            return redirect()->back()->with('error', 'Security Alert: Unauthorized or invalid model type for restoration.');
        }

        $item = $modelClass::withTrashed()->find($id);
        if ($item) {
            $item->restore();
            $resourceType = class_basename($modelClass);

            return redirect()->back()->with('success', "Asset restoration complete: {$resourceType} #{$id} has been successfully recovered from the vault.");
        }

        return redirect()->back()->with('error', 'Critical Error: Unable to execute restoration protocol. The specified resource may no longer exist in the vault.');
    }
}
