@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('messages.edit_chapter') }} for "{{ $course->title }}"</h2>
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">{{ __('messages.go_back') }}</a>
        </div>
        <div class="card-body">
            <form action="{{ route('courses.chapters.update', [$course, $chapter]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('messages.chapter_title') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $chapter->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="order" class="form-label">{{ __('messages.chapter_order') }}</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $chapter->order) }}" required>
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save_material') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
