<?php

namespace App\Http\Controllers\Modules\Tuli;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Services\Tuli\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EventAnnouncementController extends Controller
{
    /**
     * Display a listing of published events, workshops, seminars, and hackathons.
     */
    public function index(Request $request)
    {
        @set_time_limit(120);

        $selectedType = $request->query('type', 'All');
        $query = Event::query()->orderBy('event_date', 'asc');

        if (!empty($selectedType) && $selectedType !== 'All') {
            $query->where('type', $selectedType);
        }

        $events = $query->get();
        $allEvents = Event::orderBy('event_date', 'asc')->get();

        // Gemini AI Event Recommendations Generation (On-Demand when generate_ai=1)
        $aiRecommendations = null;
        $generateAi = $request->boolean('generate_ai') || $request->query('generate_ai') == '1';
        $geminiKey = config('services.gemini.api_key') ?: env('GOOGLE_API_KEY') ?: env('GEMINI_API_KEY');

        if ($generateAi && $geminiKey && $allEvents->count() > 0) {
            // Fetch JWT authenticated user or first registered student profile dynamically
            $user = JwtService::getUserFromRequest($request) ?: User::with(['profile.skills', 'profile.interests', 'profile.studentProjects'])->first();

            $studentContext = "";
            if ($user) {
                if (!$user->relationLoaded('profile')) {
                    $user->load(['profile.skills', 'profile.interests', 'profile.studentProjects']);
                }
                $p = $user->profile;
                $skillsStr = ($p && $p->skills && $p->skills->count() > 0) ? $p->skills->pluck('name')->implode(', ') : 'Not specified yet';
                $interestsStr = ($p && $p->interests && $p->interests->count() > 0) ? $p->interests->pluck('name')->implode(', ') : 'General Computer Science';
                $projectsStr = ($p && $p->studentProjects && $p->studentProjects->count() > 0) ? $p->studentProjects->pluck('title')->implode(', ') : 'None recorded yet';
                $dept = ($p && !empty($p->department)) ? $p->department : 'Computer Science';
                $bioInfo = ($p && (!empty($p->bio) || !empty($p->about_me))) ? "\nBio: " . trim(($p->about_me ?? '') . ' ' . ($p->bio ?? '')) : '';

                $studentContext = "Student Name: {$user->name}\n" .
                    "Department: {$dept}\n" .
                    "Technical Skills: {$skillsStr}\n" .
                    "Interests: {$interestsStr}\n" .
                    "Completed Projects: {$projectsStr}{$bioInfo}";
            } else {
                $studentContext = "Student Profile: Registered Student in Computer Science";
            }

            $eventRoster = implode("\n\n", $allEvents->map(function ($e) {
                $dateStr = $e->event_date ? $e->event_date->format('M d, Y @ h:i A') : 'TBA';
                return "Event ID: {$e->id}\nTitle: {$e->title}\nType: {$e->type}\nTarget Skills: {$e->target_skills}\nDate & Location: {$dateStr} | {$e->location}\nDescription: {$e->description}";
            })->toArray());

            $prompt = "You are an AI Academic Event & Career Advisor. Evaluate the following upcoming university events, workshops, seminars, and hackathons for this student:\n\n" .
                "{$studentContext}\n\n" .
                "Upcoming Events Roster:\n{$eventRoster}\n\n" .
                "Provide personalized AI Event Recommendations for the student with the following structure:\n" .
                "# 🚀 Recommended Events & Workshops for Your Profile\n" .
                "## 1. Top Recommended Workshop / Event\n" .
                "- Explain why this specific event aligns perfectly with their skills & career goals.\n" .
                "## 2. Recommended Hackathon / Competition\n" .
                "- Explain why they should participate and what skills they will gain.\n" .
                "## 3. Recommended Seminar & Tech Focus\n" .
                "- Explain key learnings and networking benefits.\n\n" .
                "Return ONLY a clean response with markdown headings and bullet points.";

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
                            $aiRecommendations = $text;
                            break;
                        }
                    }
                } catch (\Throwable $e) {
                    // Failover to next model
                }
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'selected_type' => $selectedType,
                'events' => $events,
                'ai_recommendations' => $aiRecommendations,
            ], 200);
        }

        return view('modules.tuli.events.index', compact('events', 'allEvents', 'selectedType', 'aiRecommendations'));
    }

    /**
     * Store a newly created event or workshop announcement in database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:Workshop,Seminar,Hackathon,Webinar',
            'description' => 'required|string',
            'target_skills' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
        ]);

        $event = Event::create($validated);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Event published successfully', 'event' => $event], 201);
        }

        return redirect()->route('events.index', ['type' => $event->type])
            ->with('success', "Event '{$event->title}' published successfully!");
    }

    /**
     * Update the specified event announcement in database.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:Workshop,Seminar,Hackathon,Webinar',
            'description' => 'required|string',
            'target_skills' => 'nullable|string|max:255',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
        ]);

        $event->update($validated);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Event updated successfully', 'event' => $event], 200);
        }

        return redirect()->route('events.index', ['type' => $event->type])
            ->with('success', "Event '{$event->title}' updated successfully!");
    }

    /**
     * Remove the specified event announcement from database.
     */
    public function destroy(Request $request, Event $event)
    {
        $title = $event->title;
        $event->delete();

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Event deleted successfully'], 200);
        }

        return redirect()->route('events.index')
            ->with('success', "Event '{$title}' removed successfully!");
    }
}
