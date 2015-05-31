@extends('core.partials.layouts.master')
@section('title', 'Members')
{{-- Content here --}}
@section('content')
    <ol class="breadcrumb">
        <li><a href="{{{ route('home.show') }}}">Home</a></li>
        <li class="active">Members</li>
    </ol>
    <h1 class="page-header">Members</h1>
    @unless($users->count() == 0)
    <div class="row users">
    @foreach($pagination as $u)
        @include('core.partials.forum.member-card')
    @endforeach
    </div>
    @endunless

    @unless(!$users->count() == 0)
    <p>
        There are no users.
    </p>
    @endunless
    {!! $pagination->render() !!}
@endsection