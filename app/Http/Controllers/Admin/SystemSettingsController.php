<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');

        // Ensure defaults if missing
        $defaults = [
            'company_name' => 'Natanem Engineering',
            'company_email' => 'info@natanem.com',
            'company_phone' => '+251 11 XXX XXXX',
            'company_address' => 'Addis Ababa, Ethiopia',
            'timezone' => 'Africa/Addis_Ababa',
            'date_format' => 'Y-m-d',
            'currency' => 'ETB',
            'items_per_page' => '15',
        ];

        foreach ($defaults as $key => $val) {
            if (! isset($settings[$key])) {
                $settings[$key] = $val;
            }
        }

        return view('admin.system_settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'timezone' => 'required|string|max:100',
            'date_format' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'items_per_page' => 'required|integer|min:5|max:100',
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.system-settings.index')->with('success', 'System configuration matrix successfully updated. New operational parameters are now active across all modules.');
    }
}
