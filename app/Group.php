<?php

namespace App;

use App\Events\GroupWasCreated;
use App\Events\GroupWasUpdated;
use App\Events\GroupWasDestroyed;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Group extends Model
{
    protected $guarded = [];


    protected $dispatchesEvents = [
        'created' => GroupWasCreated::class,
        'updated' => GroupWasUpdated::class,
        'deleting' => GroupWasDestroyed::class,
    ];
}
