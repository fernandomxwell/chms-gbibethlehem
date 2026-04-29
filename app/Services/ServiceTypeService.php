<?php

namespace App\Services;

use App\Http\Requests\IndexServiceTypeRequest;
use App\Http\Requests\StoreServiceTypeRequest;
use App\Http\Requests\UpdateServiceTypeRequest;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;

class ServiceTypeService
{
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
}
