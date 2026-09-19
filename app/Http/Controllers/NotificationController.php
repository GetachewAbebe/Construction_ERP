<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(15);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function open($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $data = $notification->data ?? [];
        $type = (string) ($data['type'] ?? '');
        $title = (string) ($data['title'] ?? '');
        $message = (string) ($data['message'] ?? '');
        $url = (string) ($data['url'] ?? '');

        // 1. Expense approval requests
        if ($type === 'expense_request' 
            || str_contains(strtolower($title), 'expense request') 
            || str_contains(strtolower($message), 'requires your approval')
            || (!empty($data['expense_id']) && str_contains(strtolower($message), 'expense'))) {
            return redirect()->route('admin.requests.finance');
        }

        // 2. Inventory loan / item requests
        if ($type === 'inventory_request' 
            || str_contains(strtolower($title), 'item request') 
            || str_contains(strtolower($type), 'loan')
            || !empty($data['loan_id'])) {
            return redirect()->route('admin.requests.items');
        }

        // 3. Leave requests
        if ($type === 'leave_request' 
            || str_contains(strtolower($title), 'leave request')
            || !empty($data['leave_id'])) {
            return redirect()->route('admin.requests.leave-approvals.index');
        }

        // 4. Low stock inventory alerts
        if ($type === 'inventory_low_stock' || str_contains(strtolower($title), 'stock')) {
            return redirect()->route('inventory.items.index');
        }

        // 5. If specific URL was provided
        if (!empty($url)) {
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                $path = parse_url($url, PHP_URL_PATH);
                $query = parse_url($url, PHP_URL_QUERY);
                $target = $path . ($query ? '?' . $query : '');
                return redirect($target ?: '/');
            }
            return redirect($url);
        }

        return redirect()->back();
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
