<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Material;
use App\Models\Chapter;

class MaterialPolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }
    
    public function create(User $user, Chapter $chapter)
    {
        return $user->hasRole('instructor');
    }
    
    public function update(User $user, Chapter $chapter, Material $material)
    {
        return $user->hasRole('instructor');
    }

    public function delete(User $user, Chapter $chapter, Material $material)
    {
        return $user->hasRole('instructor');
    }
}
