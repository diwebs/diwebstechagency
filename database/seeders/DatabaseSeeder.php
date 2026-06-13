<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users for each role
        $admin = User::create([
            'name' => 'Diwebs Administrator',
            'email' => 'admin@diwebs.com',
            'password' => 'password',
            'role' => 'super_admin',
            'status' => 'active'
        ]);

        $student = User::create([
            'name' => 'Tobi Alabi',
            'email' => 'student@diwebs.com',
            'password' => 'password',
            'role' => 'student',
            'status' => 'active'
        ]);

        $client = User::create([
            'name' => 'Sarah Jenkins (E-Gov Group)',
            'email' => 'client@diwebs.com',
            'password' => 'password',
            'role' => 'client',
            'status' => 'active'
        ]);

        $candidate = User::create([
            'name' => 'Michael Okafor',
            'email' => 'candidate@diwebs.com',
            'password' => 'password',
            'role' => 'candidate',
            'status' => 'active'
        ]);

        // 2. Seed CBT Centers & Seats & Devices
        $center1 = \App\Models\CbtCenter::create([
            'name' => 'Lagos Innovation Hub',
            'code' => 'CBT-01',
            'address' => '102 Herbert Macaulay Way, Yaba',
            'city' => 'Lagos',
            'capacity' => 150,
            'contact_email' => 'lagos@diwebs.com',
            'contact_phone' => '+2348011223344',
            'status' => 'active'
        ]);

        $center2 = \App\Models\CbtCenter::create([
            'name' => 'Abuja Digital Academy',
            'code' => 'CBT-02',
            'address' => 'Block C, Garki Mall',
            'city' => 'Abuja',
            'capacity' => 200,
            'contact_email' => 'abuja@diwebs.com',
            'contact_phone' => '+2348055667788',
            'status' => 'active'
        ]);

        // Create seats and devices for Lagos
        for ($i = 1; $i <= 5; $i++) {
            $seat = \App\Models\Seat::create([
                'cbt_center_id' => $center1->id,
                'seat_number' => 'L-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'available'
            ]);

            \App\Models\Device::create([
                'seat_id' => $seat->id,
                'ip_address' => '192.168.1.' . (10 + $i),
                'mac_address' => '00:1A:2B:3C:4D:0' . $i,
                'device_name' => 'LAG-WS-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'system_status' => 'online'
            ]);
        }

        // 3. Seed CBT Exams & Questions
        $exam = \App\Models\Exam::create([
            'title' => 'Software Engineering Placement Exam',
            'description' => 'Assess core programming capabilities, database design, and web app architectures.',
            'code' => 'SWE-2026',
            'duration_minutes' => 30,
            'total_questions' => 5,
            'passing_score' => 60.00,
            'is_active' => true,
            'settings' => [
                'randomize' => true,
                'webcam_monitoring' => true,
                'tab_limits' => 3,
                'browser_lock' => false
            ]
        ]);

        \App\Models\Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'Which of the following is NOT a valid HTTP status code group?',
            'question_type' => 'single_choice',
            'options' => [
                ['id' => 'A', 'text' => '2xx Success'],
                ['id' => 'B', 'text' => '3xx Redirection'],
                ['id' => 'C', 'text' => '6xx Server Errors'],
                ['id' => 'D', 'text' => '4xx Client Errors']
            ],
            'correct_answers' => ['C'],
            'difficulty' => 'easy'
        ]);

        \App\Models\Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'Which database index type is ideal for exact primary key matching?',
            'question_type' => 'single_choice',
            'options' => [
                ['id' => 'A', 'text' => 'B-Tree'],
                ['id' => 'B', 'text' => 'Hash'],
                ['id' => 'C', 'text' => 'Fulltext'],
                ['id' => 'D', 'text' => 'Spatial']
            ],
            'correct_answers' => ['B'],
            'difficulty' => 'medium'
        ]);

        \App\Models\Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'Select the benefits of containers over virtual machines:',
            'question_type' => 'multiple_choice',
            'options' => [
                ['id' => 'A', 'text' => 'Faster startup times'],
                ['id' => 'B', 'text' => 'Less CPU/RAM overhead'],
                ['id' => 'C', 'text' => 'Hard hardware-level isolation'],
                ['id' => 'D', 'text' => 'Shared kernel architecture']
            ],
            'correct_answers' => ['A', 'B', 'D'],
            'difficulty' => 'hard'
        ]);

        \App\Models\Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'What does CSRF protect against?',
            'question_type' => 'single_choice',
            'options' => [
                ['id' => 'A', 'text' => 'Unauthorized user commands triggered by trusted websites'],
                ['id' => 'B', 'text' => 'Malicious Javascript injected into public pages'],
                ['id' => 'C', 'text' => 'SQL injection queries'],
                ['id' => 'D', 'text' => 'Distributed Denial of Service (DDoS)']
            ],
            'correct_answers' => ['A'],
            'difficulty' => 'medium'
        ]);

        \App\Models\Question::create([
            'exam_id' => $exam->id,
            'question_text' => 'In Laravel, where is the application pipeline middleware registered in modern versions?',
            'question_type' => 'single_choice',
            'options' => [
                ['id' => 'A', 'text' => 'app/Http/Kernel.php'],
                ['id' => 'B', 'text' => 'bootstrap/app.php'],
                ['id' => 'C', 'text' => 'routes/web.php'],
                ['id' => 'D', 'text' => 'config/middleware.php']
            ],
            'correct_answers' => ['B'],
            'difficulty' => 'medium'
        ]);

        // 4. Seed Academy Courses & Lessons
        $course1 = \App\Models\Course::create([
            'title' => 'Advanced Software Engineering',
            'slug' => 'advanced-software-engineering',
            'description' => 'Master design patterns, system design principles, dynamic database modeling, and scaling concepts.',
            'instructor_name' => 'Prof. Jude Carter',
            'price' => 299.99,
            'cover_image' => 'course_swe.jpg',
            'syllabus' => ['Object-Oriented Design', 'Domain-Driven Design', 'Caching & Message Brokers']
        ]);

        \App\Models\Lesson::create([
            'course_id' => $course1->id,
            'title' => 'Introduction to Solid Principles',
            'slug' => 'solid-principles-intro',
            'content' => 'SOLID is a popular mnemonic acronym for five design principles. In this lesson, we study Single Responsibility and Open-Closed principles.',
            'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
            'duration_seconds' => 600,
            'sort_order' => 1
        ]);

        \App\Models\Lesson::create([
            'course_id' => $course1->id,
            'title' => 'Database Normalization and Indexing',
            'slug' => 'database-normalization',
            'content' => 'Deep dive into 1NF, 2NF, 3NF and Boyce-Codd Normal Forms. Learn about clustered vs non-clustered index logic.',
            'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
            'duration_seconds' => 1200,
            'sort_order' => 2
        ]);

        $course2 = \App\Models\Course::create([
            'title' => 'AI Engineering & Neural Networks',
            'slug' => 'ai-engineering-neural-networks',
            'description' => 'Build high-performance AI engines. Learn matrix optimization, model evaluation, and LLM integrations.',
            'instructor_name' => 'Dr. Amina Yusuf',
            'price' => 450.00,
            'cover_image' => 'course_ai.jpg',
            'syllabus' => ['Gradient Descent', 'Backpropagation', 'Attention Mechanisms', 'Transformer architectures']
        ]);

        // Enroll Student Tobi Alabi into course 1
        \App\Models\Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course1->id,
            'progress' => 50,
        ]);

        // 5. Seed Clients Projects, Milestones, Invoices
        $projectId = (string) \Illuminate\Support\Str::uuid();
        $project = \App\Models\Project::create([
            'id' => $projectId,
            'client_id' => $client->id,
            'title' => 'Federal CBT Infrastructure Portal',
            'description' => 'Develop a distributed, multi-center CBT infrastructure with localized center synchronization, tab locks, and webcam logs.',
            'status' => 'active',
            'budget' => 150000.00,
            'agreement_signed_at' => now()->subDays(5)
        ]);

        $m1 = \App\Models\Milestone::create([
            'project_id' => $project->id,
            'title' => 'System Design & Database Schema Approval',
            'description' => 'Deliver complete ERD and physical deployment manuals.',
            'due_date' => now()->subDays(2),
            'status' => 'approved',
            'amount' => 45000.00
        ]);

        $m2 = \App\Models\Milestone::create([
            'project_id' => $project->id,
            'title' => 'CBT Exam Engine Prototype & Anti-Cheat Hooks',
            'description' => 'Development of visual webcam captures and visibility hooks.',
            'due_date' => now()->addDays(15),
            'status' => 'working',
            'amount' => 60000.00
        ]);

        \App\Models\Invoice::create([
            'project_id' => $project->id,
            'milestone_id' => $m1->id,
            'client_id' => $client->id,
            'amount' => 45000.00,
            'invoice_number' => 'INV-2026-001',
            'status' => 'paid',
            'due_date' => now()->subDays(2),
            'paid_at' => now()->subDays(2)
        ]);

        \App\Models\Invoice::create([
            'project_id' => $project->id,
            'milestone_id' => $m2->id,
            'client_id' => $client->id,
            'amount' => 60000.00,
            'invoice_number' => 'INV-2026-002',
            'status' => 'unpaid',
            'due_date' => now()->addDays(15)
        ]);

        // 6. Seed Leads & Tickets
        \App\Models\Lead::create([
            'name' => 'John Doe',
            'email' => 'johndoe@company.com',
            'phone' => '+15551234',
            'company' => 'Enterprise Solutions Corp',
            'service_needed' => 'AI Solutions & Automation',
            'message' => 'We are interested in building an automated customer review system powered by LLMs.',
            'status' => 'new'
        ]);

        \App\Models\Ticket::create([
            'user_id' => $client->id,
            'subject' => 'AWS RDS scaling limits query',
            'message' => 'Can you clarify if the database structure supports automatic scaling on Aurora PostgreSQL?',
            'status' => 'open',
            'priority' => 'high'
        ]);
    }
}
