<?php

namespace Tests\Feature;

use App\Models\DepartmentInterest;
use App\Models\Interest;
use App\Models\PortfolioLink;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\StudentProject;
use App\Models\StudyGroup;
use App\Models\StudyGroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSkillManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_index_renders_successfully(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('profile.index'));
        $response->assertStatus(200);
        $response->assertViewIs('modules.rayhan.profile-skills.index');
        $response->assertSee($user->name);
    }

    public function test_profile_edit_renders_successfully(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertViewIs('modules.rayhan.profile-skills.edit');
    }

    public function test_profile_update(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);
        $this->actingAs($user);

        $response = $this->put(route('profile.update'), [
            'name' => 'Updated Name',
            'department' => 'Software Engineering',
            'semester' => 'Fall 2026',
            'university' => 'University of Dhaka',
            'phone' => '+880 1711-223344',
            'bio' => 'Updated bio description',
            'preferred_location_name' => 'Library Quiet Zone',
            'preferred_location_address' => 'Building 2, Level 3',
            'latitude' => 23.780000,
            'longitude' => 90.400000,
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'department' => 'Software Engineering',
            'semester' => 'Fall 2026',
            'university' => 'University of Dhaka',
            'phone' => '+880 1711-223344',
            'bio' => 'Updated bio description',
            'preferred_location_name' => 'Library Quiet Zone',
        ]);
    }

    public function test_skills_crud_and_proficiency_coexistence(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        // Create skill with proficiency level
        $response = $this->post(route('profile.skills.store'), [
            'name' => 'PHP Laravel',
            'proficiency_level' => 'Advanced',
            'category' => 'Backend',
        ]);
        $response->assertRedirect(route('profile.index'));

        $skill = Skill::where('profile_id', $profile->id)->where('name', 'PHP Laravel')->first();
        $this->assertNotNull($skill);
        $this->assertEquals('Advanced', $skill->proficiency_level);
        $this->assertEquals(75, $skill->proficiency);

        // Update skill with integer proficiency
        $updateResponse = $this->put(route('profile.skills.update', $skill), [
            'name' => 'PHP Laravel 12',
            'proficiency' => 95,
            'category' => 'Backend Framework',
        ]);
        $updateResponse->assertRedirect(route('profile.index'));

        $skill->refresh();
        $this->assertEquals('PHP Laravel 12', $skill->name);
        $this->assertEquals(95, $skill->proficiency);
        $this->assertEquals('Expert', $skill->proficiency_level);

        // Delete skill
        $deleteResponse = $this->delete(route('profile.skills.destroy', $skill));
        $deleteResponse->assertRedirect(route('profile.index'));
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_interests_crud_and_suggestions_endpoint(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $profile = Profile::firstOrCreate([
            'user_id' => $user->id,
            'department' => 'Computer Science & Engineering',
        ]);

        DepartmentInterest::create([
            'department' => 'Computer Science & Engineering',
            'name' => 'Quantum Computing',
        ]);

        // Suggestions API endpoint
        $suggestionsResponse = $this->getJson(route('profile.interests.suggestions', ['department' => 'Computer Science & Engineering']));
        $suggestionsResponse->assertStatus(200);
        $suggestionsResponse->assertJsonFragment(['Quantum Computing']);

        // Direct suggestions route alias
        $directSuggestionsResponse = $this->getJson('/interests/suggestions?department=Computer Science & Engineering');
        $directSuggestionsResponse->assertStatus(200);
        $directSuggestionsResponse->assertJsonFragment(['Quantum Computing']);

        // Create interest
        $storeResponse = $this->post(route('profile.interests.store'), [
            'name' => 'Quantum Computing',
            'category' => 'Research',
        ]);
        $storeResponse->assertRedirect(route('profile.index'));

        $interest = Interest::where('profile_id', $profile->id)->first();
        $this->assertNotNull($interest);

        // Update interest
        $updateResponse = $this->put(route('profile.interests.update', $interest), [
            'name' => 'Applied Quantum Computing',
            'category' => 'Advanced Research',
        ]);
        $updateResponse->assertRedirect(route('profile.index'));

        // Delete interest
        $deleteResponse = $this->delete(route('profile.interests.destroy', $interest));
        $deleteResponse->assertRedirect(route('profile.index'));
        $this->assertDatabaseMissing('interests', ['id' => $interest->id]);
    }

    public function test_student_projects_crud_distinct_from_projects_table(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        // Existing team/recruitment project should remain independent
        $teamProject = Project::create([
            'title' => 'Tuli Team Recruitment Project',
            'required_skills' => 'Python, AI',
            'team_size' => 4,
        ]);

        // Store StudentProject
        $storeResponse = $this->post(route('profile.projects.store'), [
            'title' => 'Student Portfolio Hub',
            'description' => 'Portfolio showcasing completed student projects',
            'technologies' => 'Laravel, SQLite, Tailwind',
            'project_url' => 'https://example.com/demo',
            'repo_url' => 'https://github.com/example/repo',
            'completed_date' => 'August 2026',
        ]);
        $storeResponse->assertRedirect(route('profile.index'));

        $studentProject = StudentProject::where('profile_id', $profile->id)->first();
        $this->assertNotNull($studentProject);
        $this->assertEquals('Student Portfolio Hub', $studentProject->title);

        // Ensure projects table (team project) is untouched
        $this->assertDatabaseHas('projects', ['id' => $teamProject->id, 'title' => 'Tuli Team Recruitment Project']);
        $this->assertDatabaseHas('student_projects', ['id' => $studentProject->id]);

        // Update StudentProject
        $updateResponse = $this->put(route('profile.projects.update', $studentProject), [
            'title' => 'Student Portfolio Hub v2',
            'description' => 'Updated portfolio description',
            'technologies' => 'Laravel, SQLite, Tailwind CSS',
            'project_url' => 'https://example.com/demo2',
            'repo_url' => 'https://github.com/example/repo2',
            'completed_date' => 'Fall 2026',
        ]);
        $updateResponse->assertRedirect(route('profile.index'));

        // Delete StudentProject
        $deleteResponse = $this->delete(route('profile.projects.destroy', $studentProject));
        $deleteResponse->assertRedirect(route('profile.index'));
        $this->assertDatabaseMissing('student_projects', ['id' => $studentProject->id]);
        $this->assertDatabaseHas('projects', ['id' => $teamProject->id]);
    }

    public function test_portfolio_links_crud(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);

        $storeResponse = $this->post(route('profile.portfolio-links.store'), [
            'title' => 'My GitHub',
            'platform' => 'GitHub',
            'url' => 'https://github.com/myusername',
        ]);
        $storeResponse->assertRedirect(route('profile.index'));

        $link = PortfolioLink::where('profile_id', $profile->id)->first();
        $this->assertNotNull($link);
        $this->assertEquals('GitHub', $link->platform);
        $this->assertEquals('https://github.com/myusername', $link->url);

        $updateResponse = $this->put(route('profile.portfolio-links.update', $link), [
            'title' => 'My Main GitHub',
            'platform' => 'GitHub',
            'url' => 'https://github.com/myusername-updated',
        ]);
        $updateResponse->assertRedirect(route('profile.index'));

        $deleteResponse = $this->delete(route('profile.portfolio-links.destroy', $link));
        $deleteResponse->assertRedirect(route('profile.index'));
        $this->assertDatabaseMissing('portfolio_links', ['id' => $link->id]);
    }

    public function test_unauthorized_user_cannot_modify_another_users_resources(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $profileA = Profile::firstOrCreate(['user_id' => $userA->id]);
        $skillA = $profileA->skills()->create(['name' => 'Secret Skill', 'proficiency_level' => 'Expert']);
        $projectA = $profileA->studentProjects()->create(['title' => 'Secret Project']);
        $interestA = $profileA->interests()->create(['name' => 'Secret Interest']);
        $linkA = $profileA->portfolioLinks()->create(['title' => 'Secret Link', 'url' => 'https://example.com']);

        // Act as User B
        $this->actingAs($userB);

        $this->put(route('profile.skills.update', $skillA), [
            'name' => 'Hacked Skill',
            'proficiency_level' => 'Beginner',
        ])->assertStatus(403);

        $this->delete(route('profile.skills.destroy', $skillA))->assertStatus(403);

        $this->put(route('profile.projects.update', $projectA), [
            'title' => 'Hacked Project',
        ])->assertStatus(403);

        $this->delete(route('profile.projects.destroy', $projectA))->assertStatus(403);

        $this->put(route('profile.interests.update', $interestA), [
            'name' => 'Hacked Interest',
        ])->assertStatus(403);

        $this->delete(route('profile.interests.destroy', $interestA))->assertStatus(403);

        $this->put(route('profile.portfolio-links.update', $linkA), [
            'title' => 'Hacked Link',
            'url' => 'https://hacked.com',
        ])->assertStatus(403);

        $this->delete(route('profile.portfolio-links.destroy', $linkA))->assertStatus(403);
    }

    public function test_user_model_study_group_and_marketplace_relationships(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $user->profile());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->createdStudyGroups());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->studyGroupMemberships());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $user->studyGroups());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->booksForSale());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $user->bookOrders());
    }
}
