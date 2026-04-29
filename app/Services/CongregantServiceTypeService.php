<?php

namespace App\Services;

use App\Http\Requests\IndexCongregantServiceTypeRequest;
use App\Http\Requests\StoreCongregantServiceTypeRequest;
use App\Http\Requests\UpdateCongregantServiceTypeRequest;
use App\Models\Congregant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CongregantServiceTypeService
{
    public function index(IndexCongregantServiceTypeRequest $request)
    {
        $validatedData = $request->validated();

        return Congregant::query()
            ->with([
                'serviceTypes:id,name',
                'serviceTypesPivot:id,congregant_id,service_type_id,activity_id',
                'serviceTypesPivot.activity:id,name',
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
