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
        $validated = $request->validate([
            'domain' => 'required|string|max:255',
            'techStack' => 'nullable|string|max:255',
            'tech_stack' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $domain = $validated['domain'];
        $techStack = $validated['techStack'] ?? $validated['tech_stack'] ?? '';
        $notes = $validated['notes'] ?? '';

        $title = null;
        $description = null;

        // Try Gemini API if API key is present
        $geminiKey = env('GEMINI_API_KEY');
        if ($geminiKey) {
            try {
                $prompt = "Generate a creative and detailed project idea for the '{$domain}' domain.\n" .
                    "Suggested Technologies: {$techStack}\n" .
                    "Notes/Constraints: {$notes}\n" .
                    "Return ONLY a JSON object with keys 'title' and 'description'. Do not add markdown formatting or backticks around JSON.";

                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", [
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
                    $parsed = json_decode($text, true);
                    if (is_array($parsed)) {
                        $title = $parsed['title'] ?? null;
                        $description = $parsed['description'] ?? null;
                    }
                }
            } catch (\Throwable $e) {
                // Silently fallback if error
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
}
