<?php namespace Fetch404\Core\Handlers;

use Fetch404\Core\Events\TopicWasUnlocked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class UnlockTopic {

	/**
	 * Create the event handler.
	 *
	 * @return mixed
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param TopicWasUnlocked $event
	 * @return void
	 */
	public function handle(TopicWasUnlocked $event)
	{
		//
		$topic = $event->getTopic();
		$currentUser = $event->getResponsibleUser();
		$user = $topic->user;

		$topic->update(array(
			'locked' => 0
		));

		$user->notifications()->create(array(
			'subject_id' => $topic->id,
			'subject_type' => get_class($topic),
			'name' => 'topic_unlocked',
			'user_id' => $user->getId(),
			'sender_id' => $currentUser->getId(),
			'is_read' => 0
		));
	}

}
