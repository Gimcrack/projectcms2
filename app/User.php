<?php

namespace App;

use App\Events\UserWasCreated;
use App\Events\UserWasDestroyed;
use App\Events\UserWasUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string api_token
 * @property bool $admin_flag
 * @property bool $approver_flag
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Authenticatable
{
    use Notifiable;

    public static function boot()
    {
        parent::boot();

        static::creating( function(User $user) {
            $user->api_token = str_random(60);
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    protected $casts = [
        'admin_flag' => 'bool',
        'approver_flag' => 'bool'
    ];


    protected $dispatchesEvents = [
        'created' => UserWasCreated::class,
        'updated' => UserWasUpdated::class,
        'deleting' => UserWasDestroyed::class,
    ];

    /**
     * Get the approver users
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeApprovers(Builder $query)
    {
        return $query->where('admin_flag',1)->orWhere('approver_flag',1);
    }

    /**
     * Is the user an approver?
     *
     * @return bool
     */
    public function isApprover()
    {
        return (bool) $this->admin_flag || $this->approver_flag;
    }

    /**
     * Is the user an admin?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return (bool) $this->admin_flag;
    }

    /**
     * Promote the user to an admin
     * @method promoteToAdmin
     *
     * @return   void
     */
    public function promoteToAdmin()
    {
        $this->admin_flag = 1;
        $this->save();
    }

    /**
     * Demote the admin to a user
     * @method demoteToUser
     *
     * @return   void
     */
    public function demoteToUser()
    {
        $this->admin_flag = 0;
        $this->save();
    }

    /**
     * Promote the user to an approver
     * @method promoteToApprover
     *
     * @return   void
     */
    public function promoteToApprover()
    {
        $this->approver_flag = 1;
        $this->save();
    }

    /**
     * Promote the user to an approver
     * @method demoteApproverToUser
     *
     * @return   void
     */
    public function demoteApproverToUser()
    {
        $this->approver_flag = 0;
        $this->save();
    }
}