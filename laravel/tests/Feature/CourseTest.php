<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed necessary data
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_admin_can_view_course_management_page()
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/courses');
        $response->assertStatus(200);
        $response->assertSee('課程管理');
    }
    
    public function test_instructor_can_view_course_management_page()
    {
        $instructor = User::where('role', 'instructor')->first();
        $response = $this->actingAs($instructor)->get('/courses');
        $response->assertStatus(200);
        $response->assertSee('課程管理');
    }
    
    public function test_student_cannot_view_course_management_page()
    {
        $student = User::where('role', 'student')->first();
        $response = $this->actingAs($student)->get('/courses');
        $response->assertStatus(403); // Forbidden
    }
    
    public function test_admin_can_create_a_course()
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->first();
        $category = Category::factory()->create();
        
        $response = $this->actingAs($admin)->post('/courses', [
            'title' => 'New Test Course',
            'description' => 'A description for the new course.',
            'category_id' => $category->id,
            'thumbnail' => UploadedFile::fake()->image('test-thumbnail.jpg'),
        ]);
        
        $response->assertRedirect('/courses');
        $this->assertDatabaseHas('courses', ['title' => 'New Test Course']);
        Storage::disk('public')->assertExists('thumbnails/test-thumbnail.jpg');
    }

    public function test_instructor_can_update_a_course()
    {
        Storage::fake('public');
        $instructor = User::where('role', 'instructor')->first();
        $course = Course::factory()->create();
        $newCategory = Category::factory()->create();
        
        $response = $this->actingAs($instructor)->put('/courses/' . $course->id, [
            'title' => 'Updated Course Title',
            'description' => 'Updated description.',
            'category_id' => $newCategory->id,
        ]);
        
        $response->assertRedirect('/courses');
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'title' => 'Updated Course Title', 'category_id' => $newCategory->id]);
    }
    
    public function test_instructor_can_delete_a_course()
    {
        $instructor = User::where('role', 'instructor')->first();
        $course = Course::factory()->create();
        
        $response = $this->actingAs($instructor)->delete('/courses/' . $course->id);
        
        $response->assertRedirect('/courses');
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
