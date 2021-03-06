@extends('core.partials.layouts.master')
@section('title', 'Profile settings')
    {{-- Content here --}}
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h3>
                    Profile Settings
                </h3>
            </div>
        </div>
    </div>
    <div class="row">
        @include('core.user.partials.settings.sidebar')
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

            {!! Form::open(['route' => 'account.post.update.settings.profile', 'class' => 'form-horizontal', 'files' => true]) !!}
            <h3>Avatar</h3>
            <img src="{{{ Auth::user()->getAvatarURL(100) }}}" height="120" width="120" class="img-circle"/>
            <hr />
            <a class="btn btn-danger" href="{{{ route('account.get.delete.avatar') }}}">Delete Avatar</a>
            <hr>
            {!! Form::file('avatar') !!}
            <br />
            <div class="form-group">
                {!! Form::label('signature', 'Post signature') !!}
                {!! Form::textarea('signature', Auth::user()->getSetting('profile.posts.signature', null), ['class' => 'form-control', 'rows' => 3, 'data-type' => 'summernote']) !!}
            </div>
            <div class="form-group">
                {!! Form::submit('Save changes', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
