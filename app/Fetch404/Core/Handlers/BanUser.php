<?php namespace Fetch404\Core\Handlers;

use Carbon\Carbon;
use Fetch404\Core\Events\UserWasBanned;

class BanUser {

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
	 * @param  UserWasBanned  $event
	 * @return void
	 */
	public function handle(UserWasBanned $event)
	{
		//
		$user = $event->getUser();
		$banTime = $event->getBannedUntil();
		$currentUser = $event->getResponsibleUser();

		$now = Carbon::now();

		$now->addDays(4);
		$now->addHours(2);
		$now->addMinutes(1);
		$now->addSeconds(4);

		$user->update(array(
			'is_banned' => 1,
			'banned_until' => $now->toDateTimeString()
		));
	}

}
