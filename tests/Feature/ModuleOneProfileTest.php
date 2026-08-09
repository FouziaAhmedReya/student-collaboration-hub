<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\Skill;
use App\Models\Interest;
use App\Models\Project;
use App\Models\PortfolioLink;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ModuleOneProfileTest extends TestCase
{
    use DatabaseMigrations;

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_student_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student@bracu.ac.bd',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'student@bracu.ac.bd']);
    }

    public function test_student_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_student_can_view_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_student_can_update_profile_info()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Name',
            'department' => 'CSE',
            'semester' => '10th Semester',
            'university' => 'BRAC University',
            'about_me' => 'Updated bio content',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'department' => 'CSE',
            'university' => 'BRAC University',
        ]);
    }

    public function test_student_can_add_edit_delete_skill()
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create();

        // Add
        $response = $this->actingAs($user)->post('/skills', [
            'name' => 'Laravel',
            'proficiency' => 90,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('skills', ['profile_id' => $profile->id, 'name' => 'Laravel', 'proficiency' => 90]);

        $skill = Skill::where('profile_id', $profile->id)->first();

        // Edit
        $response = $this->actingAs($user)->put("/skills/{$skill->id}", [
            'name' => 'Laravel Framework',
            'proficiency' => 95,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'Laravel Framework', 'proficiency' => 95]);

        // Delete
        $response = $this->actingAs($user)->delete("/skills/{$skill->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_student_can_add_delete_interest()
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create();

        // Add
        $response = $this->actingAs($user)->post('/interests', [
            'name' => 'Machine Learning',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('interests', ['profile_id' => $profile->id, 'name' => 'Machine Learning']);

        $interest = Interest::where('profile_id', $profile->id)->first();

        // Delete
        $response = $this->actingAs($user)->delete("/interests/{$interest->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('interests', ['id' => $interest->id]);
    }

    public function test_student_can_add_edit_delete_project()
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create();

        // Add
        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Collaboration Hub',
            'description' => 'Full Stack Web App',
            'technologies' => 'Laravel, MySQL',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['profile_id' => $profile->id, 'name' => 'Collaboration Hub']);

        $project = Project::where('profile_id', $profile->id)->first();

        // Edit
        $response = $this->actingAs($user)->put("/projects/{$project->id}", [
            'name' => 'Updated Project Name',
            'description' => 'Updated Description',
            'technologies' => 'Laravel, Vue.js',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Project Name']);

        // Delete
        $response = $this->actingAs($user)->delete("/projects/{$project->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_student_can_add_edit_delete_portfolio_link()
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create();

        // Add
        $response = $this->actingAs($user)->post('/portfolio-links', [
            'platform' => 'GitHub',
            'url' => 'https://github.com/testuser',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('portfolio_links', ['profile_id' => $profile->id, 'platform' => 'GitHub']);

        $link = PortfolioLink::where('profile_id', $profile->id)->first();

        // Edit
        $response = $this->actingAs($user)->put("/portfolio-links/{$link->id}", [
            'platform' => 'GitHub Pro',
            'url' => 'https://github.com/updateduser',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('portfolio_links', ['id' => $link->id, 'platform' => 'GitHub Pro']);

        // Delete
        $response = $this->actingAs($user)->delete("/portfolio-links/{$link->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('portfolio_links', ['id' => $link->id]);
    }

    public function test_student_can_update_study_location()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/profile/location', [
            'preferred_location_name' => 'BRAC University Library',
            'preferred_location_address' => 'Mohakhali, Dhaka',
            'latitude' => 23.7806,
            'longitude' => 90.4068,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'preferred_location_name' => 'BRAC University Library',
            'latitude' => 23.7806,
        ]);
    }

    public function test_profile_completion_percentage_calculation()
    {
        $user = User::factory()->create();
        $profile = $user->profile()->create([
            'department' => 'CSE',
            'semester' => '10th Semester',
            'university' => 'BRAC University',
            'about_me' => 'Sample bio',
            'latitude' => 23.7806,
        ]);

        $profile->skills()->create(['name' => 'PHP', 'proficiency' => 80]);
        $profile->interests()->create(['name' => 'AI']);
        $profile->projects()->create(['name' => 'Chatbot']);
        $profile->portfolioLinks()->create(['platform' => 'GitHub', 'url' => 'https://github.com/user']);

        $percentage = $profile->getCompletionPercentage();
        $this->assertGreaterThanOrEqual(80, $percentage);
    }
}
