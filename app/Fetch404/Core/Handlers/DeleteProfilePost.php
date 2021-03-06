<?php namespace Fetch404\Core\Handlers;

use Fetch404\Core\Events\ProfilePostWasDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class DeleteProfilePost {

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
	 * @param ProfilePostWasDeleted $event
	 * @return void
	 */
	public function handle(ProfilePostWasDeleted $event)
	{
		//
		$post = $event->getProfilePost();

		$post->delete();
	}

}
