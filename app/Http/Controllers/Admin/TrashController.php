<?php

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
    public function index()
    {
        $trashedItems = collect();

        // Collect trashed items from core models
        $models = [
            'User' => User::onlyTrashed()->get(),
            'Employee' => Employee::onlyTrashed()->get(),
            'Expense' => Expense::onlyTrashed()->get(),
            'InventoryItem' => InventoryItem::onlyTrashed()->get(),
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

        return view('admin.trash.index', compact('trashedItems'));
    }

    public function restore(Request $request)
    {
        $modelClass = $request->input('model');
        $id = $request->input('id');

        if (class_exists($modelClass)) {
            $item = $modelClass::withTrashed()->find($id);
            if ($item) {
                $item->restore();
                $resourceType = class_basename($modelClass);

                return redirect()->back()->with('success', "Asset restoration complete: {$resourceType} #{$id} has been successfully recovered from the vault.");
            }
        }

        return redirect()->back()->with('error', 'Critical Error: Unable to execute restoration protocol. The specified resource may no longer exist in the vault.');
    }
}
