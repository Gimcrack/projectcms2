<?php

namespace App;

use App\Events\ProjectReadyForApproval;
use Carbon\Carbon;
use App\Events\ProjectWasCreated;
use App\Events\ProjectWasUpdated;
use App\Events\ProjectWasDestroyed;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property mixed $name
 * @property mixed $images
 * @property mixed $approver
 * @property bool $ready_flag
 * @property mixed $approved_by
 * @property bool $approved_flag
 * @property Carbon $ends_at
 * @property Carbon $starts_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $published_at
 * @property Carbon $unpublished_at
 */
class Project extends Model
{
    protected $guarded = [];

    protected $casts = [
        'category_id' => 'int',
        'approved_flag' => 'bool',
        'ready_flag' => 'bool'
    ];

    protected $dates = [
        'published_at',
        'unpublished_at',
        'starts_at',
        'ends_at'
    ];

    protected $dispatchesEvents = [
        'created' => ProjectWasCreated::class,
        'updated' => ProjectWasUpdated::class,
        'deleting' => ProjectWasDestroyed::class,
    ];

    /**
     * Update the project
     *
     * @param array $attributes
     * @param array $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        if ( $this->ready() ) $this->ready(false);
        if ( $this->approved() ) $this->unapprove();

        return parent::update($attributes, $options);
    }

    /**
     * The approver of the project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Is the project ready for approval
     *  or set the ready_flag value
     *
     * @param null|bool $flag
     * @return bool
     */
    public function ready($flag = null)
    {
        if ( ! is_null($flag) ) {
            $this->ready_flag = $flag;
            $this->save();

            if ( $flag )
                ProjectReadyForApproval::dispatch($this);

            return $flag;
        }

        return (bool) $this->ready_flag;
    }

    /**
     * Has the project been approved
     *
     * @return bool
     */
    public function approved()
    {
        return (bool) $this->approved_flag;
    }

    /**
     * Has the project not been approved
     *
     * @return bool
     */
    public function unapproved()
    {
        return ! (bool) $this->approved_flag;
    }

    /**
     * Has the project been published
     *
     * @return bool
     */
    public function published()
    {
        return (bool) !! $this->published_at && $this->published_at->lte(Carbon::now())
            && ( ! $this->unpublished_at || $this->unpublished_at->gt(Carbon::now()) );
    }

    /**
     * Publish the project
     *
     * @param null $date
     */
    public function publish($date = null)
    {
        $this->published_at = ( !! $date ) ? Carbon::parse($date) : Carbon::now();
        $this->save();
    }

    /**
     * Unpublish the project
     *
     * @param null $date
     */
    public function unpublish($date = null)
    {
        $this->unpublished_at = ( !! $date ) ? Carbon::parse($date) : Carbon::now();
        $this->save();
    }

    /**
     * Approve the project
     *
     * @param User $approver
     * @throws AuthenticationException
     */
    public function approveBy( User $approver )
    {
        if  ( ! $approver->isApprover() )
            throw new AuthenticationException("You must be an approver to do that.");

        $this->approved_by = $approver->id;
        $this->approved_flag = true;
        $this->save();
    }

    /**
     * Unapprove the project
     */
    public function unapprove()
    {
        $this->approved_by = null;
        $this->approved_flag = false;

        $this->save();
    }

    /**
     * A Project can have one or more images
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'subject');
    }
}
