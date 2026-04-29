<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $activity = Activity::find($this->input('activity_id'));

        $startDateRules = ['required', 'date', Rule::date()->todayOrAfter()];
        $endDateRules = ['required', 'date', 'after_or_equal:start_date'];

        if ($activity) {
            $startDateRules[] = 'after_or_equal:' . $activity->start_time->toDateString();

            if ($activity->rrule) {
                $parsed = parseRrule($activity->rrule);
                if ($parsed['until']) {
                    $endDateRules[] = 'before_or_equal:' . $parsed['until'];
                }
            }
        }

        return [
            'activity_id' => 'required|exists:activities,id',
            'start_date' => $startDateRules,
            'end_date' => $endDateRules,
            'service_types' => 'required|array',
            'service_types.*.include' => 'nullable|boolean',
            'service_types.*.count' => 'nullable|integer|min:1|required_with:service_types.*.include',
            'service_types.*.is_repeatable' => 'nullable|boolean',
        ];
    }

    /**
     * Customize the validation attribute names that apply to the request.
     */
    public function attributes()
    {
        return [
            'activity_id' => __('activities.index'),
            'start_date' => __('start_date'),
            'end_date' => __('end_date'),
        ];
    }
}
