<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property mixed path
 * @property mixed $subject
 * @property int $subject_id
 * @property bool $featured_flag
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Image extends Model
{
    protected $guarded = [];

    protected $casts = [
        'subject_id' => 'int',
        'featured_flag' => 'bool'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting( function ( Image $image ) {
            Storage::disk('public')->delete($image->path);
        } );
    }

    /**
     * Toggle the featured_flag attribute
     *
     * @param bool $flag
     * @return bool
     */
    public function feature($flag = true)
    {
        $this->featured_flag = $flag;
        $this->save();

        return $flag;
    }

    /**
     * @param Builder $query
     * @param Model $subject
     * @return Builder
     */
    public function scopeOfSubject(Builder $query, Model $subject)
    {
        return $query->where('subject_type',get_class($subject) )
            ->where('subject_id',$subject->id);
    }

    /**
     * Get only featured images
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFeatured(Builder $query)
    {
        return $query->where('featured_flag',true);
    }

    /**
     * Is this a featured image?
     * @return bool
     */
    public function isFeatured()
    {
        return (bool) $this->featured_flag;
    }

    /**
     * An Image has a subject of various types
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo('subject');
    }
}