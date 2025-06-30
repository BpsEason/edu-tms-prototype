<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Material;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\MaterialRequest;
use Illuminate\Support\Facades\Gate;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Course $course, Chapter $chapter)
    {
        Gate::authorize('create-material', $chapter);
        return view('materials.create', compact('course', 'chapter'));
    }

    public function store(MaterialRequest $request, Course $course, Chapter $chapter)
    {
        Gate::authorize('create-material', $chapter);
        $data = $request->validated();
        
        if ($request->type === 'video' || $request->type === 'pdf') {
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('public/materials');
                $data['url'] = Storage::url($path);
            }
        }
        
        $chapter->materials()->create($data);

        return redirect()->route('courses.chapters.index', $course)->with('success', __('messages.material_created_successfully'));
    }

    public function show(Course $course, Chapter $chapter, Material $material)
    {
        // For students to view the material
        return view('materials.show', compact('course', 'chapter', 'material'));
    }

    public function edit(Course $course, Chapter $chapter, Material $material)
    {
        Gate::authorize('update-material', [$chapter, $material]);
        return view('materials.edit', compact('course', 'chapter', 'material'));
    }

    public function update(MaterialRequest $request, Course $course, Chapter $chapter, Material $material)
    {
        Gate::authorize('update-material', [$chapter, $material]);
        $data = $request->validated();

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($material->url && Storage::exists(str_replace('/storage', 'public', $material->url))) {
                Storage::delete(str_replace('/storage', 'public', $material->url));
            }
            $path = $request->file('file')->store('public/materials');
            $data['url'] = Storage::url($path);
        }

        $material->update($data);
        
        return redirect()->route('courses.chapters.index', $course)->with('success', __('messages.material_updated_successfully'));
    }

    public function destroy(Course $course, Chapter $chapter, Material $material)
    {
        Gate::authorize('delete-material', [$chapter, $material]);
        if ($material->url && Storage::exists(str_replace('/storage', 'public', $material->url))) {
            Storage::delete(str_replace('/storage', 'public', $material->url));
        }
        $material->delete();
        
        return redirect()->route('courses.chapters.index', $course)->with('success', __('messages.material_deleted_successfully'));
    }
}
