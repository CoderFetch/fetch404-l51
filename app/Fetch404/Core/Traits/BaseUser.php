<?php namespace Fetch404\Core\Traits;

use Carbon\Carbon;

use Auth;

use Fetch404\Core\Models\AccountConfirmation;
use Fetch404\Core\Models\User;
use Illuminate\Support\Facades\Storage;

trait BaseUser {

    /**
     * Relationship functions
     * DO NOT MODIFY
     */

    /**
     * Get all the topics created by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany('Fetch404\Core\Models\Topic');
    }

    /**
    * Get all the posts created by a user.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function posts()
    {
        return $this->hasMany('Fetch404\Core\Models\Post');
    }

    /**
     * Get all of the user's news posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news()
    {
        return $this->hasMany('Fetch404\Core\Models\News');
    }

    /**
     * Get any name changes the user has had.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function nameChanges()
    {
        return $this->hasMany('Fetch404\Core\Models\NameChange');
    }

    /**
     * Get any likes that the user gave.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likesGiven()
    {
        return $this->hasMany('Fetch404\Core\Models\Like', 'user_id');
    }

    /**
     * Get any likes that the user received.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likesReceived()
    {
        return $this->hasMany('Fetch404\Core\Models\Like', 'liked_user_id');
    }

    /**
     * Get a user's settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings()
    {
        return $this->hasMany('Fetch404\Core\Models\UserSetting');
    }

    /**
     * Get all of a user's badges.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function badges()
    {
        return $this->belongsToMany('Fetch404\Core\Models\Badge', 'user_badge', 'user_id');
    }

    /**
     * Get the value of a certain setting for this user.
     * If it has not yet been set, a default value will be returned,
     * or if none is specified, null.
     *
     * @param $name
     * @param $default
     * @return string
     */
    public function getSetting($name, $default = null)
    {
        $setting = $this->settings()->where('name', '=', $name)->first();

        if ($setting == null)
        {
            return ($default == null ? null : $default);
        }
        else
        {
            return $setting->value;
        }
    }

    /**
     * Set a specific setting for this user.
     * If it has not yet been set, it will be created with the given value.
     *
     * @param $name
     * @param $value
     * @return void
     */
    public function setSetting($name, $value)
    {
        $setting = $this->settings()->where('name', '=', $name)->first();

        if ($setting == null)
        {
            return $this->settings()->create(array(
                'name' => $name,
                'value' => $value,
                'user_id' => $this->getId()
            ));
        }
        else
        {
            return $setting->update(array(
                'value' => $value
            ));
        }

        return false;
    }

    /**
     * Get the user's account confirmation object.
     *
     * @return AccountConfirmation
     */
    public function getAccountConfirmation()
    {
        $confirmation = AccountConfirmation::where(
            'user_id',
            '=',
            $this->id
        )->first();

        if ($confirmation == null)
        {
            return null;
        }

        if ($this->isConfirmed())
        {
            return null;
        }

        return $confirmation;
    }

    /**
     * Attribute functions
     *
     */
    /**
     * Is the user confirmed?
     *
     * @return boolean
     */
    public function isConfirmed()
    {
        return $this->confirmed == 1;
    }

    /**
     * Get the user's profile URL.
     *
     * @return string
     */
    public function getProfileURLAttribute()
    {
        return route('profile.get.show', ['slug' => $this->slug, 'id' => $this->id]);
    }

    /**
     * Get the generated URL to a user's avatar.
     * Returns a link to the default avatar if the user does not have an avatar
     *
     * @param boolean $large
     * @return string
     */
    public function getAvatarURL($large = true)
    {
        $extensions = [
            'png',
            'jpg'
        ];

        foreach($extensions as $ext)
        {
            if (file_exists(public_path() . '/fetch404/avatars/' . $this->id . '.' . $ext))
            {
                return '/fetch404/avatars/' . $this->id . '.' . $ext;
            }
        }

        return '/assets/img/defaultavatar' . ($large ? 'large' : '') . '.png';
    }

    /**
     * Check to see if a user has uploaded a profile picture.
     *
     * @return boolean
     */
    public function hasAvatar()
    {
        $extensions = [
            'png',
            'jpeg',
            'jpg'
        ];

        $disk = Storage::disk('fetch404');

        foreach($extensions as $ext)
        {
            if ($disk->exists('avatars/' . $this->id . '.' . $ext))
            {
                return true;
            }
        }

        return false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getJoinedOn()
    {
        $now = Carbon::now();
        $now->subDays(7);

        return ($this->created_at > $now->toDateTimeString() ? $this->created_at->diffForHumans() : $this->created_at->format('M jS, Y'));
    }

    public function getLastActivity()
    {
        $now = Carbon::now();
        $now->subDays(7);

        if ($this->last_active == null)
        {
            return ($this->created_at == null ? "never" : ($this->created_at > $now->toDateTimeString() ? $this->created_at->diffForHumans() : $this->created_at->format('M jS, Y')));
        }

        if (!Auth::check() && $this->getSetting("show_when_im_online", 1) == '0') return "[hidden]";

        if ($this->getSetting("show_when_im_online", 1) == '0' && Auth::id() != $this->id)
        {
            return "[hidden]";
        }

        return ($this->last_active > $now->toDateTimeString() ? $this->last_active->diffForHumans() : $this->last_active->format('M jS, Y'));
    }

    public function getLastActiveDesc()
    {
        if (!Auth::check() && $this->getSetting("show_when_im_online", 1) == '0') return "[hidden]";

        if ($this->getSetting("show_when_im_online", 1) == '0' && Auth::id() != $this->id)
        {
            return "[hidden]";
        }

        return $this->last_active_desc;
    }

    public function postCount()
    {
        return $this->posts()->count();
    }

    /**
     * Check to see if the current user is banned.
     *
     * @return bool
     */
    public function isBanned()
    {
        if ($this->banned_until != null)
        {
            return ($this->is_banned == 1 && $this->banned_until > Carbon::now()->toDateTimeString());
        }

        return $this->is_banned == 1;
    }

    /**
     * Check to see if the current user is the same as the given user
     * The only parameter type accepted is a User object.
     *
     * @param User $user
     * @return boolean
     */
    public function isUser(User $user)
    {
        if (is_null($user)) return false;

        return $this->getId() == $user->getId();
    }

    /**
     * Get a user's current "status" (their latest profile post)
     *
     * @return object
     */
    public function currentStatus()
    {
        $profilePost = $this->profilePosts()
            ->where('from_user_id', '=', $this->getId())
            ->where('to_user_id', '=', $this->getId())
            ->first();

        return $profilePost;
    }

    /**
     * Query scopes
     *
     * @param $query
     * @return mixed
     */
    public function scopeBanned($query)
    {
        return $query->where('is_banned', '=', 1);
    }
}