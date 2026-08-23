<?php

namespace App\Http\Controllers\Modules\Tuli;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProgressDashboardController extends Controller
{
    /**
     * Display the progress dashboard with metrics and Gemini AI productivity insights.
     */
    public function index(Request $request)
    {
        @set_time_limit(120);

        $projectId = $request->query('project_id');
        $allProjects = Project::with('tasks')->orderBy('created_at', 'desc')->get();

        $selectedProject = null;
        if (!empty($projectId)) {
            $selectedProject = $allProjects->firstWhere('id', $projectId);
        }

        // Tasks calculation
        $allTasks = Task::with('project')->get();

        if ($selectedProject) {
            $tasks = $selectedProject->tasks;
        } else {
            $tasks = $allTasks;
        }

        $totalProjectsCount = $allProjects->count();
        $totalTasksCount = $tasks->count();
        $completedTasksCount = $tasks->where('status', 'completed')->count();
        $inProgressTasksCount = $tasks->where('status', 'in_progress')->count();
        $pendingTasksCount = $totalTasksCount - $completedTasksCount - $inProgressTasksCount;
        if ($pendingTasksCount < 0) $pendingTasksCount = 0;

        $completionPercentage = $totalTasksCount > 0
            ? (int) round(($completedTasksCount / $totalTasksCount) * 100)
            : 0;

        // Overdue tasks
        $overdueCount = $tasks->filter(function ($t) {
            return method_exists($t, 'getIsOverdueAttribute') ? $t->is_overdue : false;
        })->count();

        // Team performance metrics
        $teamPerformance = [];
        $assignedGroups = $tasks->groupBy('assigned_to');

        foreach ($assignedGroups as $member => $memberTasks) {
            if (empty($member)) continue;
            $mTotal = $memberTasks->count();
            $mCompleted = $memberTasks->where('status', 'completed')->count();
            $mPending = $mTotal - $mCompleted;
            $mRate = $mTotal > 0 ? (int) round(($mCompleted / $mTotal) * 100) : 0;

            $teamPerformance[] = [
                'member' => $member,
                'total_tasks' => $mTotal,
                'completed_tasks' => $mCompleted,
                'pending_tasks' => $mPending,
                'completion_rate' => $mRate,
            ];
        }

        usort($teamPerformance, fn($a, $b) => $b['completion_rate'] <=> $a['completion_rate']);

        // Gemini AI Productivity Insights Generation (Only when generate_ai=1 is requested)
        $aiInsights = null;
        $generateAi = $request->boolean('generate_ai') || $request->query('generate_ai') == '1';
        $geminiKey = config('services.gemini.api_key') ?: env('GOOGLE_API_KEY') ?: env('GEMINI_API_KEY');

        if ($generateAi && $geminiKey) {
            $projectTitle = $selectedProject ? $selectedProject->title : 'All Active Projects';
            $teamSummary = implode("\n", array_map(function ($tp) {
                return "- {$tp['member']}: {$tp['completed_tasks']}/{$tp['total_tasks']} tasks completed ({$tp['completion_rate']}%)";
            }, array_slice($teamPerformance, 0, 5)));

            $prompt = "You are an AI Project Management Coach. Analyze the following project progress and team task metrics:\n\n" .
                "Project Context: {$projectTitle}\n" .
                "Total Tasks: {$totalTasksCount}\n" .
                "Completed Tasks: {$completedTasksCount}\n" .
                "In Progress Tasks: {$inProgressTasksCount}\n" .
                "Pending Tasks: {$pendingTasksCount}\n" .
                "Overall Completion Rate: {$completionPercentage}%\n" .
                "Overdue Tasks: {$overdueCount}\n\n" .
                "Team Member Workload:\n" . ($teamSummary ?: "No individual assignments yet") . "\n\n" .
                "Provide clear, actionable AI Productivity Insights with the following structured sections:\n" .
                "1. Project Health & Velocity Summary\n" .
                "2. Potential Bottlenecks & Risk Warnings\n" .
                "3. Recommended Action Plan & Next Steps\n\n" .
                "Return ONLY a clean text response formatted with markdown headers and bullet points.";

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
                            $aiInsights = $text;
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
                'total_projects' => $totalProjectsCount,
                'total_tasks' => $totalTasksCount,
                'completed_tasks' => $completedTasksCount,
                'in_progress_tasks' => $inProgressTasksCount,
                'pending_tasks' => $pendingTasksCount,
                'completion_rate' => $completionPercentage,
                'team_performance' => $teamPerformance,
                'ai_insights' => $aiInsights,
            ], 200);
        }

        return view('modules.tuli.progress-dashboard.index', compact(
            'allProjects',
            'selectedProject',
            'totalProjectsCount',
            'totalTasksCount',
            'completedTasksCount',
            'inProgressTasksCount',
            'pendingTasksCount',
            'completionPercentage',
            'overdueCount',
            'teamPerformance',
            'aiInsights'
        ));
    }

    /**
     * API Summary endpoint for frontend integration.
     */
    public function apiSummary(Request $request)
    {
        return $this->index($request);
    }
}
