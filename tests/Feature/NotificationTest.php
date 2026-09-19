<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): User
    {
        Role::findOrCreate('Administrator', 'web');
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        return $user;
    }

    public function test_expense_request_notification_open_marks_as_read_and_redirects_to_finance_approval(): void
    {
        $admin = $this->actingAdmin();

        $notification = $admin->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\ExpenseStatusNotification',
            'data' => [
                'type' => 'expense_request',
                'title' => 'New Expense Request',
                'message' => 'A new expense of ETB 50,000.00 for project Main Living Building requires your approval.',
                'expense_id' => 123,
                'url' => route('admin.requests.finance'),
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('notifications.open', $notification->id));

        $response->assertRedirect(route('admin.requests.finance'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_legacy_expense_request_without_explicit_type_redirects_to_finance_approval(): void
    {
        $admin = $this->actingAdmin();

        $notification = $admin->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\ExpenseStatusNotification',
            'data' => [
                'title' => 'New Expense Request',
                'message' => 'A new expense of ETB 50,000.00 for project Main Living Building requires your approval.',
                'url' => 'https://erp.natanemengineering.com/finance/expenses/45',
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('notifications.open', $notification->id));

        $response->assertRedirect(route('admin.requests.finance'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_item_loan_request_notification_open_redirects_to_items_approval(): void
    {
        $admin = $this->actingAdmin();

        $notification = $admin->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\InventoryLoanStatusNotification',
            'data' => [
                'type' => 'inventory_request',
                'title' => 'New Item Request',
                'message' => 'John requested Hammer.',
                'loan_id' => 55,
                'url' => route('admin.requests.items'),
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('notifications.open', $notification->id));

        $response->assertRedirect(route('admin.requests.items'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_leave_request_notification_open_redirects_to_leave_approval(): void
    {
        $admin = $this->actingAdmin();

        $notification = $admin->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => 'App\Notifications\LeaveRequestStatusNotification',
            'data' => [
                'type' => 'leave_request',
                'title' => 'New Leave Request',
                'message' => 'Sarah requested leave.',
                'leave_id' => 10,
                'url' => route('admin.requests.leave-approvals.index'),
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('notifications.open', $notification->id));

        $response->assertRedirect(route('admin.requests.leave-approvals.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }
}
