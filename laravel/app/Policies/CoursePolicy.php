<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    public function create(User $user)
    {
        return $user->hasRole('instructor') || $user->hasRole('hr');
    }

    public function update(User $user, Course $course)
    {
        return $user->hasRole('instructor');
    }

    public function delete(User $user, Course $course)
    {
        return $user->hasRole('instructor');
    }

    public function assign(User $user)
    {
        return $user->hasRole('instructor') || $user->hasRole('hr');
    }
    
    public function viewAny(User $user)
    {
        return true; // All authenticated users can view the course list.
    }
    
    public function view(User $user, Course $course)
    {
        return true; // All authenticated users can view a course.
    }
}
