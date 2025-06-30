@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ __('messages.course_management') }}</h2>
        @can('create-course')
            <a href="{{ route('courses.create') }}" class="btn btn-success">{{ __('messages.create_course') }}</a>
        @endcan
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('messages.course_title') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.thumbnail') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $course)
                    <tr>
                        <td>{{ $course->id }}</td>
                        <td>{{ $course->title }}</td>
                        <td>{{ $course->category->name }}</td>
                        <td>
                            @if($course->thumbnail_url)
                                <img src="{{ $course->thumbnail_url }}" alt="Thumbnail" style="width: 100px; height: auto;">
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-info text-white me-1">{{ __('messages.view_details') }}</a>
                            @can('update-course', $course)
                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-primary me-1">{{ __('messages.edit') }}</a>
                            @endcan
                            @can('assign-course')
                                <a href="{{ route('courses.assign.form', $course) }}" class="btn btn-sm btn-warning text-white me-1">{{ __('messages.assign_training') }}</a>
                            @endcan
                            @can('delete-course', $course)
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline-block" onsubmit="return confirm('您確定要刪除這門課程嗎？這將無法復原。')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">目前沒有課程。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
