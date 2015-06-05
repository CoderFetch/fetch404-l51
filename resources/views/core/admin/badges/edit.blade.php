@extends('core.admin.layouts.default')
@section('title', 'Title')
{{-- Content here --}}
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h3>
                    Editing badge
                </h3>
            </div>
        </div>
    </div>

    <div class="row">
        @include('core.admin.partials.sidebar')
        <div class="col-md-9">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {!! Form::open(['route' => array('admin.badges.post.edit', $badge->id)]) !!}
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Badge Name" value="{{{ $badge->name }}}">
            </div>
            <hr>
            <h3>Criteria</h3>
            <hr>

            <div class="form-group">
                <label for="userType">Give this to...</label>
                <select name="userType" class="form-control">
                    <option value="user"{{{ $badge->criteria()->whereUserType('user')->count() == 1 ? ' selected=selected' : '' }}}>Users</option>
                </select>
            </div>
            <hr>
            <div class="form-group">
                <label for="milestone_type">Milestone type</label>
                <select name="milestone_type" class="form-control">
                    <option value="post"{{{ $badge->criteria()->whereTriggerType('post')->count() == 1 ? ' selected=selected' : '' }}}>Certain amount of posts</option>
                    <option value="registration"{{{ $badge->criteria()->whereTriggerType('registration')->count() == 1 ? ' selected=selected' : '' }}}>Registration</option>
                    <option value="likes"{{{ $badge->criteria()->whereTriggerType('likes')->count() == 1 ? ' selected=selected' : '' }}}>Certain amount of likes</option>
                </select>
            </div>
            <hr>
            <div class="form-group">
                <label for="milestone_requirement">Milestone requirement</label>
                <input type="text" name="milestone_requirement" class="form-control" />
                <span class="help-block">* Note: This is only needed if you choose the "Certain amount of posts" or "Certain amount of likes" options.</span>
            </div>

            {!! Form::submit('Save changes', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection