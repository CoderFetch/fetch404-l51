<?php namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;

use App\Http\Requests\Forum\Threads\ThreadCreateRequest;
use App\Http\Requests\Forum\Threads\ThreadReplyRequest;

use Fetch404\Core\Events\ThreadWasCreated;
use Fetch404\Core\Events\UserRepliedToThread;
use Fetch404\Core\Models\Channel;
use Fetch404\Core\Models\Post;
use Fetch404\Core\Models\Topic;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

class ForumController extends Controller {

	private $topic;
	private $post;

	/**
	 * Attempt to create a thread
	 *
	 * @param ThreadCreateRequest $request
	 * @return Response
	 */
	public function postCreateThread(ThreadCreateRequest $request)
	{
		$channel = $request->route()->getParameter('channel');
		$title = $request->input('title');
		$body = $request->input('body');

		$thread = $this->topic->create(array(
			'title' => $title,
			'slug' => str_slug($title, '-'),
			'user_id' => Auth::id(),
			'channel_id' => $channel->id,
			'locked' => 0,
			'pinned' => 0
		));

		$post = $this->post->create(array(
			'topic_id' => $thread->id,
			'user_id' => Auth::id(),
			'content' => $body
		));

		event(new ThreadWasCreated($thread, $channel, Auth::user()));

		return redirect($thread->Route);
	}

	/**
	 * Attempt to quick-reply to a thread
	 *
	 * @param ThreadReplyRequest $request
	 * @return void
	 */
	public function postQuickReplyToThread(ThreadReplyRequest $request)
	{
		$topic = $request->route()->getParameter('topic');
		$body = $request->input('body');

		$post = $this->post->create(array(
			'topic_id' => $topic->id,
			'user_id' => Auth::id(),
			'content' => $body
		));

		$post->topic->touch();

		return redirect($post->Route);
	}

	/**
	 * Attempt to reply to a thread
	 *
	 * @param ThreadReplyRequest $request
	 * @return void
	 */	
	public function postReplyToThread(ThreadReplyRequest $request)
	{
		$topic = $request->route()->getParameter('topic');
		$body = $request->input('body');

		$post = $this->post->create(array(
			'topic_id' => $topic->id,
			'user_id' => Auth::id(),
			'content' => $body
		));

		$post->topic->touch();

		event(new UserRepliedToThread($request->user(), $post, $topic));

		return redirect($post->Route);
	}

	/**
	 * Watch a channel.
	 *
	 * @param Request $request
	 * @param Channel $channel
	 * @return Response
	 */
	public function watchChannel(Request $request, Channel $channel)
	{
		if (!$channel->canView($request->user())) abort(403);
		if ($channel->watchers->contains($request->user()->id)) return redirect()->back();

		$channel->watchers()->attach($request->user()->id);

		Flash::success('You are now watching the channel "' . $channel->name . '".');

		return redirect()->to($channel->Route);
	}

	/**
	 * Unwatch a channel.
	 *
	 * @param Request $request
	 * @param Channel $channel
	 * @return Response
	 */
	public function unwatchChannel(Request $request, Channel $channel)
	{
		if (!$channel->canView($request->user())) abort(403);
		if (!$channel->watchers->contains($request->user()->id)) return redirect()->back();

		$channel->removeWatcher($request->user());

		Flash::success('You are no longer watching the channel "' . $channel->name . '".');

		return redirect()->to($channel->Route);
	}

	/**
	 * Create a new forum page controller instance.
	 *
	 * @param Topic $topic
	 * @param Post $post
	 */
	public function __construct(Topic $topic, Post $post)
	{
		$this->middleware('auth');
		$this->middleware('confirmed');
		$this->middleware('csrf');
		$this->topic = $topic;
		$this->post = $post;
	}

}
