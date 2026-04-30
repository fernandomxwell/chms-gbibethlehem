<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\ImportCongregantServiceTypeRequest;
use App\Http\Requests\IndexCongregantServiceTypeRequest;
use App\Http\Requests\StoreCongregantServiceTypeRequest;
use App\Http\Requests\UpdateCongregantServiceTypeRequest;
use App\Models\Activity;
use App\Models\Congregant;
use App\Models\ServiceType;
use App\Services\CongregantServiceTypeService;
use App\Traits\HandlesControllerErrors;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CongregantServiceTypeController extends Controller implements HasMiddleware
{
    use HandlesControllerErrors;

    private $congregantServiceTypeService;

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('navigation', only: [
                'index',
                'create',
                'edit',
                'importForm',
                'import',
            ]),
        ];
    }

    public function __construct(CongregantServiceTypeService $congregantServiceTypeService)
    {
        $this->congregantServiceTypeService = $congregantServiceTypeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexCongregantServiceTypeRequest $request)
    {
        try {
            $congregants = $this->congregantServiceTypeService->index($request);

            return view('congregant_service_types.index', [
                'congregants' => $congregants,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $activities = Activity::orderBy('name')->get(['id', 'name']);
            $serviceTypes = ServiceType::with('activities:id,name')->orderBy('name')->get(['id', 'name']);

            return view('congregant_service_types.create', [
                'activities' => $activities,
                'serviceTypes' => $serviceTypes,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCongregantServiceTypeRequest $request)
    {
        try {
            $this->congregantServiceTypeService->create($request);

            return redirect()->route('congregant_services.index')
                ->with('success', __('congregant_services.success_create'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $congregant = Congregant::query()
                ->with([
                    'serviceTypes:id,name',
                    'serviceTypesPivot:id,congregant_id,service_type_id,activity_id',
                    'serviceTypesPivot.activity:id,name',
                ])
                ->findOrFail($id, [
                    'id',
                    'full_name',
                    'can_serve_consecutively',
                ]);

            $activities = Activity::orderBy('name')->get(['id', 'name']);
            $serviceTypes = ServiceType::with('activities:id,name')->orderBy('name')->get(['id', 'name']);

            return view('congregant_service_types.edit', [
                'congregant' => $congregant,
                'activities' => $activities,
                'serviceTypes' => $serviceTypes,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCongregantServiceTypeRequest $request, $id)
    {
        try {
            $this->congregantServiceTypeService->update($request, $id);

            return redirect()->route('congregant_services.index')
                ->with('success', __('congregant_services.success_update'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->congregantServiceTypeService->delete($id);

            return redirect()->route('congregant_services.index')
                ->with('success', __('congregant_services.success_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    public function bulkDestroy(BulkDestroyRequest $request)
    {
        try {
            $this->congregantServiceTypeService->bulkDelete($request->validated('ids'));

            return redirect()->route('congregant_services.index')
                ->with('success', __('congregant_services.success_bulk_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    public function export()
    {
        try {
            return $this->congregantServiceTypeService->exportCsv();
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    public function downloadTemplate()
    {
        try {
            return $this->congregantServiceTypeService->downloadTemplate();
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    public function importForm()
    {
        try {
            return view('congregant_service_types.import');
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }

    public function import(ImportCongregantServiceTypeRequest $request)
    {
        try {
            $result = $this->congregantServiceTypeService->importCsv($request->file('file'));

            return view('congregant_service_types.import', ['result' => $result]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregant_services.index');
        }
    }
}
