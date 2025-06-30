@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('messages.dashboard') }}</h2>

    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('instructor') || Auth::user()->hasRole('hr'))
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">{{ __('messages.total_students') }}</h5>
                                <p class="card-text h2">{{ $totalStudents }}</p>
                            </div>
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">{{ __('messages.total_courses') }}</h5>
                                <p class="card-text h2">{{ $totalCourses }}</p>
                            </div>
                            <i class="fas fa-book-open fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">{{ __('messages.avg_student_progress') }}</h5>
                                <p class="card-text h2">{{ number_format($avgStudentProgress, 2) }}%</p>
                            </div>
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">{{ __('messages.avg_course_completion_rate') }}</h5>
                                <p class="card-text h2">{{ number_format($avgCourseCompletionRate, 2) }}%</p>
                            </div>
                            <i class="fas fa-tasks fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->hasRole('student'))
        <div class="card mb-4">
            <div class="card-header h4">{{ __('messages.my_courses') }}</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.course_name') }}</th>
                                <th>{{ __('messages.due_date') }}</th>
                                <th>{{ __('messages.progress') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($myCourses as $course)
                                <tr>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->due_date ? $course->due_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $course->progress_percentage }}%;" aria-valuenow="{{ $course->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ round($course->progress_percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-primary">{{ __('messages.view_details') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">您目前沒有指派的課程。</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
