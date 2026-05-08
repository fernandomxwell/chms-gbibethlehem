<?php

namespace App\Services;

use App\Http\Requests\IndexServiceTypeRequest;
use App\Http\Requests\StoreServiceTypeRequest;
use App\Http\Requests\UpdateServiceTypeRequest;
use App\Traits\Services\HasBulkDelete;
use App\Traits\Services\HasCsvImportExport;
use App\Traits\Services\HasReorder;
use Illuminate\Validation\Rules\Unique;
use App\Models\Activity;
use App\Models\ServiceType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceTypeService
{
    use HasBulkDelete, HasCsvImportExport, HasReorder;

    protected function getReorderModel(): string
    {
        return ServiceType::class;
    }

    protected function getExportColumns(): array
    {
        return [
            'name',
            'description',
            'activities',
        ];
    }

    protected function getExportFilename(): string
    {
        return 'jenis_pelayanan_' . now()->format('Y-m-d') . '.csv';
    }

    protected function getTemplateFilename(): string
    {
        return 'template_jenis_pelayanan.csv';
    }

    protected function getTemplateRows(): array
    {
        return [['Worship Leader', 'Memimpin pujian', 'Ibadah Minggu;Ibadah Pemuda']];
    }

    protected function writeExportRows($handle): void
    {
        ServiceType::with(['activities' => fn($q) => $q->select(['activities.id', 'activities.name'])->orderBy('activities.sort_order')])
            ->orderBy('sort_order')
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
    }

    public function getPaginatedServiceTypes(IndexServiceTypeRequest $request)
    {
        $validatedData = $request->validated();

        return ServiceType::query()
            ->with(['activities' => fn($q) => $q->select(['activities.id', 'activities.name'])->orderBy('activities.sort_order')])
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
                'sort_order',
            ])
            ->orderBy('sort_order')
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
                $data['sort_order'] = (ServiceType::max('sort_order') ?? 0) + 1;
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

    public function getAll($attributes = ['*'], array $relations = [])
    {
        return ServiceType::select($attributes)->with($relations)->orderBy('sort_order')->get();
    }

    public function importCsv(UploadedFile $file): array
    {
        [$handle, $earlyReturn] = $this->openValidatedCsvImport($file, 'service_types');
        if ($earlyReturn !== null) {
            return $earlyReturn;
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
        $maxSortOrder = ServiceType::max('sort_order') ?? 0;

        while (($values = fgetcsv($handle)) !== false) {
            $row++;

            if (count($values) !== count($this->getExportColumns())) {
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

            DB::transaction(function () use ($data, $activityIds, &$maxSortOrder) {
                $serviceType = ServiceType::withTrashed()->where('name', $data['name'])->first();

                if ($serviceType) {
                    if ($serviceType->trashed()) {
                        $serviceType->restore();
                        if (! $serviceType->sort_order) {
                            $serviceType->sort_order = ++$maxSortOrder;
                            $serviceType->save();
                        }
                    }
                    $serviceType->fill(['description' => $data['description']])->save();
                } else {
                    $serviceType = ServiceType::create([
                        'name'        => $data['name'],
                        'description' => $data['description'],
                        'sort_order'  => ++$maxSortOrder,
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
