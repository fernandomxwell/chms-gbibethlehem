<?php

namespace App\Services;

use App\Http\Requests\IndexScheduleRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Models\Activity;
use App\Models\Congregant;
use App\Models\Schedule;
use App\Models\ScheduleGroup;
use App\Models\ServiceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleService
{
    public function getPaginatedScheduleGroup(IndexScheduleRequest $request)
    {
        $validatedData = $request->validated();

        return ScheduleGroup::with('activity:id,name')
            ->when($validatedData['search'] ?? null, function ($query) use ($validatedData) {
                $query->whereHas('activity', function ($q) use ($validatedData) {
                    $q->where('name', 'like', '%' . $validatedData['search'] . '%');
                });
            })
            ->orderByDesc('id')
            ->select([
                'id',
                'activity_id',
                'start_date',
                'end_date',
            ])
            ->paginate()
            ->withQueryString();
    }

    public function create(StoreScheduleRequest $request)
    {
        $data = $request->validated();

        $activity = Activity::findOrFail($data['activity_id'], [
            'id',
            'rrule',
        ]);

        $serviceTypes = ServiceType::whereIn('id', array_keys($data['service_types']))
            ->get(['id'])
            ->keyBy('id');

        $dates = generateOccurrences(
            Carbon::parse($data['start_date']),
            $activity->rrule,
            Carbon::parse($data['end_date'])
        );

        $lastScheduleDate = ''; // [TODO] Get the last schedule date from the database or set to empty string

        DB::transaction(function () use ($activity, $dates, $lastScheduleDate, $data, $serviceTypes) {
            $group = ScheduleGroup::create([
                'activity_id' => $activity->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);

            foreach ($dates as $date) {
                $date = $date->format('Y-m-d');

                $assignments = [];
                $assignedCongregantIds = [];

                foreach ($data['service_types'] as $serviceTypeId => $serviceType) {
                    if (! isset($serviceTypes[$serviceTypeId]) || ! isset($serviceType['include'])) {
                        continue;
                    }

                    $isRepeatable = isset($serviceType['is_repeatable']);
                    $eligible = $this->getEligibleCongregants(
                        $activity->id,
                        $serviceTypeId,
                        $date,
                        $lastScheduleDate,
                        $serviceType['count'],
                        $isRepeatable,
                        $assignedCongregantIds
                    );

                    $selected = $eligible
                        ->sortBy([['service_count', 'asc'], ['total_activity_count', 'desc']])
                        ->groupBy(fn($c) => $c->service_count . '_' . $c->total_activity_count)
                        ->flatMap(fn($group) => $group->shuffle()->values())
                        ->take($serviceType['count'])
                        ->values();

                    $assignedCongregantIds = array_merge($assignedCongregantIds, $selected->pluck('id')->all());

                    $assignments[$serviceTypeId] = [
                        'required' => (int) $serviceType['count'],
                        'eligible' => $eligible,
                        'selected' => $selected,
                    ];
                }

                $this->fillShortfalls($assignments);

                foreach ($assignments as $serviceTypeId => $assignment) {
                    $schedule = Schedule::create([
                        'schedule_group_id' => $group->id,
                        'activity_id' => $activity->id,
                        'service_type_id' => $serviceTypeId,
                        'scheduled_date' => $date,
                    ]);

                    $schedule->congregants()->attach($assignment['selected']->pluck('id'));
                }

                $lastScheduleDate = $date;
            }
        });
    }

    public function show(string $id)
    {
        $schedules = Schedule::where('schedule_group_id', $id)
            ->with('congregants:id,honorific_title,full_name')
            ->orderBy('scheduled_date')
            ->get([
                'id',
                'service_type_id',
                'scheduled_date',
            ]);

        $availableServiceTypesIds = $schedules->pluck('service_type_id')->unique();
        $availableServiceTypes = ServiceType::whereIntegerInRaw('id', $availableServiceTypesIds)
            ->get([
                'id',
                'name',
            ])
            ->keyBy('id');

        $schedules = $schedules->groupBy(['scheduled_date', 'service_type_id']);

        $scheduleData = [];

        foreach ($schedules as $date => $scheduleByServiceType) {
            $row = ['date' => $date];

            foreach ($scheduleByServiceType as $serviceTypeId => $schedule) {
                if (! isset($availableServiceTypes[$serviceTypeId])) {
                    continue;
                }

                $serviceType = $availableServiceTypes[$serviceTypeId];
                $congregants = $schedule->pluck('congregants')->flatten();

                $row[$serviceType->name] = $congregants
                    ->map(fn($c) => trim(($c->honorific_title?->label() ?? '') . ' ' . $c->full_name))
                    ->implode(',<br>');
            }

            $scheduleData[] = $row;
        }

        return [
            'serviceTypes' => $availableServiceTypes,
            'scheduleData' => $scheduleData,
        ];
    }

    public function export(string $id): array
    {
        $scheduleGroup = ScheduleGroup::with('activity:id,name')
            ->findOrFail($id, ['id', 'activity_id', 'start_date', 'end_date']);

        $schedules = Schedule::where('schedule_group_id', $id)
            ->with('congregants:id,honorific_title,full_name')
            ->orderBy('scheduled_date')
            ->get(['id', 'service_type_id', 'scheduled_date']);

        $availableServiceTypesIds = $schedules->pluck('service_type_id')->unique();
        $availableServiceTypes = ServiceType::whereIntegerInRaw('id', $availableServiceTypesIds)
            ->get(['id', 'name'])
            ->keyBy('id');

        $schedules = $schedules->groupBy(['scheduled_date', 'service_type_id']);

        $csvHeaders = [__('date')];
        foreach ($availableServiceTypes as $serviceType) {
            $csvHeaders[] = $serviceType->name;
        }

        $rows = [];
        foreach ($schedules as $date => $scheduleByServiceType) {
            $row = [Carbon::parse($date)->format('Y-m-d')];
            foreach ($availableServiceTypes as $serviceTypeId => $serviceType) {
                $schedule = $scheduleByServiceType[$serviceTypeId] ?? collect();
                $congregants = $schedule->pluck('congregants')->flatten();
                $row[] = $congregants
                    ->map(fn($c) => trim(($c->honorific_title?->label() ?? '') . ' ' . $c->full_name))
                    ->implode(', ');
            }
            $rows[] = $row;
        }

        $filename = implode('_', [
            $scheduleGroup->activity->name,
            $scheduleGroup->start_date,
            $scheduleGroup->end_date,
        ]);

        return [
            'filename' => $filename,
            'headers' => $csvHeaders,
            'rows' => $rows,
        ];
    }

    public function delete(int $id)
    {
        $scheduleGroup = ScheduleGroup::findOrFail($id, ['id']);

        DB::transaction(function () use ($scheduleGroup) {
            $schedules = Schedule::where('schedule_group_id', $scheduleGroup->id)->get();

            foreach ($schedules as $schedule) {
                $schedule->congregants()->detach();
                $schedule->delete();
            }

            $scheduleGroup->delete();
        });
    }

    public function bulkDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
    }

    protected function getEligibleCongregants(
        int $activityId,
        int $serviceTypeId,
        string $date,
        string $lastScheduleDate,
        int $requiredCount,
        bool $isRepeatable,
        array $excludeIds = []
    ) {
        $baseQuery = Congregant::query()
            ->select(['congregants.id'])
            ->whereHas('serviceTypesPivot', function ($q) use ($activityId, $serviceTypeId) {
                $q->where('activity_id', $activityId)
                    ->where('service_type_id', $serviceTypeId);
            })
            ->whereDoesntHave('schedules', function ($q) use ($date) {
                $q->where('scheduled_date', $date);
            })
            ->when(! empty($excludeIds), function ($q) use ($excludeIds) {
                $q->whereNotIn('congregants.id', $excludeIds);
            })
            ->withCount(['schedules as service_count' => function ($q) use ($activityId, $serviceTypeId) {
                $q->where('activity_id', $activityId)
                    ->where('service_type_id', $serviceTypeId);
            }])
            ->withCount(['schedules as total_activity_count' => function ($q) use ($activityId) {
                $q->where('activity_id', $activityId);
            }]);

        $freshCongregants = $baseQuery->clone()
            ->when($lastScheduleDate, function ($q) use ($lastScheduleDate) {
                $q->whereDoesntHave('schedules', function ($q) use ($lastScheduleDate) {
                    $q->where('scheduled_date', $lastScheduleDate);
                });
            })
            ->get();

        if ($freshCongregants->count() >= $requiredCount || ! $isRepeatable) {
            return $freshCongregants;
        }

        $willingToRepeat = $baseQuery->clone()
            ->whereNotIn('congregants.id', $freshCongregants->pluck('id'))
            ->where('can_serve_consecutively', true)
            ->get();

        return $freshCongregants->merge($willingToRepeat);
    }

    protected function fillShortfalls(array &$assignments): void
    {
        $serviceTypeIds = array_keys($assignments);

        // Pass 1: fill shortfalls by shifting from service types that have surplus
        foreach ($serviceTypeIds as $shortTypeId) {
            $shortfall = $assignments[$shortTypeId]['required'] - $assignments[$shortTypeId]['selected']->count();
            if ($shortfall <= 0) continue;

            foreach ($serviceTypeIds as $otherTypeId) {
                if ($otherTypeId === $shortTypeId) continue;

                $surplus = $assignments[$otherTypeId]['selected']->count() - $assignments[$otherTypeId]['required'];
                if ($surplus <= 0) continue;

                $this->shiftBetweenAssignments($assignments, $shortTypeId, $otherTypeId, min($shortfall, $surplus));

                $shortfall = $assignments[$shortTypeId]['required'] - $assignments[$shortTypeId]['selected']->count();
                if ($shortfall <= 0) break;
            }
        }

        // Pass 2: if a service type is still completely empty, shift from any other service type as last resort
        foreach ($serviceTypeIds as $shortTypeId) {
            if ($assignments[$shortTypeId]['selected']->isNotEmpty()) continue;

            foreach ($serviceTypeIds as $otherTypeId) {
                if ($otherTypeId === $shortTypeId) continue;
                if ($assignments[$otherTypeId]['selected']->isEmpty()) continue;

                $this->shiftBetweenAssignments($assignments, $shortTypeId, $otherTypeId, $assignments[$shortTypeId]['required']);

                if ($assignments[$shortTypeId]['selected']->isNotEmpty()) break;
            }
        }
    }

    private function shiftBetweenAssignments(array &$assignments, int $toTypeId, int $fromTypeId, int $limit): void
    {
        $eligibleIds = $assignments[$toTypeId]['eligible']->pluck('id')->all();
        $alreadySelectedIds = $assignments[$toTypeId]['selected']->pluck('id')->all();

        $otherAssignedIds = collect($assignments)
            ->except([$toTypeId, $fromTypeId])
            ->flatMap(fn($a) => $a['selected']->pluck('id'))
            ->all();

        $toShift = $assignments[$fromTypeId]['selected']
            ->whereIn('id', $eligibleIds)
            ->whereNotIn('id', $alreadySelectedIds)
            ->whereNotIn('id', $otherAssignedIds)
            ->sortBy([['service_count', 'asc'], ['total_activity_count', 'desc']])
            ->take($limit)
            ->values();

        if ($toShift->isEmpty()) return;

        $shiftIds = $toShift->pluck('id')->all();
        $assignments[$toTypeId]['selected'] = $assignments[$toTypeId]['selected']->merge($toShift)->values();
        $assignments[$fromTypeId]['selected'] = $assignments[$fromTypeId]['selected']
            ->whereNotIn('id', $shiftIds)->values();
    }
}
