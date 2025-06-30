@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('messages.edit_material') }} for "{{ $chapter->title }}"</h2>
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">{{ __('messages.go_back') }}</a>
        </div>
        <div class="card-body">
            <form action="{{ route('chapters.materials.update', [$course, $chapter, $material]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('messages.material_title') }}</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $material->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('messages.material_type') }}</label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">-- 請選擇類型 --</option>
                        <option value="pdf" {{ old('type', $material->type) == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="video" {{ old('type', $material->type) == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="url" {{ old('type', $material->type) == 'url' ? 'selected' : '' }}>URL</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3" id="file-upload-group">
                    <label for="file" class="form-label">{{ __('messages.file') }} (選填，更換檔案才需上傳)</label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($material->type !== 'url' && $material->url)
                        <small class="form-text text-muted">目前檔案: <a href="{{ $material->url }}" target="_blank">檢視</a></small>
                    @endif
                </div>
                <div class="mb-3" id="url-input-group" style="display:none;">
                    <label for="url" class="form-label">{{ __('messages.url') }}</label>
                    <input type="url" class="form-control @error('url') is-invalid @enderror" id="url" name="url" value="{{ old('url', $material->url) }}">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save_material') }}</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const fileGroup = document.getElementById('file-upload-group');
        const urlGroup = document.getElementById('url-input-group');
        
        function toggleInputs() {
            if (typeSelect.value === 'url') {
                fileGroup.style.display = 'none';
                urlGroup.style.display = 'block';
                document.getElementById('file').required = false;
                document.getElementById('url').required = true;
            } else if (typeSelect.value === 'pdf' || typeSelect.value === 'video') {
                fileGroup.style.display = 'block';
                urlGroup.style.display = 'none';
                document.getElementById('file').required = false; // Not required for update unless changed
                document.getElementById('url').required = false;
            } else {
                fileGroup.style.display = 'none';
                urlGroup.style.display = 'none';
                document.getElementById('file').required = false;
                document.getElementById('url').required = false;
            }
        }
        
        typeSelect.addEventListener('change', toggleInputs);
        toggleInputs(); // Initial call to set state based on old input
    });
</script>
@endpush
@endsection
