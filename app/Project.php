<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property mixed $images
 * @property mixed $approver
 * @property mixed $approved_by
 * @property bool $approved_flag
 * @property \Carbon\Carbon $ends_at
 * @property \Carbon\Carbon $starts_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $published_at
 * @property \Carbon\Carbon $unpublished_at
 */
class Project extends Model
{
    protected $guarded = [];

    protected $casts = [
        'approved_flag' => 'bool'
    ];

    protected $dates = [
        'published_at',
        'unpublished_at',
        'starts_at',
        'ends_at'
    ];

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
     * Has the project been approved
     *
     * @return bool
     */
    public function approved()
    {
        return (bool) $this->approved_flag;
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
