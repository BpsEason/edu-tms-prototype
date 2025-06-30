<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use App\Models\Group;
use App\Models\Assignment;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CourseRequest;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CourseReportExport;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $courses = Course::with('category')->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        Gate::authorize('create-course');
        $categories = Category::all();
        return view('courses.create', compact('categories'));
    }

    public function store(CourseRequest $request)
    {
        Gate::authorize('create-course');
        $data = $request->validated();
        
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('public/thumbnails');
            $data['thumbnail_url'] = Storage::url($path);
        }

        Course::create($data);

        return redirect()->route('courses.index')->with('success', __('messages.course_created_successfully'));
    }

    public function show(Course $course)
    {
        $course->load('chapters.materials');
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        Gate::authorize('update-course', $course);
        $categories = Category::all();
        return view('courses.edit', compact('course', 'categories'));
    }

    public function update(CourseRequest $request, Course $course)
    {
        Gate::authorize('update-course', $course);
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($course->thumbnail_url) {
                Storage::delete(str_replace('/storage', 'public', $course->thumbnail_url));
            }
            $path = $request->file('thumbnail')->store('public/thumbnails');
            $data['thumbnail_url'] = Storage::url($path);
        }

        $course->update($data);

        return redirect()->route('courses.index')->with('success', __('messages.course_updated_successfully'));
    }

    public function destroy(Course $course)
    {
        Gate::authorize('delete-course', $course);
        if ($course->thumbnail_url) {
            Storage::delete(str_replace('/storage', 'public', $course->thumbnail_url));
        }
        $course->delete();

        return redirect()->route('courses.index')->with('success', __('messages.course_deleted_successfully'));
    }

    public function showAssignForm(Course $course)
    {
        Gate::authorize('assign-course');
        $users = User::where('role', 'student')->get();
        $groups = Group::all();
        return view('courses.assign', compact('course', 'users', 'groups'));
    }

    public function assign(Request $request, Course $course)
    {
        Gate::authorize('assign-course');
        $request->validate([
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $userIds = $request->input('users', []);
        $groupIds = $request->input('groups', []);

        // Assign to individual users
        foreach ($userIds as $userId) {
            Assignment::updateOrCreate(
                ['user_id' => $userId, 'course_id' => $course->id],
                ['due_date' => $request->input('due_date')]
            );
        }

        // Assign to groups and all their members
        foreach ($groupIds as $groupId) {
            $group = Group::find($groupId);
            if ($group) {
                Assignment::updateOrCreate(
                    ['group_id' => $group->id, 'course_id' => $course->id],
                    ['due_date' => $request->input('due_date')]
                );
                // Assign to all members of the group
                foreach ($group->users as $user) {
                     Assignment::updateOrCreate(
                        ['user_id' => $user->id, 'course_id' => $course->id],
                        ['due_date' => $request->input('due_date')]
                    );
                }
            }
        }

        return redirect()->route('courses.index')->with('success', __('messages.assignment_successful'));
    }

    public function report(Request $request)
    {
        Gate::authorize('view-reports');

        // 1. Course Completion Rate
        $courseCompletion = Course::withCount(['assignments', 'progress' => function ($query) {
            $query->where('progress_percentage', 100);
        }])->get();
        
        $completionReport = $courseCompletion->map(function ($course) {
            $completionRate = $course->assignments_count > 0 ? ($course->progress_count / $course->assignments_count) * 100 : 0;
            return [
                'course' => $course->title,
                'completion_rate' => round($completionRate, 2),
            ];
        });

        // 2. Popularity (based on assignments)
        $popularCourses = Course::withCount('assignments')
                                ->orderBy('assignments_count', 'desc')
                                ->take(5)
                                ->get();
        $popularityReport = $popularCourses->map(fn($c) => ['course' => $c->title, 'assignments' => $c->assignments_count]);

        // 3. Student Activity (e.g., number of completed chapters per student over time)
        // This is a simplified example. A real-world report might track logins, material views, etc.
        $activityReport = DB::table('progress')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(distinct user_id) as active_students'))
            ->where('progress_percentage', '>', 0)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Export to CSV/Excel
        if ($request->has('export_csv')) {
            return Excel::download(new CourseReportExport($completionReport, $popularityReport, $activityReport), 'course_report.xlsx');
        }

        return view('courses.report', compact('completionReport', 'popularityReport', 'activityReport'));
    }
}
