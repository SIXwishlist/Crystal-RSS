@extends('layouts.app')

@section('title', trans('feed_manager.index.title'))

@section('content')
    <h1>
        {{ trans('feed_manager.index.title') }}
        <small class="text-muted">{{ $feeds->count() }}</small>
    </h1>

    <p>
        {{ Html::linkRoute('feed.manage.create', trans('feed_manager.create.title'), [], ['class' => 'btn btn-primary']) }}
    </p>

    @if ($feeds->count() == 0)
        <p class="lead">
            <i>{{ trans('feed_manager.index.no_feeds_yet') }}</i>
        </p>
    @else
        <table class="table table-striped" data-provide="tablesorter">
            <thead>
            <tr>
                <th>{{ trans('validation.attributes.title') }}</th>
                <th>{{ trans('validation.attributes.category_id') }}</th>
                <th>{{ trans('validation.attributes.last_checked_at') }}</th>
                <th class="sorter-false"></th>
                <th class="sorter-false"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($feeds->get() as $feed)
                <tr>
                    <td>{{ $feed->title }}</td>
                    <td>{{ $feed->category->title }}</td>
                    <td>{{ $feed->last_checked_at->format(DATETIME) }}</td>
                    <td>
                        @include('shared._delete_link', ['route' => ['feed.manage.destroy', $feed->id]])
                    </td>
                    <td>{{ Html::linkRoute('feed.manage.edit', trans('common.edit'), [$feed->id]) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection