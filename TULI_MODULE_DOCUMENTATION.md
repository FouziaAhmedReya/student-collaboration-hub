# 📖 Tuli Module — Complete Technical & User Documentation

This document provides a comprehensive guide to the **Tuli Module** (AI Project Idea Generator & Team Matcher) integrated into the `student-collaboration-hub` Laravel application.

---

## 📌 1. Overview & Key Features

The Tuli Module provides two main capabilities for student collaboration:

1. **AI Project Idea Generator**:
   - Browse and filter project ideas by domain or keywords.
   - Generate project ideas using Google Gemini AI (if `GEMINI_API_KEY` is configured in `.env`).
   - Clean, direct text output fallback if working offline without an API key.

2. **Team Recommendation & Dynamic Matcher**:
   - Calculate student-to-project skill match percentages (`0%`, `33%`, `67%`, `100%`).
   - Recommend top candidates for existing projects based on skill overlap.
   - Dynamic Team Matcher: Input project title, required skills, and team size to automatically assemble the best team with Gemini AI composition insights.

---

## 📁 2. File Structure

All code for this module follows Laravel best practices and is located in the following files:

```text
student-collaboration-hub/
├── app/
│   ├── Http/Controllers/Modules/Tuli/
│   │   ├── ProjectIdeaGeneratorController.php  # Handles idea listing & AI generation
│   │   └── TeamRecommendationController.php     # Handles skill matching & team recommendations
│   └── Models/
│       ├── ProjectIdea.php                      # Model for 'ideas' table
│       ├── Project.php                          # Model for 'projects' table
│       └── Student.php                          # Model for 'students' table
├── database/migrations/
│   └── 2026_08_09_000001_create_tuli_tables.php # Creates ideas, projects, students tables & seeds data
├── resources/views/
│   ├── layouts/app.blade.php                    # Global layout with navigation links
│   └── modules/tuli/
│       ├── project-idea-generator/
│       │   └── index.blade.php                  # Web UI for Project Ideas Generator
│       └── team-recommendations/
│           └── index.blade.php                  # Web UI for Team Matcher & Recommendations
├── routes/
│   └── web.php                                  # Web and API route definitions
└── tests/Feature/
    └── TuliFeatureTest.php                      # Automated test suite for Tuli module
```

---

## 🗄️ 3. Database Schema & Seed Data

### Migration: `database/migrations/2026_08_09_000001_create_tuli_tables.php`

1. **`ideas` table**:
   - `id`: Primary key (auto-increment)
   - `title`: String
   - `description`: Text
   - `domain`: String
   - `tech_stack`: String
   - `created_at`, `updated_at`: Timestamps

2. **`projects` table**:
   - `id`: Primary key (auto-increment)
   - `title`: String
   - `required_skills`: Text
   - `team_size`: Integer (default 4)
   - `created_at`, `updated_at`: Timestamps

3. **`students` table**:
   - `id`: Primary key (auto-increment)
   - `name`: String
   - `department`: String
   - `skills`: Text (comma-separated skills)
   - `interests`: Text (nullable)
   - `completed_projects`: Text (nullable)
   - `created_at`, `updated_at`: Timestamps

### Pre-seeded Mock Students:
- **Alice Smith**: Computer Science (`Python, Flask, SQLite, React`)
- **Bob Johnson**: Software Engineering (`React, Figma, UI Design, JavaScript`)
- **Charlie Brown**: Computer Science (`Figma, UI Design, CSS, HTML`)
- **Diana Prince**: Information Technology (`Node.js, MongoDB, Express, React`)
- **Evan Wright**: Computer Science (`Python, Machine Learning, TensorFlow, SQL`)
- **Fiona Gallagher**: Software Engineering (`Java, Spring Boot, MySQL`)

---

## ⚙️ 4. Controllers & Business Logic

### `ProjectIdeaGeneratorController.php`
- **`index(Request $request)`**:
  - Filter ideas by `domain` query param.
  - Returns JSON for API requests (`/api/ideas`), or renders `modules.tuli.project-idea-generator.index` for web browsers.
- **`generate(Request $request)`**:
  - Validates `domain`, `techStack`, and optional `notes`.
  - Attempts Gemini API call (`gemini-2.5-flash`).
  - Fallback logic: Creates clean title (`"{Domain} Project"`) and uses the exact provided notes/description without extra prefix tags.

### `TeamRecommendationController.php`
- **`index(Request $request)`**:
  - Calculates skill match %: `(Matching Skills / Required Skills) * 100`.
  - Sorts candidates descending by match %.
  - Generates Gemini AI analysis explaining why candidates fit the project.
- **`match(Request $request)`**:
  - Creates project in database.
  - Matches top candidates based on skill match % up to requested `teamSize`.
  - Returns AI evaluation.

---

## 🌐 5. Routes & API Endpoints

### Web Pages (Browser)
- **Project Ideas**: `http://127.0.0.1:8000/project-ideas` (`GET`, `POST /generate`)
- **Team Matcher**: `http://127.0.0.1:8000/team-recommendations` (`GET`, `POST /match`)

### REST API Endpoints
| Endpoint | Method | Description | Payload / Params |
|---|---|---|---|
| `/api/ideas` | `GET` | Browse ideas | `?domain=keyword` |
| `/api/ideas/generate` | `POST` | Generate new idea | `{"domain": "...", "techStack": "...", "notes": "..."}` |
| `/api/teammates` | `GET` | Recommended teammates | `?project_id=1` |
| `/api/teams/match` | `POST` | Dynamic team matching | `{"projectTitle": "...", "requiredSkills": "...", "teamSize": 3}` |

---

## 🧪 6. Testing

Automated tests are located in [`tests/Feature/TuliFeatureTest.php`](file:///e:/brac/student-collaboration-hub/tests/Feature/TuliFeatureTest.php).

Run tests using:
```bash
php artisan test --filter=TuliFeatureTest
```

**Results**: 7 tests passed, 27 assertions.

---

## 🚀 7. Quick Start Instructions

1. Run migrations (if not already done):
   ```bash
   php artisan migrate
   ```
2. Start the dev server:
   ```bash
   php artisan serve
   ```
3. Open `http://127.0.0.1:8000/project-ideas` in your browser.
