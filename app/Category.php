<?php

namespace App;

use App\Events\CategoryWasCreated;
use App\Events\CategoryWasUpdated;
use App\Events\CategoryWasDestroyed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property mixed $projects
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Category extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => CategoryWasCreated::class,
        'updated' => CategoryWasUpdated::class,
        'deleting' => CategoryWasDestroyed::class,
    ];

    /**
     * A category has many projects
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
