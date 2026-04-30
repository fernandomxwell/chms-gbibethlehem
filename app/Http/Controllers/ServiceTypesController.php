<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\ImportServiceTypeRequest;
use App\Http\Requests\IndexServiceTypeRequest;
use App\Http\Requests\StoreServiceTypeRequest;
use App\Http\Requests\UpdateServiceTypeRequest;
use App\Models\Activity;
use App\Models\ServiceType;
use App\Services\ServiceTypeService;
use App\Traits\HandlesControllerErrors;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ServiceTypesController extends Controller implements HasMiddleware
{
    use HandlesControllerErrors;

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
                'edit',
                'importForm',
                'import',
            ]),
        ];
    }

    public function __construct(ServiceTypeService $serviceTypeService)
    {
        $this->serviceTypeService = $serviceTypeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexServiceTypeRequest $request)
    {
        try {
            $serviceTypes = $this->serviceTypeService->getPaginatedServiceTypes($request);

            return view('service_types.index', [
                'serviceTypes' => $serviceTypes,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $activities = Activity::orderBy('name')->get(['id', 'name']);

            return view('service_types.create', [
                'activities' => $activities,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceTypeRequest $request)
    {
        try {
            $this->serviceTypeService->create($request);

            return redirect()->route('service_types.index')
                ->with('success', __('service_types.success_create'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceType $serviceType)
    {
        try {
            $serviceType->load('activities');

            return view('service_types.show', [
                'serviceType' => $serviceType,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceType $serviceType)
    {
        try {
            $serviceType->load('activities');
            $activities = Activity::orderBy('name')->get(['id', 'name']);

            return view('service_types.edit', [
                'serviceType' => $serviceType,
                'activities' => $activities,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceTypeRequest $request, ServiceType $serviceType)
    {
        try {
            $this->serviceTypeService->update($request, $serviceType->id);

            return redirect()->route('service_types.index')
                ->with('success', __('service_types.success_update'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->serviceTypeService->delete($id);

            return redirect()->route('service_types.index')
                ->with('success', __('service_types.success_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    public function bulkDestroy(BulkDestroyRequest $request)
    {
        try {
            $this->serviceTypeService->bulkDelete($request->validated('ids'));

            return redirect()->route('service_types.index')
                ->with('success', __('service_types.success_bulk_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    public function export()
    {
        try {
            return $this->serviceTypeService->exportCsv();
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    public function downloadTemplate()
    {
        try {
            return $this->serviceTypeService->downloadTemplate();
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    public function importForm()
    {
        try {
            return view('service_types.import');
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }

    public function import(ImportServiceTypeRequest $request)
    {
        try {
            $result = $this->serviceTypeService->importCsv($request->file('file'));

            return view('service_types.import', ['result' => $result]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'service_types.index');
        }
    }
}
