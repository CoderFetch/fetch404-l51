<?php namespace Fetch404\Core\Events;

use App\Events\Event;

use Fetch404\Core\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UserUnfollowedSomeone extends Event implements ShouldBroadcast
{

	use SerializesModels;

	public $unfollowedUser;
	public $userWhoUnfollowed;

	/**
	 * Create a new event instance.
	 *
	 * @param User $unfollowedUser
	 * @param User $userWhoUnfollowed
	 * @type mixed
	 */
	public function __construct(User $unfollowedUser, User $userWhoUnfollowed)
	{
		//
		$this->unfollowedUser = $unfollowedUser;
		$this->userWhoUnfollowed = $userWhoUnfollowed;
	}

	/**
	 * Get the user for this event.
	 *
	 * @return User
	 */
	public function getUser()
	{
		return $this->unfollowedUser;
	}

	/**
	 * Get the user responsible for this action.
	 *
	 * @return User
	 */
	public function getResponsibleUser()
	{
		return $this->userWhoUnfollowed;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['user-' . $this->unfollowedUser->id];
	}
}