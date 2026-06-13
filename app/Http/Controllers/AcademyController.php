<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademyController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $enrollments = Enrollment::with('course.lessons')->where('user_id', $user->id)->get();
        $availableCourses = Course::whereNotIn('id', $enrollments->pluck('course_id'))->get();

        return view('academy.dashboard', compact('enrollments', 'availableCourses'));
    }

    public function courseDetail($slug)
    {
        $course = Course::with('lessons')->where('slug', $slug)->firstOrFail();
        $isEnrolled = false;
        $progress = 0;

        if (auth()->check()) {
            $enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->first();
            if ($enrollment) {
                $isEnrolled = true;
                $progress = $enrollment->progress;
            }
        }

        return view('academy.course-detail', compact('course', 'isEnrolled', 'progress'));
    }

    public function enroll(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = $request->user();

        Enrollment::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'progress' => 0
        ]);

        return redirect()->route('academy.course', $course->slug)->with('success', 'Enrolled successfully!');
    }

    public function lessonDetail(Request $request, $courseSlug, $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $lesson = Lesson::where('course_id', $course->id)->where('slug', $lessonSlug)->firstOrFail();
        
        $enrollment = Enrollment::where('user_id', $request->user()->id)->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return redirect()->route('academy.course', $course->slug)->with('error', 'You must enroll to view lessons.');
        }

        $allLessons = $course->lessons;
        $nextLesson = $allLessons->where('sort_order', '>', $lesson->sort_order)->first();

        // Increment progress slightly
        $totalLessons = $allLessons->count();
        $completedLessonsCount = Lesson::where('course_id', $course->id)
            ->where('sort_order', '<=', $lesson->sort_order)
            ->count();
        $newProgress = min(100, round(($completedLessonsCount / $totalLessons) * 100));

        if ($newProgress > $enrollment->progress) {
            $enrollment->update([
                'progress' => $newProgress,
                'completed_at' => $newProgress === 100 ? now() : $enrollment->completed_at,
                'certificate_code' => ($newProgress === 100 && !$enrollment->certificate_code) 
                    ? 'CERT-' . strtoupper(Str::random(10)) 
                    : $enrollment->certificate_code
            ]);
        }

        return view('academy.lesson-detail', compact('course', 'lesson', 'allLessons', 'nextLesson', 'enrollment'));
    }

    public function askAiTutor(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'lesson_id' => 'nullable|integer'
        ]);

        $question = $request->input('question');
        
        // Dynamic mock AI response referencing the course topics
        $response = "This is a response from Diwebs AI Tutor regarding your query. For a course on software engineering, we recommend structured coding principles: " . 
                    "1. Always split code into clean modules. " .
                    "2. Use database migrations for schema definitions. " . 
                    "3. Implement dual deployment setups by resolving config dynamically from the environment files.";

        return response()->json([
            'answer' => $response
        ]);
    }
}
