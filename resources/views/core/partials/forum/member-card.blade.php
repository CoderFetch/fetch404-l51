<div class="col-md-3">
    <div class="panel panel-default">
        <div class="panel-body">
            <a href="{{{ $u->profileURL }}}">
                <img class="media-object" src="{{ $u->getAvatarURL(40) }}" alt="{{ $u->name }}" height="120" width="120">
            </a>
            <h4>
                {!! link_to($u->profileURL, $u->name) !!}
            </h4>
            <hr>
            <i class="fa fa-circle text-{{{ $u->is_online == 1 ? 'success' : 'danger' }}}"></i>

            <span class="text-{{{ $u->is_online == 1 ? 'success' : 'danger' }}}">{{{ $u->is_online == 1 ? 'Online' : 'Offline' }}}</span>
            <br>
            <span class="text-muted">
                Joined <strong>{{{ $u->getJoinedOn() }}}</strong>
            </span>
            <br>
            <span class="text-muted">
                Last active <strong>{{{ $u->getLastActivity() }}}</strong>
            </span>
            <br>
            <small>
                Posts: <strong>{{{ $u->posts()->count() }}}</strong>
            </small>
            |
            <small>
                Threads: <strong>{{{ $u->topics()->count() }}}</strong>
            </small>
            |
            <small>
                Likes:
                @if ($u->likesReceived()->count() > 0)
                <strong class="text-success">
                    {{{ $u->likesReceived()->count() }}}
                </strong>
                @else
                <strong>
                    0
                </strong>
                @endif
            </small>
        </div>
    </div>
</div>