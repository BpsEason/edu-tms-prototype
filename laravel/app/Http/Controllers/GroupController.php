<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Gate::authorize('manage-groups');
        $groups = Group::with('users')->get();
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        Gate::authorize('manage-groups');
        $students = User::where('role', 'student')->get();
        return view('groups.create', compact('students'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-groups');
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        
        $group = Group::create($request->only('name'));
        $group->users()->sync($request->input('user_ids', []));

        return redirect()->route('groups.index')->with('success', '群組建立成功。');
    }

    public function edit(Group $group)
    {
        Gate::authorize('manage-groups');
        $students = User::where('role', 'student')->get();
        return view('groups.edit', compact('group', 'students'));
    }

    public function update(Request $request, Group $group)
    {
        Gate::authorize('manage-groups');
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group->update($request->only('name'));
        $group->users()->sync($request->input('user_ids', []));

        return redirect()->route('groups.index')->with('success', '群組更新成功。');
    }

    public function destroy(Group $group)
    {
        Gate::authorize('manage-groups');
        $group->users()->detach(); // Detach users from the group first
        $group->delete();
        return redirect()->route('groups.index')->with('success', '群組已刪除。');
    }
}
