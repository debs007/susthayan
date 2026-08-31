<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

/**
 * "Critical action performed -> User + time + action logged -> Audit trail
 * stored permanently -> Available for inspection" (SRS flow #12) - the
 * storing part has been happening since the schema pass (every model with
 * LogsActivity writes here automatically), but nothing let anyone actually
 * inspect it until now.
 */
class AuditLogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $activity = Activity::query()
            ->with('causer:id,name,mobile')
            ->when($request->filled('subject_type'), fn ($query) => $query->where('subject_type', $this->resolveSubjectClass($request->string('subject_type'))))
            ->when($request->filled('causer_id'), fn ($query) => $query->where('causer_id', $request->integer('causer_id')))
            ->when($request->filled('from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('to')))
            ->latest()
            ->paginate(50);

        return response()->json($activity);
    }

    public function show(Activity $activity): JsonResponse
    {
        return response()->json(['activity' => $activity->load('causer:id,name,mobile')]);
    }

    /** Accepts a short name ("Order") from the query string rather than requiring the caller to know/URL-encode the full App\Models\Order class string. */
    private function resolveSubjectClass(string $shortName): string
    {
        $class = 'App\\Models\\'.ucfirst($shortName);

        return class_exists($class) ? $class : $shortName;
    }
}
