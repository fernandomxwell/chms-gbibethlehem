<?php

namespace App\Services;

use App\Http\Requests\IndexActivityRequest;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityService
{
    public function getPaginatedActivities(IndexActivityRequest $request)
    {
        $validatedData = $request->validated();

        return Activity::searchBy($validatedData)
            ->select([
                'id',
                'name',
                'start_time',
                'rrule',
                'sort_order',
            ])
            ->orderBy('sort_order')
            ->paginate()
            ->withQueryString();
    }

    public function reorder(array $ids): void
    {
        $sortOrders = Activity::whereIn('id', $ids)
            ->orderBy('sort_order')
            ->pluck('sort_order')
            ->toArray();

        DB::transaction(function () use ($ids, $sortOrders) {
            foreach ($ids as $i => $id) {
                Activity::where('id', $id)->update(['sort_order' => $sortOrders[$i]]);
            }
        });
    }

    public function create(StoreActivityRequest $request)
    {
        $data = $request->validated();
        $data['rrule'] = constructRrule(
            $data['frequency'],
            $data['interval'],
            $data['byday'] ?? null,
            $data['end_condition'] ?? null,
            $data['until'] ?? null,
            $data['count'] ?? null
        );

        $activity = Activity::withTrashed()
            ->where('name', $data['name'])
            ->first();

        if ($activity) {
            if ($activity->trashed()) {
                $activity->restore();
            }

            $activity->fill($data)->save();

            return $activity;
        }

        $data['sort_order'] = (Activity::max('sort_order') ?? 0) + 1;

        return Activity::create($data);
    }

    public function update(UpdateActivityRequest $request, int $id)
    {
        $activity = Activity::findOrFail($id, ['id']);

        $data = $request->validated();
        $data['rrule'] = constructRrule(
            $data['frequency'],
            $data['interval'],
            $data['byday'] ?? null,
            $data['end_condition'] ?? null,
            $data['until'] ?? null,
            $data['count'] ?? null
        );

        $activity->update($data);

        return $activity;
    }

    public function delete(int $id)
    {
        Activity::findOrFail($id, ['id'])->delete();
    }

    public function bulkDelete(array $ids): void
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
    }

    public function getActivitiesForAjax(Request $request)
    {
        return Activity::searchBy($request->all())
            ->orderBy('sort_order')
            ->select([
                'id',
                'name',
            ])
            ->simplePaginate();
    }

    public function generateRruleSummary(array $rruleData)
    {
        $frequency = $rruleData['frequency'];
        $interval = $rruleData['interval'] ?? 1;
        $byday = $rruleData['byday'] ?? [];
        $until = $rruleData['until'] ?? null;
        $count = $rruleData['count'] ?? null;

        $dayMap = [
            'MO' => __('day.monday'),
            'TU' => __('day.tuesday'),
            'WE' => __('day.wednesday'),
            'TH' => __('day.thursday'),
            'FR' => __('day.friday'),
            'SA' => __('day.saturday'),
            'SU' => __('day.sunday'),
        ];

        $summary = __('activities.this_event_repeats');

        switch ($frequency) {
            case 'DAILY':
                $summary .= $interval > 1
                    ? ' ' . __('every') . " {$interval} " . __('days')
                    : ' ' . __('every_day');
                break;

            case 'WEEKLY':
                $days = array_map(fn($day) => $dayMap[$day] ?? $day, $byday);
                $daysList = implode(', ', $days);
                $summary .= $interval > 1
                    ? ' ' . __('every') . " {$interval} " . __('weeks') . ' ' . __('on') . " {$daysList}"
                    : ' ' . __('every_week_on') . " {$daysList}";
                break;

            case 'MONTHLY':
                $summary .= $interval > 1
                    ? ' ' . __('every') . " {$interval} " . __('months')
                    : ' ' . __('every_month');
                break;

            case 'YEARLY':
                $summary .= $interval > 1
                    ? ' ' . __('every') . " {$interval} " . __('years')
                    : ' ' . __('every_year');
                break;

            default:
                return __('no_recurrence');
        }

        if ($until) {
            try {
                $summary .= ' ' . __('until') . ' ' . $until;
            } catch (\Exception $e) {
                $summary .= ' ' . __('until') . ' (?)';
            }
        } elseif ($count) {
            $summary .= ' ' . __('for') . " {$count} " . __('occurrences');
        }

        return ucfirst(strtolower($summary));
    }
}
