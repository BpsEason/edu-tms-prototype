@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ __('messages.create_group') }}</h2>
            <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">{{ __('messages.go_back') }}</a>
        </div>
        <div class="card-body">
            <form action="{{ route('groups.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('messages.group_name') }}</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="user_ids" class="form-label">{{ __('messages.select_members') }}</label>
                    <select name="user_ids[]" id="user_ids" class="form-select" multiple>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ in_array($student->id, old('user_ids', [])) ? 'selected' : '' }}>{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>
                    <div class="form-text">按住 Ctrl/Cmd 可選擇多個成員。</div>
                    @error('user_ids')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">{{ __('messages.add_group') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
