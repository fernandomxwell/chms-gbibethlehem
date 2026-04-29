<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\IndexScheduleRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Services\ScheduleService;
use App\Services\ServiceTypeService;
use App\Traits\HandlesControllerErrors;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ScheduleController extends Controller implements HasMiddleware
{
    use HandlesControllerErrors;

    private $scheduleService;

    private $serviceTypeService;

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
            ]),
        ];
    }

    public function __construct(ScheduleService $scheduleService, ServiceTypeService $serviceTypeService)
    {
        $this->scheduleService = $scheduleService;
        $this->serviceTypeService = $serviceTypeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexScheduleRequest $request)
    {
        try {
            $scheduleGroups = $this->scheduleService->getPaginatedScheduleGroup($request);

            $scheduleGroups->makeHidden(['activity_id']);

            return view('schedules.index', [
                'scheduleGroups' => $scheduleGroups,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'schedules.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $serviceTypes = $this->serviceTypeService->getAll([
                'id',
                'name',
            ], ['activities:id,name']);

            return view('schedules.create', [
                'serviceTypes' => $serviceTypes,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'schedules.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        try {
            $this->scheduleService->create($request);

            return redirect()
                ->route('schedules.index')
                ->with('success',  __('schedules.success_create'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'schedules.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $scheduleGroup = $this->scheduleService->show($id);

            return view('schedules.show', $scheduleGroup);
        } catch (\Exception $e) {
            return $this->handleException($e, 'schedules.index');
        }
    }

    public function export(string $id)
    {
        try {
            $data = $this->scheduleService->export($id);

            $filename = $data['filename'] . '.csv';

            $responseHeaders = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($data) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $data['headers']);
                foreach ($data['rows'] as $row) {
                    fputcsv($handle, $row);
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $responseHeaders);
        } catch (\Exception $e) {
            return $this->handleException($e, 'schedules.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $this->scheduleService->delete($id);

            return redirect()
                ->route('schedules.index')
                ->with('success',  __('schedules.success_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'schedules.index');
        }
    }

    public function bulkDestroy(BulkDestroyRequest $request)
    {
        try {
            $this->scheduleService->bulkDelete($request->validated('ids'));

            return redirect()->route('schedules.index')
                ->with('success', __('schedules.success_bulk_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'schedules.index');
        }
    }
}
