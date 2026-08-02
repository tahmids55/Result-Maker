<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display the activity history dashboard.
     */
    public function index(Request $request)
    {
        $ownerId = auth()->user()->owner_id;
        
        // Scope to users belonging to this school/tenant
        $tenantUserIds = \App\Models\User::where('id', $ownerId)
            ->orWhere('admin_id', $ownerId)
            ->pluck('id');

        $query = Activity::with('causer')
            ->whereIn('causer_id', $tenantUserIds)
            ->latest();

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->where('subject_type', $request->model);
        }

        // Filter by user (causer)
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $activities = $query->paginate(25)->withQueryString();

        // Get unique model types for the filter dropdown (scoped to tenant activities)
        $modelTypes = Activity::whereIn('causer_id', $tenantUserIds)
            ->select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(fn($type) => [
                'full' => $type,
                'short' => class_basename($type),
            ]);

        // Get unique causers (users) for the filter dropdown scoped to tenant
        $users = \App\Models\User::whereIn('id', $tenantUserIds)
            ->select('id', 'name', 'role')
            ->orderBy('name')
            ->get();

        return view('activity-log.index', compact('activities', 'modelTypes', 'users'));
    }
}
