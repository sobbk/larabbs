<?php

namespace App\Models;

use App\Models\Traits\ActiveUserHelper;
use App\Models\Traits\LastActiveAtHelper;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Str;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmailTrait, ActiveUserHelper, LastActiveAtHelper;

    use Notifiable {
        notify as protected laravelNotify;
    }

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'introduction',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function topics()
    {
        return $this->hasMany(Topic::class);
    }


    public function replies()
    {
        return $this->hasMany(Reply::class);
    }


    public function setPasswordAttribute($value)
    {
        if (strlen($value) != 60) {
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }


    public function setAvatarAttribute($path)
    {
        if (! Str::startsWith($path, 'http')) {
            $path = config('app.url') . '/uploads/images/avatars/' . $path;
        }

        $this->attributes['avatar'] = $path;
    }


    public function isAuthor($model)
    {
        return $this->id == $model->user_id;
    }


    public function notify($instance)
    {
        if ($this->id == \Auth::id()) {
            return;
        }

        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }


    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }
}
