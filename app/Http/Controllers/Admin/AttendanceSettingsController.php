<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class AttendanceSettingsController extends Controller
{
    public function index()
    {
        $settings = AttendanceSetting::all()->pluck('value', 'key');

        // Ensure defaults if missing (though they should be seeded)
        $defaults = [
            'shift_start_time' => '09:00',
            'shift_end_time' => '17:00',
            'grace_period_minutes' => '15',
        ];

        foreach ($defaults as $key => $val) {
            if (! isset($settings[$key])) {
                $settings[$key] = $val;
            }
        }

        return view('admin.attendance_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shift_start_time' => 'required|date_format:H:i',
            'shift_end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'required|integer|min:0|max:120',
        ]);

        foreach ($data as $key => $value) {
            AttendanceSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.attendance-settings.index')->with('success', 'Temporal configuration protocols successfully updated. New shift parameters are now active across all workforce monitoring systems.');
    }
}
