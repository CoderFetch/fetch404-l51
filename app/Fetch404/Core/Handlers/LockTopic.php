<?php namespace Fetch404\Core\Handlers;


use Fetch404\Core\Events\TopicWasLocked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class LockTopic {

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
	 * @param  TopicWasLocked  $event
	 * @return void
	 */
	public function handle(TopicWasLocked $event)
	{
		//
		$topic = $event->getTopic();
		$currentUser = $event->getResponsibleUser();
		$user = $topic->user;

		$topic->update(array(
			'locked' => 1
		));
	}

}
