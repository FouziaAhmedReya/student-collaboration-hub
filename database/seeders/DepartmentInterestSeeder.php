<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepartmentInterest;

class DepartmentInterestSeeder extends Seeder
{
    public function run(): void
    {
        // Wipe and re-seed the catalog so it's idempotent
        DepartmentInterest::truncate();

        $interests = [

            // ─────────────────────────────────────────────────
            // CSE — Computer Science & Engineering  (45 entries)
            // ─────────────────────────────────────────────────
            ['department' => 'CSE', 'name' => 'Artificial Intelligence'],
            ['department' => 'CSE', 'name' => 'Machine Learning'],
            ['department' => 'CSE', 'name' => 'Deep Learning'],
            ['department' => 'CSE', 'name' => 'Reinforcement Learning'],
            ['department' => 'CSE', 'name' => 'Computer Vision'],
            ['department' => 'CSE', 'name' => 'Natural Language Processing'],
            ['department' => 'CSE', 'name' => 'Data Science'],
            ['department' => 'CSE', 'name' => 'Big Data Analytics'],
            ['department' => 'CSE', 'name' => 'Web Development'],
            ['department' => 'CSE', 'name' => 'Full Stack Development'],
            ['department' => 'CSE', 'name' => 'Backend Development'],
            ['department' => 'CSE', 'name' => 'Frontend Development'],
            ['department' => 'CSE', 'name' => 'Mobile App Development'],
            ['department' => 'CSE', 'name' => 'Android Development'],
            ['department' => 'CSE', 'name' => 'iOS Development'],
            ['department' => 'CSE', 'name' => 'Game Development'],
            ['department' => 'CSE', 'name' => 'Cybersecurity'],
            ['department' => 'CSE', 'name' => 'Ethical Hacking'],
            ['department' => 'CSE', 'name' => 'Cryptography'],
            ['department' => 'CSE', 'name' => 'Cloud Computing'],
            ['department' => 'CSE', 'name' => 'DevOps'],
            ['department' => 'CSE', 'name' => 'Docker & Containerization'],
            ['department' => 'CSE', 'name' => 'Kubernetes'],
            ['department' => 'CSE', 'name' => 'Microservices Architecture'],
            ['department' => 'CSE', 'name' => 'API Development'],
            ['department' => 'CSE', 'name' => 'Database Design'],
            ['department' => 'CSE', 'name' => 'Blockchain & Web3'],
            ['department' => 'CSE', 'name' => 'Internet of Things (IoT)'],
            ['department' => 'CSE', 'name' => 'Robotics'],
            ['department' => 'CSE', 'name' => 'Augmented Reality / VR'],
            ['department' => 'CSE', 'name' => 'Competitive Programming'],
            ['department' => 'CSE', 'name' => 'Data Structures & Algorithms'],
            ['department' => 'CSE', 'name' => 'System Design'],
            ['department' => 'CSE', 'name' => 'Software Engineering'],
            ['department' => 'CSE', 'name' => 'UI/UX Design'],
            ['department' => 'CSE', 'name' => 'Vibe Coding'],
            ['department' => 'CSE', 'name' => 'Open Source Contribution'],
            ['department' => 'CSE', 'name' => 'Linux & System Administration'],
            ['department' => 'CSE', 'name' => 'Computer Networking'],
            ['department' => 'CSE', 'name' => 'Compiler Design'],
            ['department' => 'CSE', 'name' => 'Operating Systems'],
            ['department' => 'CSE', 'name' => 'Parallel & Distributed Computing'],
            ['department' => 'CSE', 'name' => 'Computer Graphics'],
            ['department' => 'CSE', 'name' => 'Human-Computer Interaction'],
            ['department' => 'CSE', 'name' => 'Quantum Computing'],

            // ─────────────────────────────────────────────────
            // EEE — Electrical & Electronic Engineering  (18 entries)
            // ─────────────────────────────────────────────────
            ['department' => 'EEE', 'name' => 'Embedded Systems'],
            ['department' => 'EEE', 'name' => 'Signal Processing'],
            ['department' => 'EEE', 'name' => 'Power Systems & Smart Grid'],
            ['department' => 'EEE', 'name' => 'VLSI Design'],
            ['department' => 'EEE', 'name' => 'Control Systems'],
            ['department' => 'EEE', 'name' => 'Antenna & RF Design'],
            ['department' => 'EEE', 'name' => 'Wireless Communications'],
            ['department' => 'EEE', 'name' => 'Fiber Optics'],
            ['department' => 'EEE', 'name' => 'Renewable Energy & Solar'],
            ['department' => 'EEE', 'name' => 'PLC & Industrial Automation'],
            ['department' => 'EEE', 'name' => 'Circuit Design'],
            ['department' => 'EEE', 'name' => 'PCB Design'],
            ['department' => 'EEE', 'name' => 'Microcontroller Programming'],
            ['department' => 'EEE', 'name' => 'Internet of Things (IoT)'],
            ['department' => 'EEE', 'name' => 'Robotics & Mechatronics'],
            ['department' => 'EEE', 'name' => 'Digital Electronics'],
            ['department' => 'EEE', 'name' => 'Biomedical Engineering'],
            ['department' => 'EEE', 'name' => 'Nanotechnology'],

            // ─────────────────────────────────────────────────
            // BBA — Business Administration  (14 entries)
            // ─────────────────────────────────────────────────
            ['department' => 'BBA', 'name' => 'Entrepreneurship & Startups'],
            ['department' => 'BBA', 'name' => 'Digital Marketing'],
            ['department' => 'BBA', 'name' => 'Finance & Investment'],
            ['department' => 'BBA', 'name' => 'E-Commerce & Online Business'],
            ['department' => 'BBA', 'name' => 'Business Analytics'],
            ['department' => 'BBA', 'name' => 'Supply Chain Management'],
            ['department' => 'BBA', 'name' => 'Human Resource Management'],
            ['department' => 'BBA', 'name' => 'Market Research'],
            ['department' => 'BBA', 'name' => 'Brand Management'],
            ['department' => 'BBA', 'name' => 'Corporate Strategy'],
            ['department' => 'BBA', 'name' => 'Stock Market & Trading'],
            ['department' => 'BBA', 'name' => 'Project Management'],
            ['department' => 'BBA', 'name' => 'International Business'],
            ['department' => 'BBA', 'name' => 'Fintech'],

            // ─────────────────────────────────────────────────
            // Architecture  (10 entries)
            // ─────────────────────────────────────────────────
            ['department' => 'Architecture', 'name' => 'Urban & City Planning'],
            ['department' => 'Architecture', 'name' => 'Sustainable Architecture'],
            ['department' => 'Architecture', 'name' => 'Interior Design'],
            ['department' => 'Architecture', 'name' => 'Landscape Architecture'],
            ['department' => 'Architecture', 'name' => 'Building Information Modeling (BIM)'],
            ['department' => 'Architecture', 'name' => 'AutoCAD & 3D Modeling'],
            ['department' => 'Architecture', 'name' => 'Structural Design'],
            ['department' => 'Architecture', 'name' => 'Green Building Design'],
            ['department' => 'Architecture', 'name' => 'Historic Preservation'],
            ['department' => 'Architecture', 'name' => 'Parametric Design'],

            // ─────────────────────────────────────────────────
            // Mathematics  (10 entries)
            // ─────────────────────────────────────────────────
            ['department' => 'Mathematics', 'name' => 'Number Theory'],
            ['department' => 'Mathematics', 'name' => 'Linear Algebra & Applications'],
            ['department' => 'Mathematics', 'name' => 'Statistics & Probability'],
            ['department' => 'Mathematics', 'name' => 'Mathematical Modeling'],
            ['department' => 'Mathematics', 'name' => 'Cryptography & Coding Theory'],
            ['department' => 'Mathematics', 'name' => 'Differential Equations'],
            ['department' => 'Mathematics', 'name' => 'Graph Theory'],
            ['department' => 'Mathematics', 'name' => 'Optimization & Operations Research'],
            ['department' => 'Mathematics', 'name' => 'Actuarial Science'],
            ['department' => 'Mathematics', 'name' => 'Topology'],

            // ─────────────────────────────────────────────────
            // Economics  (8 entries)
            // ─────────────────────────────────────────────────
            ['department' => 'Economics', 'name' => 'Microeconomics & Game Theory'],
            ['department' => 'Economics', 'name' => 'Macroeconomics'],
            ['department' => 'Economics', 'name' => 'Development Economics'],
            ['department' => 'Economics', 'name' => 'Behavioral Economics'],
            ['department' => 'Economics', 'name' => 'Environmental Economics'],
            ['department' => 'Economics', 'name' => 'Econometrics & Data Analysis'],
            ['department' => 'Economics', 'name' => 'International Trade & Policy'],
            ['department' => 'Economics', 'name' => 'Public Finance'],

            // ─────────────────────────────────────────────────
            // General — available to ALL departments  (18 entries)
            // ─────────────────────────────────────────────────
            ['department' => 'General', 'name' => 'Research & Academic Writing'],
            ['department' => 'General', 'name' => 'Public Speaking & Debate'],
            ['department' => 'General', 'name' => 'Leadership & Team Management'],
            ['department' => 'General', 'name' => 'Community Service & Volunteering'],
            ['department' => 'General', 'name' => 'Photography & Videography'],
            ['department' => 'General', 'name' => 'Music & Performing Arts'],
            ['department' => 'General', 'name' => 'Graphic Design'],
            ['department' => 'General', 'name' => 'Content Creation & Blogging'],
            ['department' => 'General', 'name' => 'Sustainability & Environment'],
            ['department' => 'General', 'name' => 'Social Innovation'],
            ['department' => 'General', 'name' => 'Mental Health Awareness'],
            ['department' => 'General', 'name' => 'Sports & Athletics'],
            ['department' => 'General', 'name' => 'Travel & Cultural Exchange'],
            ['department' => 'General', 'name' => 'Foreign Languages'],
            ['department' => 'General', 'name' => 'Problem Solving & Critical Thinking'],
            ['department' => 'General', 'name' => 'Networking & Professional Development'],
            ['department' => 'General', 'name' => 'Startup Ecosystem'],
            ['department' => 'General', 'name' => 'Data Literacy'],
        ];

        // Insert in one batch for efficiency
        foreach (array_chunk($interests, 50) as $chunk) {
            DepartmentInterest::insert($chunk);
        }

        $this->command->info('DepartmentInterestSeeder: ' . count($interests) . ' interests seeded across ' .
            count(array_unique(array_column($interests, 'department'))) . ' departments.');
    }
}
