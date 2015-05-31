<?php namespace Fetch404\Core\Events;

use Fetch404\Core\Models\ProfilePost;
use Fetch404\Core\Models\User;
use Illuminate\Queue\SerializesModels;
use App\Events\Event;

class ProfilePostWasDeleted extends Event {

	use SerializesModels;

	public $profilePost;
	public $responsibleUser;

	/**
	 * Create a new event instance.
	 *
	 * @param ProfilePost $profilePost
	 * @param User $responsibleUser
	 */
	public function __construct(ProfilePost $profilePost, User $responsibleUser)
	{
		//
		$this->profilePost = $profilePost;
		$this->responsibleUser = $responsibleUser;
	}

	/**
	 * Get the post for this event.
	 *
	 * @return ProfilePost
	 */
	public function getProfilePost()
	{
		return $this->profilePost;
	}

	/**
	 * Get the user responsible for this action.
	 *
	 * @return User
	 */
	public function getResponsibleUser()
	{
		return $this->responsibleUser;
	}

	/**
	 * Get the channels the event should be broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return [];
	}
}
