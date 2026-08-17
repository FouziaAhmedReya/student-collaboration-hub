<?php

namespace App\Http\Controllers\Modules\Tuli;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectIdea;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeamRecommendationController extends Controller
{
    /**
     * Get recommended teammates for a specific project or project idea.
     */
    public function index(Request $request)
    {
        @set_time_limit(120);
        $projectId = $request->query('project_id');

        // Combine all Projects and ProjectIdeas into a unified list
        $projectsList = Project::orderBy('created_at', 'desc')->get()->map(function ($p) {
            return (object)[
                'id' => 'p_' . $p->id,
                'raw_id' => $p->id,
                'type' => 'Project',
                'title' => $p->title,
                'required_skills' => $p->required_skills,
                'team_size' => $p->team_size ?? 4,
            ];
        });

        $ideasList = ProjectIdea::orderBy('created_at', 'desc')->get()->map(function ($i) {
            return (object)[
                'id' => 'i_' . $i->id,
                'raw_id' => $i->id,
                'type' => 'AI Idea',
                'title' => $i->title,
                'required_skills' => !empty($i->tech_stack) ? $i->tech_stack : $i->domain,
                'team_size' => 4,
            ];
        });

        $allProjects = $projectsList->concat($ideasList);

        $project = null;
        if (!empty($projectId)) {
            $project = $allProjects->firstWhere('id', $projectId);
            if (!$project) {
                // Fallback check by raw_id
                $project = $allProjects->first(function ($item) use ($projectId) {
                    return $item->id == $projectId || $item->raw_id == $projectId;
                });
            }
        }

        $recommendedTeammates = [];
        $aiAnalysis = null;

        if ($project) {
            $requiredSkillsList = array_filter(array_map('trim', explode(',', strtolower($project->required_skills))));
            $students = Student::all();

            foreach ($students as $student) {
                $studentSkills = array_filter(array_map('trim', explode(',', strtolower($student->skills))));
                $overlap = count(array_intersect($requiredSkillsList, $studentSkills));
                $matchPct = count($requiredSkillsList) > 0
                    ? (int) round(($overlap / count($requiredSkillsList)) * 100)
                    : 0;

                $recommendedTeammates[] = [
                    'id' => $student->id,
                    'name' => $student->name,
                    'department' => $student->department,
                    'skills' => $student->skills,
                    'match_percent' => $matchPct,
                    '_interests' => $student->interests,
                    '_completed_projects' => $student->completed_projects,
                ];
            }

            usort($recommendedTeammates, function ($a, $b) {
                return $b['match_percent'] <=> $a['match_percent'];
            });

            // AI Analysis if GOOGLE_API_KEY or GEMINI_API_KEY present
            $geminiKey = config('services.gemini.api_key') ?: env('GOOGLE_API_KEY') ?: env('GEMINI_API_KEY');
            if ($geminiKey) {
                $topMatches = array_filter($recommendedTeammates, fn($s) => $s['match_percent'] > 0);
                if (empty($topMatches)) {
                    $topMatches = array_slice($recommendedTeammates, 0, 3);
                } else {
                    $topMatches = array_slice($topMatches, 0, 3);
                }

                if (!empty($topMatches)) {
                    $studentContext = implode("\n", array_map(function ($s) {
                        return "- {$s['name']} (Skills: {$s['skills']}, Interests: {$s['_interests']}, Completed Projects: {$s['_completed_projects']})";
                    }, $topMatches));

                    $prompt = "Explain why the following students are recommended for the project '{$project->title}' " .
                        "requiring skills '{$project->required_skills}'. Highlight how their skills, interests, and completed projects align.\n" .
                        "Recommended Students:\n{$studentContext}";

                    $models = ['gemini-2.5-flash', 'gemini-flash-lite-latest', 'gemini-3.5-flash-lite', 'gemini-flash-latest'];
                    foreach ($models as $model) {
                        try {
                            $response = Http::withoutVerifying()
                                ->timeout(8)
                                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}", [
                                    'contents' => [['parts' => [['text' => $prompt]]]]
                                ]);

                            if ($response->successful()) {
                                $text = trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                                if (!empty($text)) {
                                    $aiAnalysis = $text;
                                    break;
                                }
                            }
                        } catch (\Throwable $e) {
                            // Try next model
                        }
                    }
                }
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            $cleanTeammates = array_map(function ($item) {
                unset($item['_interests'], $item['_completed_projects']);
                return $item;
            }, $recommendedTeammates);

            $payload = [
                'project' => $project ? $project->title : '',
                'recommended_teammates' => $cleanTeammates,
            ];

            if ($aiAnalysis) {
                $payload['ai_analysis'] = $aiAnalysis;
            }

            return response()->json($payload, 200);
        }

        return view('modules.tuli.team-recommendations.index', compact('project', 'allProjects', 'recommendedTeammates', 'aiAnalysis'));
    }

    /**
     * Dynamically match team members based on project requirements.
     */
    public function match(Request $request)
    {
        @set_time_limit(120);
        $validated = $request->validate([
            'projectTitle' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'requiredSkills' => 'nullable|string',
            'required_skills' => 'nullable|string',
            'teamSize' => 'nullable|integer|min:1',
            'team_size' => 'nullable|integer|min:1',
        ]);

        $title = $validated['projectTitle'] ?? $validated['title'] ?? null;
        $requiredSkills = $validated['requiredSkills'] ?? $validated['required_skills'] ?? null;
        $teamSize = (int) ($validated['teamSize'] ?? $validated['team_size'] ?? 4);

        if (!$title || !$requiredSkills) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'projectTitle and requiredSkills are required'], 400);
            }
            return back()->withErrors(['match' => 'Project title and required skills are required.']);
        }

        $project = Project::create([
            'title' => $title,
            'required_skills' => $requiredSkills,
            'team_size' => $teamSize,
        ]);

        $requiredSkillsList = array_filter(array_map('trim', explode(',', strtolower($requiredSkills))));
        $students = Student::all();
        $matches = [];

        foreach ($students as $student) {
            $studentSkills = array_filter(array_map('trim', explode(',', strtolower($student->skills))));
            $overlap = count(array_intersect($requiredSkillsList, $studentSkills));
            $matchPct = count($requiredSkillsList) > 0
                ? (int) round(($overlap / count($requiredSkillsList)) * 100)
                : 0;

            $matches[] = [
                'id' => $student->id,
                'name' => $student->name,
                'match_percent' => $matchPct,
                '_skills' => $student->skills,
                '_interests' => $student->interests,
                '_completed_projects' => $student->completed_projects,
            ];
        }

        usort($matches, fn($a, $b) => $b['match_percent'] <=> $a['match_percent']);
        $selectedMatches = array_slice($matches, 0, $teamSize);

        $aiAnalysis = null;
        $geminiKey = config('services.gemini.api_key') ?: env('GOOGLE_API_KEY') ?: env('GEMINI_API_KEY');
        if ($geminiKey && !empty($selectedMatches)) {
            $studentContext = implode("\n", array_map(function ($m) {
                return "- {$m['name']} (Skills: {$m['_skills']}, Interests: {$m['_interests']}, Completed Projects: {$m['_completed_projects']})";
            }, $selectedMatches));

            $prompt = "Explain why this recommended team is optimal for the project '{$title}' requiring skills '{$requiredSkills}'. " .
                "Highlight how their skills, interests, and completed projects complement each other and align with the project.\n" .
                "Team Members:\n{$studentContext}";

            $models = ['gemini-2.5-flash', 'gemini-flash-lite-latest', 'gemini-3.5-flash-lite', 'gemini-flash-latest'];
            foreach ($models as $model) {
                try {
                    $response = Http::withoutVerifying()
                        ->timeout(8)
                        ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}", [
                            'contents' => [['parts' => [['text' => $prompt]]]]
                        ]);

                    if ($response->successful()) {
                        $text = trim($response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '');
                        if (!empty($text)) {
                            $aiAnalysis = $text;
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    // Try next model
                }
            }
        }

        $finalMatches = array_map(function ($item) {
            unset($item['_skills'], $item['_interests'], $item['_completed_projects']);
            return $item;
        }, $selectedMatches);

        if ($request->wantsJson() || $request->is('api/*')) {
            $payload = [
                'project_id' => 'p_' . $project->id,
                'project_title' => $project->title,
                'team_size' => $teamSize,
                'matches' => $finalMatches,
            ];

            if ($aiAnalysis) {
                $payload['ai_analysis'] = $aiAnalysis;
            }

            return response()->json($payload, 201);
        }

        return redirect()->route('team-recommendations.index', ['project_id' => 'p_' . $project->id])
            ->with('success', "Matched a team for '{$project->title}'!");
    }
}
