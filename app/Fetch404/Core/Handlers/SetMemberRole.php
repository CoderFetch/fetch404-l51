<?php namespace Fetch404\Core\Handlers;

use Fetch404\Core\Events\UserWasRegistered;
use Fetch404\Core\Models\Role;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class SetMemberRole {

	private $role;

	/**
	 * Create the event handler.
	 *
	 * @param Role $role
	 */
	public function __construct(Role $role)
	{
		//
		$this->role = $role;
	}

	/**
	 * Handle the event.
	 *
	 * @param  UserWasRegistered  $event
	 * @return void
	 */
	public function handle(UserWasRegistered $event)
	{
		//
		$user = $event->getUser();

		$defaultRole = $this->role->where('is_default', '=', 1)->first();

		if ($defaultRole)
		{
			$user->attachRole($defaultRole);
		}
	}

}
