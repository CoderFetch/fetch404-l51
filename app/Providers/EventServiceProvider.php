<?php namespace App\Providers;

use Fetch404\Core\Events\ProfilePostWasDeleted;
use Fetch404\Core\Events\UserDislikedSomething;
use Fetch404\Core\Events\UserFollowedSomeone;
use Fetch404\Core\Events\UserLikedSomething;
use Fetch404\Core\Events\UserUnfollowedSomeone;
use Fetch404\Core\Events\UserWasBanned;
use Fetch404\Core\Events\UserWasRegistered;
use Fetch404\Core\Events\UserWasUnbanned;
use Fetch404\Core\Events\UserWroteProfilePost;
use Fetch404\Core\Handlers\AddProfilePost;
use Fetch404\Core\Handlers\AddUserFollower;
use Fetch404\Core\Handlers\BanUser;
use Fetch404\Core\Handlers\DeleteProfilePost;
use Fetch404\Core\Handlers\RemoveUserFollower;
use Fetch404\Core\Handlers\SendDislikeNotification;
use Fetch404\Core\Handlers\SendEmailConfirmation;
use Fetch404\Core\Handlers\SendFollowerNotification;
use Fetch404\Core\Handlers\SendLikeNotification;
use Fetch404\Core\Handlers\SendProfilePostNotification;
use Fetch404\Core\Handlers\SetMemberRole;
use Fetch404\Core\Handlers\UnbanUser;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ProfilePostWasDeleted::class => [
            DeleteProfilePost::class
        ],
        UserDislikedSomething::class => [
            SendDislikeNotification::class
        ],
        UserFollowedSomeone::class => [
            AddUserFollower::class,
            SendFollowerNotification::class
        ],
        UserLikedSomething::class => [
            SendLikeNotification::class
        ],
        UserUnfollowedSomeone::class => [
            RemoveUserFollower::class
        ],
        UserWasBanned::class => [
            BanUser::class
        ],
        UserWasRegistered::class => [
            SetMemberRole::class,
            SendEmailConfirmation::class
        ],
        UserWasUnbanned::class => [
            UnbanUser::class
        ],
        UserWroteProfilePost::class => [
            AddProfilePost::class,
            SendProfilePostNotification::class
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
