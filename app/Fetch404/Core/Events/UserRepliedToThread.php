<?php namespace Fetch404\Core\Events;

use App\Events\Event;

use Fetch404\Core\Models\Post;
use Fetch404\Core\Models\Topic;
use Fetch404\Core\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UserRepliedToThread extends Event implements ShouldBroadcast {

	use SerializesModels;

	public $user;
	public $post;
	public $thread;

	/**
	 * Create a new event instance.
	 *
	 * @param User $user
	 * @param Post $post
	 * @param Topic $thread
	 */
	public function __construct(User $user, Post $post, Topic $thread)
	{
		//
		$this->user = $user;
		$this->post = $post;
		$this->thread = $thread;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return array
	 */
	public function broadcastOn()
	{
		return ['thread-' . $this->thread->id];
	}
}
