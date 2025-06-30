<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Progress;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Admin/HR/Instructor KPI data
        $totalStudents = 0;
        $totalCourses = 0;
        $avgStudentProgress = 0;
        $avgCourseCompletionRate = 0;

        if ($user->role === 'admin' || $user->role === 'instructor' || $user->role === 'hr') {
            $totalStudents = User::where('role', 'student')->count();
            $totalCourses = Course::count();
            
            // Calculate average student progress based on all courses assigned
            $totalProgress = Progress::sum('progress_percentage');
            $totalProgressRecords = Progress::count();
            $avgStudentProgress = $totalProgressRecords > 0 ? $totalProgress / $totalProgressRecords : 0;

            // Calculate average course completion rate
            $completedCourses = Progress::where('progress_percentage', 100)->count();
            $totalAssignments = \App\Models\Assignment::count();
            $avgCourseCompletionRate = $totalAssignments > 0 ? ($completedCourses / $totalAssignments) * 100 : 0;
        }

        // Student's progress data
        $myCourses = collect();
        if ($user->role === 'student') {
            $myCourses = $user->assignments()->with('course.chapters.materials', 'progress')->get()->map(function($assignment) {
                $course = $assignment->course;
                $progress = $course->progress()->where('user_id', auth()->id())->first();
                $course->progress_percentage = $progress ? $progress->progress_percentage : 0;
                $course->due_date = $assignment->due_date;
                return $course;
            });
        }
        
        return view('dashboard', compact('user', 'totalStudents', 'totalCourses', 'avgStudentProgress', 'avgCourseCompletionRate', 'myCourses'));
    }
}
