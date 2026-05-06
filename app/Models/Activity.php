<?php

namespace App\Models;

use App\Traits\Models\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use Searchable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'start_time',
        'rrule',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
    ];

    /**
     * Get the searchable attributes
     */
    public function getSearchable(): array
    {
        return [
            'name',
        ];
    }

    /**
     * Get the schedule groups.
     */
    public function scheduleGroups(): HasMany
    {
        return $this->hasMany(ScheduleGroup::class);
    }

    /**
     * Get the service types.
     */
    public function serviceTypes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class);
    }
}
