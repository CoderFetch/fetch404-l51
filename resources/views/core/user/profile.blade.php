@extends('core.partials.layouts.master')

@section('title', $user->name . '\'s profile')

@section('extra_tags')
@if ($user->getSetting("allow_bots_to_index_me", true) == false)
<meta name="robots" content="noindex">
@endif
@stop
@section('content')
	<ol class="breadcrumb">
		<li><a href="/">Home</a></li>
		<li><a href="{{{ route('members.get.index') }}}">Members</a></li>
		<li class="active"><a href="{{{ $user->profileURL }}}">{{{ $user->name }}}</a></li>
	</ol>
	<div class="row">
		<div class="col-md-3" style="border-right: 1px solid lightgray;">
			<img src="{{{ $user->getAvatarURL() }}}" height="120" width="120" />
			<br>
			<br>
			<div class="panel panel-primary">
				<div class="panel-heading">
					Stats
				</div>
				<div class="panel-body">
					<label>Joined:</label> {{{ $user->getJoinedOn() }}}
					<br>
					<label>Last Activity:</label> {{{ $user->getLastActivity() }}}
					<br>
					<label>Messages: </label> {{{ $user->posts()->count() }}}
					<br>
					<label>Likes Received: </label> {{{ $user->likesReceived()->count() }}}
				</div>
			</div>
			<div class="panel panel-primary">
				<div class="panel-heading">
					Following
				</div>
				<div class="panel-body">
					@if ($user->followedUsers->isEmpty())
					<p>
						This user is following nobody.
					</p>
					@else
					@foreach($user->followedUsers as $followedUser)
					<a href="{{{ $followedUser->profileURL }}}">
						<img src="{{{ $followedUser->getAvatarURL(35) }}}" height="35" width="30" style="box-shadow: 0 0 1px 1px silver;" data-type="tooltip" data-original-title="{{{ $followedUser->getName() }}}" />
					</a>
					@endforeach
					@endif
				</div>
			</div>
			<div class="panel panel-primary">
				<div class="panel-heading">
					Followers
				</div>
				<div class="panel-body">
					@if ($user->followers->isEmpty())
					<p>
						Nobody is following this user.
					</p>
					@else
					@foreach($user->followers as $follower)
					<a href="{{{ $follower->profileURL }}}">
						<img src="{{{ $follower->getAvatarURL(35) }}}" height="35" width="30" style="box-shadow: 0 0 1px 1px silver;" data-type="tooltip" data-original-title="{{{ $follower->getName() }}}" />
					</a>
					@endforeach
					@endif
				</div>
			</div>
		</div>
		<div class="col-lg-9">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h2 class="panel-title">
						<div class="pull-right">
							@if (Auth::check() && Auth::id() != $user->id)
							@if (!Auth::user()->isFollowing($user))
							{!! Form::open(['route' => array('user.post.follow', $user)]) !!}
							{!! Form::submit('Follow', ['class' => 'btn btn-info btn-xs']) !!}
							{!! Form::close() !!}
							@else
							{!! Form::open(['route' => array('user.post.unfollow', $user)]) !!}
							{!! Form::submit('Unfollow', ['class' => 'btn btn-danger btn-xs']) !!}
							{!! Form::close() !!}
							@endif
							@endunless
						</div>
						{{{ $user->name }}}
						@foreach($user->roles as $role)
						<span class="label label-{{{ $role->is_superuser == 1 ? 'danger' : 'success' }}}">
							{{{ $role->name }}}
						</span>
						@endforeach
						&nbsp;
						<small class="last-activity">
							Last active
							<span {!! $user->last_active_desc != null ? 'data-type=tooltip data-original-title=\'' . $user->getLastActiveDesc() . '\'' : '' !!}>
								{{{ $user->getLastActivity() }}}
							</span>
						</small>
					</h2>
				</div>
				<div class="panel-body">
					<div role="tabpanel">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#profile-posts" aria-controls="profile-posts" role="tab" data-toggle="tab">Profile posts</a></li>
							<li role="presentation"><a href="#postings" aria-controls="postings" role="tab" data-toggle="tab">Postings</a></li>
							<li role="presentation"><a href="#information" aria-controls="information" role="tab" data-toggle="tab">Information</a></li>
							<li role="presentation"><a href="#badges" aria-controls="badges" role="tab" data-toggle="tab">Badges</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="profile-posts">
								@if (Auth::check() && Auth::user()->isConfirmed())
								<div class="status-post">
									{!! Form::open(['route' => array('user.profile-posts.post.create', $user)]) !!}
									<!-- Status Form Input -->
									<div class="form-group{{{ $errors->has('body') ? ' has-error' : '' }}}">
										{!! Form::textarea('body', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => $user->isUser(Auth::user()) ? "What's on your mind?" : "Post something..."]) !!}
										@if ($errors->has('body'))
										<span class="text-danger">{{{ $errors->first('body') }}}</span>
										@endif
										<div class="status-post-submit">
											{!! Form::submit('Post', ['class' => 'btn btn-primary btn-sm']) !!}
										</div>
									</div>

									{!! Form::close() !!}
								</div>
								@endif
								@if ($user->profilePosts->isEmpty())
								<p>Nobody has written anything on this user's profile.</p>
								@else
								@foreach($user->profilePosts as $profilePost)
								@include('core.user.partials.profile.profile-post', array('profilePost' => $profilePost))
								@endforeach
								@endif
							</div>
							<div role="tabpanel" class="tab-pane fade" id="postings">
								Coming soon
							</div>
							<div role="tabpanel" class="tab-pane fade" id="information">
								Coming soon
							</div>
							<div role="tabpanel" class="tab-pane fade" id="badges">
								<h3>Badges ({{{ $user->badges()->count() }}})</h3>
								<hr>
								@if ($user->badges()->count() == 0)
								This user has not received any badges.
								@else
								<ul>
									@foreach($user->badges as $badge)
									{{{ $badge->name }}}
									- <small>{{{ str_limit($badge->description, 45) }}}</small>
									@endforeach
								</ul>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop