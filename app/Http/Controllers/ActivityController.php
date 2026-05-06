<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\IndexActivityRequest;
use App\Http\Requests\ReorderActivityRequest;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Services\ActivityService;
use App\Traits\HandlesControllerErrors;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityController extends Controller implements HasMiddleware
{
    use HandlesControllerErrors;

    private $activityService;

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('navigation', only: [
                'index',
                'create',
                'show',
                'edit',
            ]),
        ];
    }

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexActivityRequest $request)
    {
        try {
            $activities = $this->activityService->getPaginatedActivities($request);

            $recurrenceSummaries = [];
            foreach ($activities as $activity) {
                $rruleData = parseRrule($activity->rrule ?? '');
                $recurrenceSummaries[$activity->id] = $this->activityService->generateRruleSummary($rruleData);
            }

            $activities->makeHidden(['rrule']);

            return view('activities.index', [
                'activities' => $activities,
                'recurrenceSummaries' => $recurrenceSummaries,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('activities.create');
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {
        try {
            $this->activityService->create($request);

            return redirect()->route('activities.index')
                ->with('success', __('activities.success_create'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        try {
            $rruleData = parseRrule($activity->rrule ?? '');
            $recurrenceSummary = $this->activityService->generateRruleSummary($rruleData);

            return view('activities.show', [
                'activity' => $activity,
                'recurrenceSummary' => $recurrenceSummary,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        try {
            $rruleData = parseRrule($activity->rrule ?? '');
            $recurrenceSummary = $this->activityService->generateRruleSummary($rruleData);

            return view('activities.edit', [
                'activity' => $activity,
                'recurrenceSummary' => $recurrenceSummary,
                ...$rruleData,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        try {
            $this->activityService->update($request, $activity->id);

            return redirect()->route('activities.index')
                ->with('success', __('activities.success_update'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->activityService->delete($id);

            return redirect()->route('activities.index')
                ->with('success', __('activities.success_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    public function reorder(ReorderActivityRequest $request)
    {
        try {
            $this->activityService->reorder($request->validated('ids'));

            return response()->json(['message' => __('activities.success_reorder')]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('error')], 500);
        }
    }

    public function bulkDestroy(BulkDestroyRequest $request)
    {
        try {
            $this->activityService->bulkDelete($request->validated('ids'));

            return redirect()->route('activities.index')
                ->with('success', __('activities.success_bulk_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'activities.index');
        }
    }

    /**
     * Handle AJAX request for activities (e.g., for select2 dropdowns).
     */
    public function ajax(Request $request)
    {
        try {
            $results = $this->activityService->getActivitiesForAjax($request);

            return response()->json([
                'items' => $results->map(fn($item) => [
                    'id' => $item->id,
                    'text' => $item->name,
                ]),
                'pagination' => [
                    'more' => $results->hasMorePages()
                ]
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
