<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Progress;
use App\Models\Chapter;
use App\Models\Course;

class ProgressController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'progress_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        $chapter = Chapter::findOrFail($request->chapter_id);
        $courseId = $chapter->course_id;

        // Update progress for the specific chapter
        Progress::updateOrCreate(
            [
                'user_id' => $user->id,
                'chapter_id' => $request->chapter_id,
            ],
            [
                'course_id' => $courseId,
                'progress_percentage' => $request->progress_percentage
            ]
        );

        // Recalculate total course progress
        $course = Course::findOrFail($courseId);
        $totalChapters = $course->chapters()->count();
        if ($totalChapters > 0) {
            $completedChapters = $user->progress()->where('course_id', $courseId)->where('progress_percentage', 100)->count();
            $courseProgress = ($completedChapters / $totalChapters) * 100;
            
            // This is a simplified logic. A more complex system might track progress per material.
            // Here, we just update a general course progress record.
            Progress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                    'chapter_id' => null, // Represents overall course progress
                ],
                ['progress_percentage' => $courseProgress]
            );
        }

        return response()->json(['message' => '進度更新成功！', 'course_progress' => $courseProgress ?? 0]);
    }
}
