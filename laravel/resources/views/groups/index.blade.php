@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ __('messages.group_management') }}</h2>
        <a href="{{ route('groups.create') }}" class="btn btn-success">{{ __('messages.create_group') }}</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('messages.group_name') }}</th>
                    <th>{{ __('messages.members') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groups as $group)
                    <tr>
                        <td>{{ $group->id }}</td>
                        <td>{{ $group->name }}</td>
                        <td>
                            @forelse($group->users as $user)
                                <span class="badge bg-primary">{{ $user->name }}</span>
                            @empty
                                <span class="badge bg-secondary">無成員</span>
                            @endforelse
                        </td>
                        <td>
                            <a href="{{ route('groups.edit', $group) }}" class="btn btn-sm btn-primary me-1">{{ __('messages.edit') }}</a>
                            <form action="{{ route('groups.destroy', $group) }}" method="POST" class="d-inline-block" onsubmit="return confirm('您確定要刪除這個群組嗎？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">目前沒有任何群組。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
