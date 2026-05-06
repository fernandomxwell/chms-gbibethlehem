<?php

namespace App\Services;

use App\Http\Requests\IndexCongregantServiceTypeRequest;
use App\Http\Requests\StoreCongregantServiceTypeRequest;
use App\Http\Requests\UpdateCongregantServiceTypeRequest;
use App\Models\Activity;
use App\Models\Congregant;
use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CongregantServiceTypeService
{
    private const EXPORT_COLUMNS = [
        'full_name',
        'can_serve_consecutively',
        'activity',
        'service_type'
    ];

    public function index(IndexCongregantServiceTypeRequest $request)
    {
        $validatedData = $request->validated();

        return Congregant::query()
            ->with([
                'serviceTypesPivot:id,congregant_id,service_type_id,activity_id',
                'serviceTypesPivot.activity:id,name,sort_order',
                'serviceTypesPivot.serviceType:id,name,sort_order',
            ])
            ->when($validatedData['search'] ?? null, function (Builder $query) use ($validatedData) {
                $query->has('serviceTypes')
                    ->where(function (Builder $query) use ($validatedData) {
                        $query->searchBy($validatedData)
                            ->orWhereHas('serviceTypesPivot.activity', function (Builder $query) use ($validatedData) {
                                $query->searchBy($validatedData);
                            })
                            ->orWhereHas('serviceTypes', function (Builder $query) use ($validatedData) {
                                $query->searchBy($validatedData);
                            });
                    });
            }, function (Builder $query) {
                $query->has('serviceTypes');
            })
            ->select([
                'id',
                'full_name',
                'can_serve_consecutively',
            ])
            ->orderBy('full_name')
            ->paginate()
            ->withQueryString();
    }

    public function create(StoreCongregantServiceTypeRequest $request)
    {
        $validatedData = $request->validated();

        $congregantId = $validatedData['congregant_id'];
        $canServeConsecutively = $validatedData['can_serve_consecutively'];
        $serviceTypes = $validatedData['service_types'] ?? [];

        $this->assign($congregantId, $canServeConsecutively, $serviceTypes);
    }

    public function update(UpdateCongregantServiceTypeRequest $request, int $congregantId)
    {
        $validatedData = $request->validated();

        $canServeConsecutively = $validatedData['can_serve_consecutively'];
        $serviceTypes = $validatedData['service_types'] ?? [];

        $this->assign($congregantId, $canServeConsecutively, $serviceTypes);
    }

    public function delete(int $congregantId)
    {
        $congregant = Congregant::findOrFail($congregantId, ['id']);
        $congregant->serviceTypes()->detach();
    }

    public function bulkDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::EXPORT_COLUMNS);

            Congregant::with([
                'serviceTypesPivot:id,congregant_id,service_type_id,activity_id',
                'serviceTypesPivot.activity:id,name,sort_order',
                'serviceTypesPivot.serviceType:id,name,sort_order',
            ])
                ->has('serviceTypes')
                ->orderBy('full_name')
                ->chunkById(500, function ($congregants) use ($handle) {
                    foreach ($congregants as $congregant) {
                        $pivots = $congregant->serviceTypesPivot
                            ->sortBy(fn($p) => $p->activity?->sort_order ?? PHP_INT_MAX);
                        foreach ($pivots as $pivot) {
                            fputcsv($handle, [
                                $congregant->full_name,
                                $congregant->can_serve_consecutively ? '1' : '0',
                                $pivot->activity?->name ?? '',
                                $pivot->serviceType?->name ?? '',
                            ]);
                        }
                    }
                });

            fclose($handle);
        }, 'pelayanan_jemaat_' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::EXPORT_COLUMNS);
            fputcsv($handle, ['Budi Santoso', '1', 'Ibadah Minggu', 'Worship Leader']);
            fputcsv($handle, ['Budi Santoso', '1', 'Ibadah Minggu', 'Pianist']);
            fputcsv($handle, ['Sari Dewi', '0', 'Ibadah Pemuda', 'Singer']);
            fclose($handle);
        }, 'template_pelayanan_jemaat.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function importCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);

        if (! $headers) {
            fclose($handle);

            return ['imported' => 0, 'failed' => 0, 'errors' => [__('congregant_services.import_empty_file')]];
        }

        if (array_map('trim', $headers) !== self::EXPORT_COLUMNS) {
            fclose($handle);

            return ['imported' => 0, 'failed' => 0, 'errors' => [__('congregant_services.import_invalid_headers')]];
        }

        // Parse all rows first, grouped by full_name
        $groups = [];
        $row = 1;
        $parseErrors = [];

        while (($values = fgetcsv($handle)) !== false) {
            $row++;

            if (count($values) !== 4) {
                $parseErrors[] = __('congregant_services.import_row_column_mismatch', ['row' => $row]);
                continue;
            }

            [$fullName, $canServe, $activityName, $serviceTypeName] = array_map('trim', $values);

            if ($fullName === '') {
                $parseErrors[] = __('congregant_services.import_row_empty_name', ['row' => $row]);
                continue;
            }

            if (! isset($groups[$fullName])) {
                $groups[$fullName] = [
                    'can_serve_consecutively' => in_array($canServe, ['1', 'true', 'ya'], true),
                    'assignments'             => [],
                ];
            }

            if ($activityName !== '' && $serviceTypeName !== '') {
                $groups[$fullName]['assignments'][] = [$activityName, $serviceTypeName];
            }
        }

        fclose($handle);

        $imported = 0;
        $failed   = 0;
        $errors   = array_merge($parseErrors, []);

        foreach ($groups as $fullName => $group) {
            // TODO: pencarian berdasarkan full_name rentan duplikat. Pertimbangkan
            // penambahan identifier unik (misal: nomor anggota / email) sebagai
            // kolom kunci pada CSV agar lookup lebih akurat.
            $congregant = Congregant::where('full_name', $fullName)->first(['id']);

            if (! $congregant) {
                $errors[] = __('congregant_services.import_congregant_not_found', ['name' => $fullName]);
                $failed++;
                continue;
            }

            $serviceTypesMap = [];
            $warnings        = [];

            foreach ($group['assignments'] as [$activityName, $serviceTypeName]) {
                $activity = Activity::where('name', $activityName)->first(['id']);

                if (! $activity) {
                    $warnings[] = __('congregant_services.import_activity_not_found', ['activity' => $activityName]);
                    continue;
                }

                $serviceType = ServiceType::where('name', $serviceTypeName)->first(['id']);

                if (! $serviceType) {
                    $warnings[] = __('congregant_services.import_service_type_not_found', ['service_type' => $serviceTypeName]);
                    continue;
                }

                $serviceTypesMap[$activity->id][] = $serviceType->id;
            }

            if (! empty($warnings)) {
                $errors[] = __('congregant_services.import_congregant_warning', [
                    'name'     => $fullName,
                    'warnings' => implode('; ', $warnings),
                ]);
            }

            $this->assign($congregant->id, $group['can_serve_consecutively'], $serviceTypesMap);
            $imported++;
        }

        return compact('imported', 'failed', 'errors');
    }

    protected function assign(int $congregantId, bool $canServeConsecutively, array $serviceTypes)
    {
        DB::transaction(function () use ($congregantId, $canServeConsecutively, $serviceTypes) {
            $congregant = Congregant::findOrFail($congregantId, ['id']);

            $congregant->update([
                'can_serve_consecutively' => $canServeConsecutively,
            ]);

            $congregant->serviceTypes()->detach();

            foreach ($serviceTypes as $activityId => $serviceTypeIds) {
                foreach ((array) $serviceTypeIds as $serviceTypeId) {
                    $congregant->serviceTypes()->attach($serviceTypeId, [
                        'activity_id' => $activityId,
                    ]);
                }
            }
        });
    }
}
