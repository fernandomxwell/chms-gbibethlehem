<?php

namespace App\Services;

use App\Http\Requests\IndexActivityRequest;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;

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
            ])
            ->paginate()
            ->withQueryString();
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
            ->orderBy('name')
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
