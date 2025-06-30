<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\ChapterRequest;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Course $course)
    {
        $chapters = $course->chapters()->with('materials')->orderBy('order')->get();
        return view('chapters.index', compact('course', 'chapters'));
    }
    
    public function create(Course $course)
    {
        Gate::authorize('create-chapter', $course);
        return view('chapters.create', compact('course'));
    }

    public function store(ChapterRequest $request, Course $course)
    {
        Gate::authorize('create-chapter', $course);
        $chapter = $course->chapters()->create($request->validated());
        return redirect()->route('courses.chapters.index', $course)->with('success', __('messages.chapter_created_successfully'));
    }

    public function show(Course $course, Chapter $chapter)
    {
        $chapter->load('materials');
        return view('chapters.show', compact('course', 'chapter'));
    }

    public function edit(Course $course, Chapter $chapter)
    {
        Gate::authorize('update-chapter', [$course, $chapter]);
        return view('chapters.edit', compact('course', 'chapter'));
    }

    public function update(ChapterRequest $request, Course $course, Chapter $chapter)
    {
        Gate::authorize('update-chapter', [$course, $chapter]);
        $chapter->update($request->validated());
        return redirect()->route('courses.chapters.index', $course)->with('success', __('messages.chapter_updated_successfully'));
    }

    public function destroy(Course $course, Chapter $chapter)
    {
        Gate::authorize('delete-chapter', [$course, $chapter]);
        $chapter->delete();
        return redirect()->route('courses.chapters.index', $course)->with('success', __('messages.chapter_deleted_successfully'));
    }
}
