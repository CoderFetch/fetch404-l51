<?php namespace Fetch404\Core\Events;

use App\Events\Event;

use Illuminate\Queue\SerializesModels;

class UserRepliedToThread extends Event {

	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

}
