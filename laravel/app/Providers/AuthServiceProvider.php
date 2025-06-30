<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Policies\CoursePolicy;
use App\Models\Chapter;
use App\Policies\ChapterPolicy;
use App\Models\Material;
use App\Policies\MaterialPolicy;
use App\Models\Group;
use App\Policies\GroupPolicy;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Course::class => CoursePolicy::class,
        Chapter::class => ChapterPolicy::class,
        Material::class => MaterialPolicy::class,
        Group::class => GroupPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        
        // Define Gates for specific permissions
        Gate::define('create-course', [CoursePolicy::class, 'create']);
        Gate::define('update-course', [CoursePolicy::class, 'update']);
        Gate::define('delete-course', [CoursePolicy::class, 'delete']);
        Gate::define('assign-course', [CoursePolicy::class, 'assign']);
        
        Gate::define('create-chapter', [ChapterPolicy::class, 'create']);
        Gate::define('update-chapter', [ChapterPolicy::class, 'update']);
        Gate::define('delete-chapter', [ChapterPolicy::class, 'delete']);

        Gate::define('create-material', [MaterialPolicy::class, 'create']);
        Gate::define('update-material', [MaterialPolicy::class, 'update']);
        Gate::define('delete-material', [MaterialPolicy::class, 'delete']);

        Gate::define('manage-groups', [GroupPolicy::class, 'manageGroups']);
        
        Gate::define('view-reports', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('instructor') || $user->hasRole('hr');
        });
    }
}
