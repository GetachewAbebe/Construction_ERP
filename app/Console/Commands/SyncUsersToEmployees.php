<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;

class SyncUsersToEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:users-to-employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure every User has a corresponding Employee record linked.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $count = 0;
        $linked = 0;

        $this->info("Found {$users->count()} users. Starting sync...");

        foreach ($users as $user) {
            // Try to find employee by user_id first, then email
            $employee = Employee::where('user_id', $user->id)
                                ->orWhere('email', $user->email)
                                ->first();

            if (!$employee) {
                $this->info("Creating employee for user: {$user->email}");
                
                // Handle name components
                // If user has direct columns, use them. If not, split the accessor 'name'.
                $firstName = $user->first_name;
                $lastName = $user->last_name;

                if (empty($firstName)) {
                    // Fallback to splitting name if columns are empty but name accessor works
                    $parts = explode(' ', $user->name, 2);
                    $firstName = $parts[0];
                    $lastName = $parts[1] ?? 'User';
                }

                Employee::create([
                    'user_id'    => $user->id,
                    'first_name' => $firstName,
                    'last_name'  => $lastName ?: 'User',
                    'email'      => $user->email,
                    'status'     => $user->status ?? 'Active',
                    'hire_date'  => now(),
                    'phone'      => $user->phone_number,
                    'position'   => $user->position,
                    'department' => $user->department,
                ]);
                $count++;
            } else {
                // Ensure user_id is set if it was found by email
                if ($employee->user_id !== $user->id) {
                    $employee->update(['user_id' => $user->id]);
                    $linked++;
                    $this->comment("Linked existing employee to user: {$user->email}");
                }
            }
        }

        $this->info("Sync Complete.");
        $this->info("Created: $count");
        $this->info("Linked: $linked");
    }
}
