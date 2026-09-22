<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Show the Global Settings Page.
     */
    public function index()
    {
        // Load settings to pre-fill the form
        $settings = Setting::pluck('value', 'key')->toArray();

        // Pass settings individually or as a block
        $editWindowHours = $settings['quotation_edit_window_hours'] ?? 3;
        
        // Emergency Notice settings
        $emergencyEnabled = ($settings['emergency_enabled'] ?? '0') === '1';
        $emergencyMessage1 = $settings['emergency_message_1'] ?? '';
        $emergencyMessage2 = $settings['emergency_message_2'] ?? '';

        return view('admin.settings', compact('editWindowHours', 'emergencyEnabled', 'emergencyMessage1', 'emergencyMessage2'));
    }

    /**
     * Update the Global Settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'quotation_edit_window_hours' => 'required|integer|min:0',
            'emergency_message_1' => 'nullable|string|max:500',
            'emergency_message_2' => 'nullable|string|max:500',
        ]);

        // Save each item individually
        Setting::updateOrCreate(
            ['key' => 'quotation_edit_window_hours'],
            ['value' => $request->quotation_edit_window_hours]
        );
        
        // Emergency notice settings
        Setting::updateOrCreate(
            ['key' => 'emergency_enabled'],
            ['value' => $request->has('emergency_enabled') ? '1' : '0']
        );
        
        Setting::updateOrCreate(
            ['key' => 'emergency_message_1'],
            ['value' => $request->emergency_message_1 ?? '']
        );
        
        Setting::updateOrCreate(
            ['key' => 'emergency_message_2'],
            ['value' => $request->emergency_message_2 ?? '']
        );

        return redirect()->back()->with('success', 'Global Settings have been updated successfully.');
    }
}
