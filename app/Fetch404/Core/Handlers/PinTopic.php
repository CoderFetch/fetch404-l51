<?php namespace Fetch404\Core\Handlers;

use Fetch404\Core\Events\TopicWasPinned;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class PinTopic {

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
	 * @param  TopicWasPinned  $event
	 * @return void
	 */
	public function handle(TopicWasPinned $event)
	{
		//
		$topic = $event->getTopic();
		$currentUser = $event->getResponsibleUser();
		$user = $topic->user;

		$topic->update(array(
			'pinned' => 1
		));
	}

}
