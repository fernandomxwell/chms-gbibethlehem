<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDestroyRequest;
use App\Http\Requests\ImportCongregantRequest;
use App\Http\Requests\IndexCongregantRequest;
use App\Http\Requests\StoreCongregantRequest;
use App\Http\Requests\UpdateCongregantRequest;
use App\Models\Congregant;
use App\Services\CongregantService;
use App\Traits\HandlesControllerErrors;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CongregantController extends Controller implements HasMiddleware
{
    use HandlesControllerErrors;

    private $congregantService;

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
            ]),
        ];
    }

    public function __construct(CongregantService $congregantService)
    {
        $this->congregantService = $congregantService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexCongregantRequest $request)
    {
        try {
            $congregants = $this->congregantService->getPaginatedCongregants($request);

            return view('congregants.index', [
                'congregants' => $congregants,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('congregants.create');
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCongregantRequest $request)
    {
        try {
            $this->congregantService->create($request);

            return redirect()->route('congregants.index')
                ->with('success', __('congregants.success_create'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Congregant $congregant)
    {
        try {
            return view('congregants.show', [
                'congregant' => $congregant,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Congregant $congregant)
    {
        try {
            return view('congregants.edit', [
                'congregant' => $congregant,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCongregantRequest $request, Congregant $congregant)
    {
        try {
            $this->congregantService->update($request, $congregant->id);

            return redirect()->route('congregants.index')
                ->with('success', __('congregants.success_update'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->congregantService->delete($id);

            return redirect()->route('congregants.index')
                ->with('success', __('congregants.success_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    public function bulkDestroy(BulkDestroyRequest $request)
    {
        try {
            $this->congregantService->bulkDelete($request->validated('ids'));

            return redirect()->route('congregants.index')
                ->with('success', __('congregants.success_bulk_delete'));
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Export all congregants as CSV.
     */
    public function export()
    {
        try {
            return $this->congregantService->exportCsv();
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Download a blank CSV import template.
     */
    public function downloadTemplate()
    {
        try {
            return $this->congregantService->downloadTemplate();
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Show the import form.
     */
    public function importForm()
    {
        try {
            return view('congregants.import');
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Process the uploaded CSV and import congregants.
     */
    public function import(ImportCongregantRequest $request)
    {
        try {
            $result = $this->congregantService->importCsv($request->file('file'));

            return view('congregants.import', ['result' => $result]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'congregants.index');
        }
    }

    /**
     * Handle AJAX request for congregants (e.g., for select2 dropdowns).
     */
    public function ajax(Request $request)
    {
        try {
            $results = $this->congregantService->getCongregantsForAjax($request);

            return response()->json([
                'items' => $results->map(fn($item) => [
                    'id' => $item->id,
                    'text' => $item->full_name,
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
