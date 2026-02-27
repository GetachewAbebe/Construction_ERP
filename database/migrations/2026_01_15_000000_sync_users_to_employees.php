<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add user_id column if it doesn't exist
        if (! Schema::hasColumn('employees', 'user_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }

        // 2. Sync Users to Employees
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            // Check if employee exists by email
            $employee = Employee::where('email', $user->email)->first();

            if (! $employee) {
                // Split name
                $parts = explode(' ', $user->name, 2);
                $firstName = $parts[0];
                $lastName = $parts[1] ?? 'User';

                Employee::create([
                    'user_id' => $user->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    // 'name' => $user->name, // 'name' column doesn't exist in employees based on tinker output
                    'email' => $user->email,
                    'status' => 'Active',
                    'hire_date' => now(),
                    // Default values
                ]);
            } else {
                // Update user_id if missing
                if (! $employee->user_id) {
                    $employee->user_id = $user->id;
                    $employee->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('employees', 'user_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
