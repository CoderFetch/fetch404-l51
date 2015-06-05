@extends('core.partials.layouts.master')

@section('title', $channel->name)
@section('content')
	<ol class="breadcrumb">
		<li><a href="/">Home</a></li>
		<li><a href="/forum">Forum</a></li>
		<li><a href="{{{ $channel->category->Route }}}">{{{ $channel->category->name }}}</a></li>
		<li class="active"><a href="{{{ $channel->Route }}}">{{{ $channel->name }}}</a></li>
	</ol>
	<br />
	<div class="row">
		<div class="col-lg-7">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="pull-right">
						@if ($channel->can(1, Auth::user()))
						<a class="btn btn-success btn-xs" href="{{{ route('forum.get.channel.create.thread', [$channel->id]) }}}"><i class="fa fa-pencil"></i> Create thread</a>
						@endif
						@if (Auth::check())
						@if (!$channel->watchers->contains(Auth::id()))
						<a class="btn btn-info btn-xs" href="{{{ route('forum.get.watch.channel', [$channel->id]) }}}"><i class="fa fa-eye"></i> Watch channel</a>
						@else
						<a class="btn btn-info btn-xs" href="{{{ route('forum.get.unwatch.channel', [$channel->id]) }}}"><i class="fa fa-eye-slash"></i> Unwatch channel</a>
						@endif
						@endif
					</div>
					<h3 class="panel-title">
						{{{ $channel->name }}}
					</h3>
				</div>
				<div class="panel-body">
					@if (!$channel->topicsPaginated->isEmpty())
						@foreach($channel->topicsPaginated as $i => $thread)
						<span{{{ $thread->pinned == 1  }}}>
							<span class="fa-stack pull-left">
								<i class="fa fa-comment fa-stack-2x"></i>
								@if($thread->userReadStatus == 'unread')
									<i class="fa fa-exclamation fa-stack-1x" style="color: black;"></i>
								@endif
							</span>
							&nbsp;
							<a href="{{{ $thread->Route }}}">
								@if ($thread->userReadStatus == 'unread')
								<span style="font-weight: bold;">{{{ $thread->title }}}</span>
								@else
								{{{ $thread->title }}}
								@endif
							</a>
							<span class="text-muted">- by {!! link_to_route('profile.get.show', $thread->user->name, [$thread->user->slug, $thread->user->id]) !!}</span>
							<span class="pull-right">
							@if ($thread->isLocked())
								<i class="fa fa-lock" data-type="tooltip" data-original-title="Locked"></i>
							@endif
							@if ($thread->isPinned())
								<i class="fa fa-thumb-tack" data-type="tooltip" data-original-title="Pinned"></i>
							@endif
							{{{ $thread->replyCount }}} {{{ Pluralizer::plural('reply', $thread->replyCount) }}}
							</span>
						</span>
						@if ($i != sizeof($channel->topicsPaginated) - 1)
						<hr>
						@endif
						@endforeach
					@else
						<p>Nobody has created a thread.</p>
					@endif
				</div>
			</div>
			@if ($channel->hasPages)
				{!! $channel->pageLinks !!}
			@endif
		</div>

		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-list-alt fa-fw"></i> Stats</h3>
				</div>
				<div class="panel-body">
					<label>Discussions:</label> {{{ sizeof($channel->topics) }}}
					<br>
					<label>Posts:</label> {{{ sizeof($channel->posts) }}}
				</div>
			</div>
		</div>
	</div>
@stop

@section('jquery')
	@if (Auth::check() && $channel->watchers->contains(Auth::id()))
	var channel = pusher.subscribe('channel-{{{ $channel->id }}}');

	channel.bind('Fetch404\Core\Events\ThreadWasCreated', function(data) {
		console.log("hi", data);
	});
	@endif
@stop