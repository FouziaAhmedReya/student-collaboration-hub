<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Rayhanul Hoque',
            'email' => 'rayhanul@bracu.ac.bd',
            'password' => Hash::make('password'),
        ]);

        $profile = $user->profile()->create([
            'department' => 'CSE',
            'semester' => '10th Semester',
            'university' => 'BRAC University',
            'joined_date' => '2022-01-15',
            'about_me' => 'Passionate about building scalable web applications and AI solutions. I love solving real-world problems and collaborating with like-minded people.',
            'preferred_location_name' => 'BRAC University Library',
            'preferred_location_address' => 'Mohakhali, Dhaka 1212',
            'latitude' => 23.7806,
            'longitude' => 90.4068,
        ]);

        $profile->skills()->createMany([
            ['name' => 'Python', 'proficiency' => 95],
            ['name' => 'Laravel', 'proficiency' => 82],
            ['name' => 'Java', 'proficiency' => 70],
            ['name' => 'Javascript', 'proficiency' => 90],
            ['name' => 'PHP', 'proficiency' => 80],
            ['name' => 'MySQL', 'proficiency' => 72],
        ]);

        $profile->interests()->createMany([
            ['name' => 'AI'],
            ['name' => 'Web Development'],
            ['name' => 'Machine Learning'],
            ['name' => 'Competitive Programming'],
            ['name' => 'Data Science'],
            ['name' => 'UI/UX Design'],
        ]);

        $profile->projects()->createMany([
            [
                'name' => 'Student Collaboration Hub',
                'description' => 'Full Stack Web Application',
                'technologies' => 'Laravel, Vue.js, MySQL'
            ],
            [
                'name' => 'Library Management System',
                'description' => 'A complete library management solution',
                'technologies' => 'Laravel, MySQL, Bootstrap'
            ],
            [
                'name' => 'AI Chatbot',
                'description' => 'An intelligent chatbot using NLP',
                'technologies' => 'Python, NLP, Machine Learning'
            ],
            [
                'name' => 'Online Quiz Platform',
                'description' => 'Interactive quiz platform',
                'technologies' => 'PHP, MySQL, Javascript'
            ],
        ]);

        $profile->portfolioLinks()->createMany([
            ['platform' => 'GitHub', 'url' => 'https://github.com/rayhanulhoque'],
            ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/rayhanulhoque'],
            ['platform' => 'Portfolio Website', 'url' => 'https://rayhan.dev'],
            ['platform' => 'LeetCode', 'url' => 'https://leetcode.com/u/rayhanulhoque'],
        ]);
    }
}
