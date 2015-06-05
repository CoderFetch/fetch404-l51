@extends('core.admin.layouts.default')
@section('title', 'Badges')
    {{-- Content here --}}
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h3>
                    <i class="fa fa-certificate"></i> Badges
                </h3>
            </div>
        </div>
    </div>

    <div class="row">
        @include('core.admin.partials.sidebar')
        <div class="col-md-9">
            @if ($badges->count() == 0)
            You have not created any badges.
            @else
            <ul class="list-group">
                @foreach($badges as $badge)
                <li class="list-group-item">
                    <a href="/admin/badges/{{{ $badge->id }}}/edit">
                        {{{ $badge->name }}}

                        @if ($badge->criteria()->count() == 0)
                        <i class="fa fa-exclamation-triangle text-danger"></i>
                        @endif
                    </a>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
@endsection