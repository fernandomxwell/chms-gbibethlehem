<?php

namespace App\Models;

use App\Traits\Models\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceType extends Model
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
        'sort_order',
    ];

    /**
     * Get the attributes that should be hidden.
     *
     * @return array<string, string>
     */
    protected $hidden = [
        'pivot',
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
     * Get the congregants.
     */
    public function congregants(): BelongsToMany
    {
        return $this->belongsToMany(Congregant::class, 'congregant_service_types');
    }

    /**
     * Get the activities.
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class);
    }
}
