<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Material;
use App\Models\Group;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users with different roles
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'role' => 'instructor',
        ]);
        User::factory()->create([
            'name' => 'HR User',
            'email' => 'hr@example.com',
            'role' => 'hr',
        ]);
        User::factory(10)->create(['role' => 'student']);

        // Create Categories
        $categories = ['IT', 'Sales', 'Marketing', 'HR'];
        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
        
        // Create some courses with chapters and materials
        $courses = Course::factory(5)->create();
        $categories = Category::all();

        foreach ($courses as $course) {
            $course->category_id = $categories->random()->id;
            $course->save();
            
            $chapters = Chapter::factory(5)->create(['course_id' => $course->id]);
            foreach ($chapters as $chapter) {
                Material::factory(3)->create(['chapter_id' => $chapter->id]);
            }
        }
        
        // Create some groups and assign students
        $groups = Group::factory(3)->create();
        $students = User::where('role', 'student')->get();
        foreach ($groups as $group) {
            $group->users()->attach($students->random(rand(2, 5))->pluck('id'));
        }
    }
}
