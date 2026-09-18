<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::orderBy('type')->orderBy('name')->get();

        return \Inertia\Inertia::render('Admin/NotificationTemplates/Index', compact('templates'));
    }

    public function create()
    {
        return \Inertia\Inertia::render('Admin/NotificationTemplates/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:notification_templates,key|regex:/^[a-z0-9_]+$/',
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:email,notification,sms',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert comma-separated variables to array
        if (! empty($validated['variables'])) {
            $validated['variables'] = array_map('trim', explode(',', $validated['variables']));
        } else {
            $validated['variables'] = [];
        }

        $validated['is_active'] = $request->has('is_active');

        NotificationTemplate::create($validated);

        return redirect()->route('admin.notification-templates.index')
            ->with('success', "Communication template '{$validated['name']}' successfully registered in the messaging matrix.");
    }

    public function edit(NotificationTemplate $notificationTemplate)
    {
        return \Inertia\Inertia::render('Admin/NotificationTemplates/Edit', compact('notificationTemplate'));
    }

    public function update(Request $request, NotificationTemplate $notificationTemplate)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|regex:/^[a-z0-9_]+$/|unique:notification_templates,key,'.$notificationTemplate->id,
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:email,notification,sms',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert comma-separated variables to array
        if (! empty($validated['variables'])) {
            $validated['variables'] = array_map('trim', explode(',', $validated['variables']));
        } else {
            $validated['variables'] = [];
        }

        $validated['is_active'] = $request->has('is_active');

        $notificationTemplate->update($validated);

        return redirect()->route('admin.notification-templates.index')
            ->with('success', "Communication template '{$validated['name']}' successfully updated with new messaging parameters.");
    }

    public function destroy(NotificationTemplate $notificationTemplate)
    {
        $templateName = $notificationTemplate->name;
        $notificationTemplate->delete();

        return redirect()->route('admin.notification-templates.index')
            ->with('success', "Communication template '{$templateName}' has been expunged from the messaging matrix.");
    }

    public function preview(NotificationTemplate $notificationTemplate)
    {
        // Generate sample data for preview
        $sampleData = [];
        if (! empty($notificationTemplate->variables)) {
            foreach ($notificationTemplate->variables as $variable) {
                $sampleData[$variable] = '['.strtoupper($variable).'_VALUE]';
            }
        }

        $renderedBody = $notificationTemplate->render($sampleData);

        return \Inertia\Inertia::render('Admin/NotificationTemplates/Preview', compact('notificationTemplate', 'renderedBody', 'sampleData'));
    }
}
