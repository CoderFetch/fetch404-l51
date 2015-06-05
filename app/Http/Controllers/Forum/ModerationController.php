<?php namespace App\Http\Controllers\Forum;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\Forum\Threads\ThreadLockRequest;
use App\Http\Requests\Forum\Threads\ThreadPinRequest;
use App\Http\Requests\Forum\Threads\ThreadUnlockRequest;
use Fetch404\Core\Events\TopicWasLocked;
use Fetch404\Core\Events\TopicWasPinned;
use Fetch404\Core\Events\TopicWasUnlocked;
use Laracasts\Flash\Flash;

class ModerationController extends Controller {

	/**
	 * Lock a topic.
	 *
	 * @param ThreadLockRequest $request
	 * @return Response
	 */
	public function lock(ThreadLockRequest $request)
	{
		//
		$topic = $request->route()->getParameter('topic');
		$user = $request->user();

		if ($topic->locked == 1)
		{
			Flash::error('This thread is already locked.');
			return redirect()->back();
		}

		$topic->update(array('locked' => 1));

		Flash::success('Locked thread');

		return redirect()->back();
	}

	/**
	 * Unlock a topic.
	 *
	 * @param ThreadUnlockRequest $request
	 * @return Response
	 */
	public function unlock(ThreadUnlockRequest $request)
	{
		//
		$topic = $request->route()->getParameter('topic');
		$user = $request->user();

		if ($topic->locked == 0)
		{
			Flash::error('This thread has not been locked.');
			return redirect()->back();
		}

		$topic->update(array('locked' => 0));

		Flash::success('Unlocked thread');

		return redirect()->back();
	}

	/**
	 * Pin a topic.
	 *
	 * @param ThreadPinRequest $request
	 * @return Response
	 */
	public function pin(ThreadPinRequest $request)
	{
		$topic = $request->route()->getParameter('topic');
		$user = $request->user();

		if ($topic->pinned == 1)
		{
			Flash::error('This thread is already pinned.');
			return redirect()->back();
		}

		$topic->update(array('pinned' => 1));

		Flash::success('Pinned thread');

		return redirect()->back();
	}
}
