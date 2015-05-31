<?php namespace Fetch404\Core\Events;

use App\Events\Event;

use Fetch404\Core\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UserFollowedSomeone extends Event implements ShouldBroadcast {

	use SerializesModels;

	public $followedUser;
	public $userWhoFollowed;

	/**
	 * Create a new event instance.
	 *
	 * @param User $followedUser
	 * @param User $userWhoFollowed
	 * @type mixed
	 */
	public function __construct(User $followedUser, User $userWhoFollowed)
	{
		//
		$this->followedUser = $followedUser;
		$this->userWhoFollowed = $userWhoFollowed;
	}

	/**
	 * Get the user for this event.
	 *
	 * @return User
	 */
	public function getUser()
	{
		return $this->followedUser;
	}

	/**
	 * Get the user responsible for this action.
	 *
	 * @return User
	 */
	public function getResponsibleUser()
	{
		return $this->userWhoFollowed;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['user-' . $this->followedUser->getId()];
	}
}
