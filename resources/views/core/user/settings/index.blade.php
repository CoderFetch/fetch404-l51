@extends('core.partials.layouts.master')

@section('title', 'Account settings')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h3>
                    General Settings
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

            {!! Form::open(['route' => 'account.post.update.settings', 'class' => 'form-horizontal']) !!}
            <div class="form-group">
                <label for="name">Username: </label>
                <input class="form-control" type="text" name="name" value="{{{ Auth::user()->name }}}" />
            </div>

            @if (Auth::user()->isConfirmed())
                <div class="form-group">
                    <label for="name">Email: </label>
                    <input class="form-control" type="text" name="email" value="{{{ Auth::user()->email }}}" />
                </div>
            @else
                <div class="form-group">
                    <label style="font-weight: 400;">Before changing your email, you must confirm your account.</label>
                </div>
            @endif

            <div class="form-group">
                <label for="password">Password: </label>
                <input class="form-control" name="password" type="password" value="" id="password">
                <small class="help-block">* Enter your password in this box and re-enter it in the second.</small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm your password:</label>
                <input class="form-control" name="password_confirmation" type="password" value="" id="password_confirmation">
            </div>

            <div class="form-group">
                {!! Form::submit('Save changes', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop