<div class="col-md-3">
	<div class="well well-sm">
		<ul class="nav nav-pills nav-stacked">
			<li{{{ Request::is('admin') ? ' class=active' : '' }}}>
				<a href="{{{ route('admin.get.index') }}}"><i class="fa fa-list"></i> Overview</a>
			</li>
			<li{{{ Request::is('admin/general*') ? ' class=active' : '' }}}>
				<a href="/admin/general"><i class="fa fa-cog"></i> General Settings</a>
			</li>
			<li{{{ Request::is('admin/roles*') ? ' class=active' : '' }}}>
				<a href="/admin/roles"><i class="fa fa-users"></i> Roles</a>
			</li>
			<li{{{ Request::is('admin/users*') ? ' class=active' : '' }}}>
				<a href="/admin/users"><i class="fa fa-user"></i> Users</a>
			</li>
			<li{{{ Request::is('admin/forum*') ? ' class=active' : '' }}}>
				<a href="/admin/forum"><i class="fa fa-comments"></i> Forum</a>
			</li>
			<li{{{ Request::is('admin/badges*') ? ' class=active' : '' }}}>
				<a href="/admin/badges"><i class="fa fa-certificate"></i> Badges</a>
			</li>
		</ul>
	</div>
	<div class="well well-sm">
		<label>Users:</label> {{{ User::count() }}}
		<br />
		<label>Posts:</label> {{{ Post::count() }}}
		<br />
		<label>Topics:</label> {{{ Topic::count() }}}
	</div>
</div>