<?php

namespace App\Http\Controllers\Modules\Tuli;

use App\Http\Controllers\Controller;
use App\Models\ProjectIdea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProjectIdeaGeneratorController extends Controller
{
    /**
     * Display a listing of project ideas or return JSON for API.
     */
    public function index(Request $request)
    {
        $domain = $request->query('domain', '');

        $query = ProjectIdea::query();

        if (!empty($domain)) {
            $query->where(function ($q) use ($domain) {
                $q->where('domain', 'LIKE', '%' . $domain . '%')
                  ->orWhere('title', 'LIKE', '%' . $domain . '%');
            });
        }

        $ideas = $query->orderBy('created_at', 'desc')->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($ideas->map(function ($idea) {
                return [
                    'id' => $idea->id,
                    'title' => $idea->title,
                    'description' => $idea->description,
                    'domain' => $idea->domain,
                    'tech_stack' => $idea->tech_stack,
                ];
            }), 200);
        }

        return view('modules.tuli.project-idea-generator.index', compact('ideas', 'domain'));
    }

    /**
     * Generate a new project idea using Gemini API or fallback rules.
     */
    public function generate(Request $request)
    {
        @set_time_limit(120);

        $validated = $request->validate([
            'domain' => 'required|string|max:255',
            'subDomain' => 'nullable|string|max:255',
            'sub_domain' => 'nullable|string|max:255',
            'techStack' => 'nullable|string|max:255',
            'tech_stack' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $domain = $validated['domain'];
        $subDomain = $validated['subDomain'] ?? $validated['sub_domain'] ?? '';
        $techStack = $validated['techStack'] ?? $validated['tech_stack'] ?? '';
        $notes = $validated['notes'] ?? '';

        $fullDomain = $domain . ($subDomain ? " (Sub-Field: {$subDomain})" : '');

        $title = null;
        $description = null;

        // Try Gemini API if GOOGLE_API_KEY or GEMINI_API_KEY is present
        $geminiKey = config('services.gemini.api_key') ?: env('GOOGLE_API_KEY') ?: env('GEMINI_API_KEY');
        if ($geminiKey) {
            $prompt = "You are an expert AI software architect. Generate a highly detailed, innovative, and comprehensive project idea for the '{$fullDomain}' domain.\n" .
                "Suggested Technologies: " . ($techStack ?: "Modern Web/Mobile Stack") . "\n" .
                "Notes/Constraints: " . ($notes ?: "None") . "\n\n" .
                "INSTRUCTIONS FOR THE DESCRIPTION:\n" .
                "The 'description' field MUST contain explicit labeled sections separated by newlines:\n\n" .
                "Executive Summary:\nComprehensive overview of the core concept, target audience, and problem solved.\n\n" .
                "Key Features:\n* Feature 1: detailed description\n* Feature 2: detailed description\n* Feature 3: detailed description\n\n" .
                "Technical Architecture:\nHow the technologies (" . ($techStack ?: "relevant tools") . ") and data flows are used.\n\n" .
                "Expected Impact:\nMeasurable benefits and value for target users.\n\n" .
                "Return ONLY a valid JSON object with keys 'title' and 'description'. Do not wrap in markdown backticks.";

            $models = ['gemini-2.5-flash', 'gemini-flash-lite-latest', 'gemini-3.5-flash-lite', 'gemini-flash-latest'];

            foreach ($models as $model) {
                try {
                    $response = Http::withoutVerifying()
                        ->timeout(8)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}", [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]]
                            ],
                            'generationConfig' => [
                                'response_mime_type' => 'application/json',
                            ]
                        ]);

                    if ($response->successful()) {
                        $json = $response->json();
                        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
                        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
                            $parsed = json_decode($matches[0], true);
                            if (is_array($parsed) && !empty($parsed['title']) && !empty($parsed['description'])) {
                                $title = is_array($parsed['title']) ? implode(' ', $parsed['title']) : (string)$parsed['title'];
                                $descRaw = $parsed['description'];
                                $description = is_array($descRaw) ? implode("\n\n", $descRaw) : (string)$descRaw;
                                break; // Success! Stop trying other models
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Try next candidate model
                }
            }
        }

        // Clean fallback
        if (!$title || !$description) {
            $title = Str::title($domain) . " Project";
            $description = !empty($notes)
                ? $notes
                : "Project focusing on {$domain}" . ($techStack ? " using {$techStack}." : ".");
        }

        $idea = ProjectIdea::create([
            'title' => $title,
            'description' => $description,
            'domain' => $domain,
            'tech_stack' => $techStack,
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'id' => $idea->id,
                'title' => $idea->title,
                'description' => $idea->description,
                'domain' => $idea->domain,
                'tech_stack' => $idea->tech_stack,
            ], 201);
        }

        return redirect()->route('project-ideas.index')
            ->with('success', "Project idea '{$idea->title}' successfully generated!");
    }

    /**
     * Update an existing project idea.
     */
    public function update(Request $request, ProjectIdea $idea)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'techStack' => 'nullable|string|max:255',
            'tech_stack' => 'nullable|string|max:255',
            'description' => 'required|string',
        ]);

        $techStack = $validated['techStack'] ?? $validated['tech_stack'] ?? $idea->tech_stack;

        $idea->update([
            'title' => $validated['title'],
            'domain' => $validated['domain'],
            'tech_stack' => $techStack,
            'description' => $validated['description'],
        ]);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'id' => $idea->id,
                'title' => $idea->title,
                'description' => $idea->description,
                'domain' => $idea->domain,
                'tech_stack' => $idea->tech_stack,
            ], 200);
        }

        return redirect()->route('project-ideas.index')
            ->with('success', "Project idea '{$idea->title}' updated successfully!");
    }

    /**
     * Remove a project idea.
     */
    public function destroy(Request $request, ProjectIdea $idea)
    {
        $title = $idea->title;
        $idea->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => "Project idea '{$title}' deleted successfully."], 200);
        }

        return redirect()->route('project-ideas.index')
            ->with('success', "Project idea '{$title}' deleted successfully!");
    }
}
