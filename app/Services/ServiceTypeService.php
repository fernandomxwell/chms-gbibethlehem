<?php

namespace App\Services;

use App\Http\Requests\IndexServiceTypeRequest;
use App\Http\Requests\StoreServiceTypeRequest;
use App\Http\Requests\UpdateServiceTypeRequest;
use Illuminate\Validation\Rules\Unique;
use App\Models\Activity;
use App\Models\ServiceType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceTypeService
{
    private const EXPORT_COLUMNS = [
        'name',
        'description',
        'activities',
    ];

    public function getPaginatedServiceTypes(IndexServiceTypeRequest $request)
    {
        $validatedData = $request->validated();

        return ServiceType::query()
            ->with('activities:name')
            ->when($validatedData['activity'] ?? null, function ($query) use ($validatedData) {
                $query->whereHas('activities', function ($query) use ($validatedData) {
                    $query->where('activities.id', $validatedData['activity']);
                });
            })
            ->when($validatedData['search'] ?? null, function ($query) use ($validatedData) {
                $query->searchBy($validatedData)
                    ->orWhereHas('activities', function ($query) use ($validatedData) {
                        $query->searchBy($validatedData);
                    });
            })
            ->select([
                'id',
                'name',
            ])
            ->paginate()
            ->withQueryString();
    }

    public function create(StoreServiceTypeRequest $request)
    {
        $data = $request->validated();
        $activityIds = $data['activities'] ?? [];
        unset($data['activities']);

        $serviceType = ServiceType::withTrashed()
            ->where('name', $data['name'])
            ->first();

        DB::transaction(function () use ($data, $activityIds, &$serviceType) {
            if ($serviceType) {
                if ($serviceType->trashed()) {
                    $serviceType->restore();
                }

                $serviceType->fill($data)->save();
            } else {
                $serviceType = ServiceType::create($data);
            }

            $serviceType->activities()->sync($activityIds);
        });

        return $serviceType;
    }

    public function update(UpdateServiceTypeRequest $request, int $id)
    {
        $serviceType = ServiceType::findOrFail($id, ['id']);

        $data = $request->validated();
        $activityIds = $data['activities'] ?? [];
        unset($data['activities']);

        DB::transaction(function () use ($data, $activityIds, $serviceType) {
            $serviceType->update($data);
            $serviceType->activities()->sync($activityIds);
        });

        return $serviceType;
    }

    public function delete(int $id)
    {
        $serviceType = ServiceType::findOrFail($id, ['id']);

        DB::transaction(function () use ($serviceType) {
            $serviceType->activities()->detach();
            $serviceType->delete();
        });
    }

    public function bulkDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
    }

    public function getAll($attributes = ['*'], array $relations = [])
    {
        return ServiceType::select($attributes)->with($relations)->get();
    }

    public function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::EXPORT_COLUMNS);

            ServiceType::with('activities:id,name')
                ->orderBy('name')
                ->chunkById(500, function ($serviceTypes) use ($handle) {
                    foreach ($serviceTypes as $serviceType) {
                        $activities = $serviceType->activities->pluck('name')->implode(';');
                        fputcsv($handle, [
                            $serviceType->name,
                            $serviceType->description ?? '',
                            $activities,
                        ]);
                    }
                });

            fclose($handle);
        }, 'jenis_pelayanan_' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function downloadTemplate(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, self::EXPORT_COLUMNS);
            fputcsv($handle, ['Worship Leader', 'Memimpin pujian', 'Ibadah Minggu;Ibadah Pemuda']);
            fclose($handle);
        }, 'template_jenis_pelayanan.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function importCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);

        if (! $headers) {
            fclose($handle);

            return ['imported' => 0, 'failed' => 0, 'errors' => [__('service_types.import_empty_file')]];
        }

        if (array_map('trim', $headers) !== self::EXPORT_COLUMNS) {
            fclose($handle);

            return ['imported' => 0, 'failed' => 0, 'errors' => [__('service_types.import_invalid_headers')]];
        }

        $storeRules = (new StoreServiceTypeRequest())->rules();
        $rules = [
            'name'        => array_values(array_filter($storeRules['name'], fn($r) => ! ($r instanceof Unique))),
            'description' => $storeRules['description'],
        ];

        $imported = 0;
        $failed = 0;
        $errors = [];
        $row = 1;

        while (($values = fgetcsv($handle)) !== false) {
            $row++;

            if (count($values) !== 3) {
                $errors[] = __('service_types.import_row_column_mismatch', ['row' => $row]);
                $failed++;
                continue;
            }

            $data = [
                'name'        => trim($values[0]),
                'description' => trim($values[1]) !== '' ? trim($values[1]) : null,
                'activities'  => trim($values[2]),
            ];

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) {
                $messages = collect($validator->errors()->all())->implode(', ');
                $errors[] = __('service_types.import_row_error', ['row' => $row, 'errors' => $messages]);
                $failed++;
                continue;
            }

            $activityIds = [];
            $activityWarnings = [];

            if ($data['activities'] !== '') {
                foreach (explode(';', $data['activities']) as $activityName) {
                    $activityName = trim($activityName);
                    if ($activityName === '') {
                        continue;
                    }
                    $activity = Activity::where('name', $activityName)->first(['id']);
                    if ($activity) {
                        $activityIds[] = $activity->id;
                    } else {
                        $activityWarnings[] = $activityName;
                    }
                }
            }

            DB::transaction(function () use ($data, $activityIds) {
                $serviceType = ServiceType::withTrashed()->where('name', $data['name'])->first();

                if ($serviceType) {
                    if ($serviceType->trashed()) {
                        $serviceType->restore();
                    }
                    $serviceType->fill(['description' => $data['description']])->save();
                } else {
                    $serviceType = ServiceType::create([
                        'name'        => $data['name'],
                        'description' => $data['description'],
                    ]);
                }

                $serviceType->activities()->sync($activityIds);
            });

            if (! empty($activityWarnings)) {
                $errors[] = __('service_types.import_row_activity_not_found', [
                    'row'        => $row,
                    'activities' => implode(', ', $activityWarnings),
                ]);
            }

            $imported++;
        }

        fclose($handle);

        return compact('imported', 'failed', 'errors');
    }
}
