<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TuliFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_ideas_returns_json(): void
    {
        $response = $this->getJson('/api/ideas');

        $response->assertStatus(200)
            ->assertJsonIsArray();
    }

    public function test_generate_idea(): void
    {
        $payload = [
            'domain' => 'campus food ordering',
            'techStack' => 'Node.js, MongoDB, Twilio API',
            'notes' => 'Focus on group orders for hostel students',
        ];

        $response = $this->postJson('/api/ideas/generate', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('domain', 'campus food ordering')
            ->assertJsonPath('tech_stack', 'Node.js, MongoDB, Twilio API');
    }

    public function test_get_ideas_with_filter(): void
    {
        // First generate an idea
        $this->postJson('/api/ideas/generate', [
            'domain' => 'campus food ordering',
            'techStack' => 'Node.js, MongoDB',
        ]);

        $response = $this->getJson('/api/ideas?domain=campus');
        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json()));

        $responseEmpty = $this->getJson('/api/ideas?domain=nonexistentdomainxyz');
        $responseEmpty->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function test_get_teammates(): void
    {
        $response = $this->getJson('/api/teammates?project_id=1');

        $response->assertStatus(200)
            ->assertJsonPath('project', 'Student Productivity App');

        $teammates = $response->json('recommended_teammates');
        $this->assertNotEmpty($teammates);
        $this->assertEquals('Alice Smith', $teammates[0]['name']);
        $this->assertEquals(100, $teammates[0]['match_percent']);
    }

    public function test_get_teammates_not_found(): void
    {
        $response = $this->getJson('/api/teammates?project_id=999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'project not found']);
    }

    public function test_find_team_match(): void
    {
        $payload = [
            'projectTitle' => 'Campus Lost & Found App',
            'requiredSkills' => 'Figma,React,UI Design',
            'teamSize' => 3,
        ];

        $response = $this->postJson('/api/teams/match', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('project_title', 'Campus Lost & Found App')
            ->assertJsonPath('team_size', 3);

        $matches = $response->json('matches');
        $this->assertCount(3, $matches);
        $this->assertEquals('Bob Johnson', $matches[0]['name']);
        $this->assertEquals(100, $matches[0]['match_percent']);
        $this->assertEquals('Charlie Brown', $matches[1]['name']);
        $this->assertEquals(67, $matches[1]['match_percent']);
    }

    public function test_web_routes_render(): void
    {
        $response1 = $this->get('/project-ideas');
        $response1->assertStatus(200);

        $response2 = $this->get('/team-recommendations');
        $response2->assertStatus(200);
    }
}
