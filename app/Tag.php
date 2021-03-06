<?php

namespace App;

use App\Events\TagWasCreated;
use App\Events\TagWasUpdated;
use App\Events\TagWasDestroyed;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Tag extends Model
{
    protected $guarded = [];


    protected $dispatchesEvents = [
        'created' => TagWasCreated::class,
        'updated' => TagWasUpdated::class,
        'deleting' => TagWasDestroyed::class,
    ];
}
