<div class="well well-sm">
    <h4>Statistics</h4>
    <hr>
    <label>Users Registered:</label> {{{ $users->count() }}}
    <br />
    <label>Latest User:</label> @if ($users->count() > 0) {!! link_to_route('profile.get.show', $latestUser->name, [$latestUser->slug, $latestUser->id]) !!}@else Nobody @endif
</div>