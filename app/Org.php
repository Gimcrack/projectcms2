<?php

namespace App;

use App\Events\OrgWasCreated;
use App\Events\OrgWasUpdated;
use App\Events\OrgWasDestroyed;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Org extends Model
{
    protected $guarded = [];


    protected $dispatchesEvents = [
        'created' => OrgWasCreated::class,
        'updated' => OrgWasUpdated::class,
        'deleting' => OrgWasDestroyed::class,
    ];
}
