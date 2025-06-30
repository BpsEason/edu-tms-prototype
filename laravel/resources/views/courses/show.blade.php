@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ __('messages.course_details') }}: {{ $course->title }}</h2>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ __('messages.go_back') }}</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($course->thumbnail_url)
                        <img src="{{ $course->thumbnail_url }}" class="img-fluid rounded-start mb-3" alt="Course Thumbnail">
                    @else
                        <img src="https://via.placeholder.com/400x300.png?text=No+Image" class="img-fluid rounded-start mb-3" alt="No Image">
                    @endif
                    <h5 class="card-title">{{ $course->title }}</h5>
                    <p class="card-text text-muted">{{ __('messages.category') }}: {{ $course->category->name }}</p>
                    <p class="card-text">{{ $course->description }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('messages.chapter_management') }}</h4>
                    @can('create-chapter', $course)
                        <a href="{{ route('courses.chapters.create', $course) }}" class="btn btn-success btn-sm">{{ __('messages.create_chapter') }}</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="accordion" id="chaptersAccordion">
                        @forelse ($course->chapters as $chapter)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $chapter->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $chapter->id }}" aria-expanded="false" aria-controls="collapse{{ $chapter->id }}">
                                        {{ __('messages.chapter') }} {{ $chapter->order }}: {{ $chapter->title }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $chapter->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $chapter->id }}" data-bs-parent="#chaptersAccordion">
                                    <div class="accordion-body">
                                        <div class="d-flex justify-content-end mb-2">
                                            @can('update-chapter', [$course, $chapter])
                                                <a href="{{ route('courses.chapters.edit', [$course, $chapter]) }}" class="btn btn-sm btn-outline-primary me-2">{{ __('messages.edit') }}</a>
                                            @endcan
                                            @can('delete-chapter', [$course, $chapter])
                                                <form action="{{ route('courses.chapters.destroy', [$course, $chapter]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('確定要刪除此章節嗎？')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                                                </form>
                                            @endcan
                                        </div>
                                        <h5 class="mt-3">{{ __('messages.materials') }}</h5>
                                        <ul class="list-group">
                                            @forelse ($chapter->materials as $material)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-{{ $material->type == 'video' ? 'video' : ($material->type == 'pdf' ? 'file-pdf' : 'link') }} me-2"></i>
                                                        <a href="{{ $material->url }}" target="_blank" class="text-decoration-none">{{ $material->title }} ({{ $material->type }})</a>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('chapters.materials.edit', [$course, $chapter, $material]) }}" class="btn btn-sm btn-secondary me-1">{{ __('messages.edit') }}</a>
                                                        <form action="{{ route('chapters.materials.destroy', [$course, $chapter, $material]) }}" method="POST" class="d-inline-block" onsubmit="return confirm('確定要刪除此教材嗎？')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                                                        </form>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item">此章節目前沒有教材。
                                                    @can('create-material', $chapter)
                                                        <a href="{{ route('chapters.materials.create', [$course, $chapter]) }}" class="btn btn-link btn-sm p-0">{{ __('messages.create_material') }}</a>
                                                    @endcan
                                                </li>
                                            @endforelse
                                        </ul>
                                        @can('create-material', $chapter)
                                            <a href="{{ route('chapters.materials.create', [$course, $chapter]) }}" class="btn btn-sm btn-primary mt-3">{{ __('messages.create_material') }}</a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info" role="alert">
                                此課程目前沒有任何章節。
                                @can('create-chapter', $course)
                                    <a href="{{ route('courses.chapters.create', $course) }}" class="btn btn-link btn-sm p-0">{{ __('messages.create_chapter') }}</a>
                                @endcan
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
