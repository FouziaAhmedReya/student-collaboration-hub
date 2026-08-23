<?php

namespace Database\Seeders;

use App\Models\StudyGroup;
use App\Models\StudyGroupMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudyGroupSeeder extends Seeder
{
    public function run(): void
    {
        $mainUser = User::where('email', 'rayhanul@bracu.ac.bd')->first();
        if (!$mainUser) {
            $mainUser = User::first();
        }

        // Create sample peers
        $student1 = User::firstOrCreate(
            ['email' => 'tahmid@bracu.ac.bd'],
            ['name' => 'Tahmid Rahman', 'password' => Hash::make('password')]
        );
        if (!$student1->profile) {
            $student1->profile()->create(['department' => 'CSE', 'semester' => '9th Semester', 'university' => 'BRAC University']);
        }

        $student2 = User::firstOrCreate(
            ['email' => 'anika@bracu.ac.bd'],
            ['name' => 'Anika Tabassum', 'password' => Hash::make('password')]
        );
        if (!$student2->profile) {
            $student2->profile()->create(['department' => 'CSE', 'semester' => '10th Semester', 'university' => 'BRAC University']);
        }

        $student3 = User::firstOrCreate(
            ['email' => 'nafis@bracu.ac.bd'],
            ['name' => 'Nafis Iqbal', 'password' => Hash::make('password')]
        );
        if (!$student3->profile) {
            $student3->profile()->create(['department' => 'EEE', 'semester' => '8th Semester', 'university' => 'BRAC University']);
        }

        // 1. Group created by Rayhanul
        $group1 = StudyGroup::firstOrCreate(
            ['name' => 'System Analysis & Design Squad (CSE471)'],
            [
                'creator_id' => $mainUser->id,
                'course' => 'CSE471 - System Analysis and Design',
                'max_members' => 6,
                'meeting_date' => now()->addDays(2)->toDateString(),
                'meeting_time' => '15:30',
                'description' => 'Weekly team discussions on SRS documentation, UML diagram design, architectural patterns, and project milestone preparations.',
                'visibility' => 'public',
                'location_name' => 'BRAC University Library Study Room 4',
                'location_address' => 'UB02 Building, 3rd Floor, Mohakhali, Dhaka',
                'latitude' => 23.7806,
                'longitude' => 90.4068,
            ]
        );

        StudyGroupMember::firstOrCreate(
            ['study_group_id' => $group1->id, 'user_id' => $mainUser->id],
            ['role' => 'admin', 'status' => 'active', 'joined_at' => now()->subDays(5)]
        );

        StudyGroupMember::firstOrCreate(
            ['study_group_id' => $group1->id, 'user_id' => $student1->id],
            ['role' => 'member', 'status' => 'active', 'joined_at' => now()->subDays(2)]
        );

        StudyGroupMember::firstOrCreate(
            ['study_group_id' => $group1->id, 'user_id' => $student2->id],
            ['role' => 'member', 'status' => 'pending', 'joined_at' => null]
        );

        // 2. Public AI & ML group created by Tahmid
        $group2 = StudyGroup::firstOrCreate(
            ['name' => 'Artificial Intelligence & Neural Nets Study Circle'],
            [
                'creator_id' => $student1->id,
                'course' => 'CSE422 - Artificial Intelligence',
                'max_members' => 8,
                'meeting_date' => now()->addDays(4)->toDateString(),
                'meeting_time' => '17:00',
                'description' => 'Solving past mid-term exam questions, exploring heuristic search algorithms, min-max alpha-beta pruning, and basic PyTorch models.',
                'visibility' => 'public',
                'location_name' => 'UB02 7th Floor Study Lounge',
                'location_address' => 'UB02 Building, Mohakhali, Dhaka',
                'latitude' => 23.7808,
                'longitude' => 90.4072,
            ]
        );

        StudyGroupMember::firstOrCreate(
            ['study_group_id' => $group2->id, 'user_id' => $student1->id],
            ['role' => 'admin', 'status' => 'active', 'joined_at' => now()->subDays(3)]
        );

        // 3. Private Research group created by Rayhanul
        $group3 = StudyGroup::firstOrCreate(
            ['name' => 'Full-Stack Web Engineering Thesis & Capstone'],
            [
                'creator_id' => $mainUser->id,
                'course' => 'CSE470 - Software Engineering',
                'max_members' => 4,
                'meeting_date' => now()->addDays(6)->toDateString(),
                'meeting_time' => '11:00',
                'description' => 'Private capstone working group dedicated to backend API development, CI/CD pipeline automation, and code reviews.',
                'visibility' => 'private',
                'location_name' => 'Online Google Meet / Discord Room',
                'location_address' => 'Virtual Meeting Link shared in group description',
                'latitude' => 23.7800,
                'longitude' => 90.4000,
            ]
        );

        StudyGroupMember::firstOrCreate(
            ['study_group_id' => $group3->id, 'user_id' => $mainUser->id],
            ['role' => 'admin', 'status' => 'active', 'joined_at' => now()->subDays(7)]
        );

        StudyGroupMember::firstOrCreate(
            ['study_group_id' => $group3->id, 'user_id' => $student3->id],
            ['role' => 'member', 'status' => 'pending', 'joined_at' => null]
        );
    }
}
