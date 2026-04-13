<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ExamPaper;
use App\Models\ExamPaperQuestion;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Services\AIQuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller
{
    // =================== QUESTION BANKS ===================

    public function index()
    {
        $school = app('school');
        $banks  = QuestionBank::with('subject', 'schoolClass', 'creator')
            ->withCount('questions')
            ->withCount(['questions as mcq_count'   => fn($q) => $q->where('type', 'mcq')])
            ->withCount(['questions as short_count'  => fn($q) => $q->where('type', 'short')])
            ->withCount(['questions as long_count'   => fn($q) => $q->where('type', 'long')])
            ->latest()->paginate(15);

        return view('school.question_bank.index', compact('banks', 'school'));
    }

    public function create()
    {
        $school   = app('school');
        $subjects = Subject::all();
        $classes  = SchoolClass::all();
        return view('school.question_bank.create', compact('subjects', 'classes', 'school'));
    }

    /**
     * Upload multiple images → extract text from each → combine → generate questions
     */
    public function processImage(Request $request)
    {
        $request->validate([
            'subject_id'      => 'required|exists:subjects,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'title'           => 'required|string|max:200',
            'chapter'         => 'nullable|string|max:100',
            'difficulty'      => 'required|in:easy,medium,hard',
            'language'        => 'required|in:english,urdu,arabic',
            'mcq_count'       => 'required|integer|min:0|max:30',
            'short_count'     => 'required|integer|min:0|max:20',
            'long_count'      => 'required|integer|min:0|max:10',
            'source_images'   => 'required|array|min:1|max:10',
            'source_images.*' => 'image|max:10240',
        ]);

        // Increase execution time for AI processing
        set_time_limit(600);
        ini_set('max_execution_time', 600);

        $school = app('school');
        $ai     = new AIQuestionService();

        // Extract text from ALL uploaded images using Tesseract OCR (fast)
        $allText    = '';
        $imagePaths = [];
        $errors     = [];

        foreach ($request->file('source_images') as $index => $image) {
            $path         = $image->store('question-banks', 'public');
            $imagePaths[] = $path;
            $fullPath     = Storage::disk('public')->path($path);

            try {
                // Use Tesseract OCR - much faster than LLaVA vision
                $extracted = $ai->extractTextFromImage($fullPath, $request->language);

                if ($extracted) {
                    $allText .= ($index > 0 ? "\n\n--- Page " . ($index + 1) . " ---\n\n" : '') . $extracted;
                    \Log::info("OCR image {$index}: " . strlen($extracted) . " chars");
                } else {
                    $errors[] = "Image " . ($index + 1) . ": No text found (check image quality)";
                }
            } catch (\Exception $e) {
                $errors[] = "Image " . ($index + 1) . ": " . $e->getMessage();
                \Log::error("OCR failed image {$index}: " . $e->getMessage());
            }
        }

        $allText = trim($allText);
        \Log::info("Total extracted text: " . strlen($allText) . " chars");

        $subject = Subject::find($request->subject_id);
        $class   = SchoolClass::find($request->school_class_id);

        // If no text extracted, use subject name as fallback
        $textForAI = $allText ?: "Generate questions about {$subject->name} for {$class->name} students.";

        try {
            $questions = $ai->generateQuestions(
                text:       $textForAI,
                mcqCount:   (int) $request->mcq_count,
                shortCount: (int) $request->short_count,
                longCount:  (int) $request->long_count,
                difficulty: $request->difficulty,
                language:   $request->language,
                subject:    $subject->name,
                className:  $class->name,
            );
            \Log::info("Generated " . count($questions) . " questions");
        } catch (\Exception $e) {
            \Log::error("Question generation failed: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'AI question generation failed: ' . $e->getMessage())
                ->withInput();
        }

        if (empty($questions)) {
            return redirect()->back()
                ->with('error', 'AI could not generate questions. Please try again or check Ollama is running.')
                ->withInput();
        }

        session([
            'qb_form'      => $request->except('source_images') + ['image_paths' => $imagePaths],
            'qb_extracted' => $allText,
            'qb_questions' => $questions,
        ]);

        $successMsg = count($questions) . ' questions generated successfully!';
        if ($errors) {
            $successMsg .= ' (Note: ' . implode(', ', $errors) . ')';
        }

        return redirect()->route('school.question-bank.review', $school->slug)
            ->with('success', $successMsg);
    }

    public function review()
    {
        $school    = app('school');
        $form      = session('qb_form');
        $questions = session('qb_questions', []);

        if (!$form) {
            return redirect()->route('school.question-bank.create', $school->slug)
                ->with('error', 'Session expired. Please start again.');
        }

        return view('school.question_bank.review', compact('school', 'form', 'questions'));
    }

    public function saveQuestions(Request $request)
    {
        $school = app('school');
        $form   = session('qb_form');

        if (!$form) {
            return redirect()->route('school.question-bank.create', $school->slug)
                ->with('error', 'Session expired.');
        }

        return DB::transaction(function () use ($request, $school, $form) {
            $bank = QuestionBank::create([
                'school_id'       => $school->id,
                'subject_id'      => $form['subject_id'],
                'school_class_id' => $form['school_class_id'],
                'created_by'      => auth()->id(),
                'title'           => $form['title'],
                'chapter'         => $form['chapter'] ?? null,
                'difficulty'      => $form['difficulty'],
                'language'        => $form['language'],
                'source_image'    => $form['image_paths'][0] ?? null,
                'extracted_text'  => session('qb_extracted'),
            ]);

            $questions = $request->input('questions', []);
            $saved     = 0;

            foreach ($questions as $q) {
                if (empty(trim($q['question_text'] ?? ''))) continue;

                Question::create([
                    'school_id'        => $school->id,
                    'question_bank_id' => $bank->id,
                    'type'             => $q['type'],
                    'question_text'    => $q['question_text'],
                    'option_a'         => $q['option_a'] ?? null,
                    'option_b'         => $q['option_b'] ?? null,
                    'option_c'         => $q['option_c'] ?? null,
                    'option_d'         => $q['option_d'] ?? null,
                    'correct_answer'   => $q['correct_answer'] ?? null,
                    'answer_hint'      => $q['answer_hint'] ?? null,
                    'marks'            => $q['marks'] ?? 1,
                    'difficulty'       => $form['difficulty'],
                    'is_approved'      => true,
                ]);
                $saved++;
            }

            session()->forget(['qb_form', 'qb_extracted', 'qb_questions']);

            return redirect()->route('school.question-bank.show', [$school->slug, $bank])
                ->with('success', "Question bank saved with {$saved} questions.");
        });
    }

    public function show(string $school, QuestionBank $questionBank)
    {
        $school = app('school');
        $questionBank->load('subject', 'schoolClass', 'questions');
        return view('school.question_bank.show', compact('school', 'questionBank'));
    }

    public function destroy(string $school, QuestionBank $questionBank)
    {
        $questionBank->delete();
        return redirect()->route('school.question-bank.index', app('school')->slug)
            ->with('success', 'Question bank deleted');
    }

    // =================== PAPER GENERATION ===================

    public function paperIndex()
    {
        $school = app('school');
        $papers = ExamPaper::with('subject', 'schoolClass', 'creator')
            ->latest()->paginate(15);
        return view('school.question_bank.papers.index', compact('papers', 'school'));
    }

    public function generateForm(Request $request)
    {
        $school   = app('school');
        $subjects = Subject::all();
        $classes  = SchoolClass::all();
        $banks    = QuestionBank::with('subject', 'schoolClass')
            ->withCount(['questions as mcq_count'   => fn($q) => $q->where('type', 'mcq')->where('is_approved', true)])
            ->withCount(['questions as short_count'  => fn($q) => $q->where('type', 'short')->where('is_approved', true)])
            ->withCount(['questions as long_count'   => fn($q) => $q->where('type', 'long')->where('is_approved', true)])
            ->get();

        return view('school.question_bank.papers.generate', compact('school', 'subjects', 'classes', 'banks'));
    }

    /**
     * Preview paper - pick random questions from MULTIPLE selected banks
     */
    public function previewPaper(Request $request)
    {
        $request->validate([
            'question_bank_ids'  => 'required|array|min:1',
            'question_bank_ids.*'=> 'exists:question_banks,id',
            'title'              => 'required|string|max:200',
            'language'           => 'required|in:english,urdu,arabic',
            'mcq_count'          => 'required|integer|min:0',
            'short_count'        => 'required|integer|min:0',
            'long_count'         => 'required|integer|min:0',
            'mcq_marks'          => 'required|integer|min:1',
            'short_marks'        => 'required|integer|min:1',
            'long_marks'         => 'required|integer|min:1',
            'duration_minutes'   => 'required|integer|min:15',
            'exam_date'          => 'nullable|date',
            'instructions'       => 'nullable|string',
        ]);

        $school      = app('school');
        $bankIds     = $request->question_bank_ids;
        $banks       = QuestionBank::with('subject', 'schoolClass')->whereIn('id', $bankIds)->get();
        $primaryBank = $banks->first();

        // Pick random questions from ALL selected banks combined
        $mcqs   = Question::whereIn('question_bank_id', $bankIds)
            ->where('type', 'mcq')->where('is_approved', true)
            ->inRandomOrder()->take((int) $request->mcq_count)->get();

        $shorts = Question::whereIn('question_bank_id', $bankIds)
            ->where('type', 'short')->where('is_approved', true)
            ->inRandomOrder()->take((int) $request->short_count)->get();

        $longs  = Question::whereIn('question_bank_id', $bankIds)
            ->where('type', 'long')->where('is_approved', true)
            ->inRandomOrder()->take((int) $request->long_count)->get();

        $totalMarks = ($mcqs->count()   * (int) $request->mcq_marks)
                    + ($shorts->count() * (int) $request->short_marks)
                    + ($longs->count()  * (int) $request->long_marks);

        $paperData = array_merge($request->all(), [
            'bank'        => $primaryBank,
            'banks'       => $banks,
            'mcqs'        => $mcqs,
            'shorts'      => $shorts,
            'longs'       => $longs,
            'total_marks' => $totalMarks,
        ]);

        session(['paper_preview' => $paperData]);

        $bank = $primaryBank;
        return view('school.question_bank.papers.preview', compact('school', 'paperData', 'bank'));
    }

    public function savePaper(Request $request)
    {
        $school    = app('school');
        $paperData = session('paper_preview');

        if (!$paperData) {
            return redirect()->route('school.question-bank.generate', $school->slug)
                ->with('error', 'Session expired. Please regenerate.');
        }

        return DB::transaction(function () use ($school, $paperData) {
            $bank = $paperData['bank'];

            $paper = ExamPaper::create([
                'school_id'        => $school->id,
                'subject_id'       => $bank->subject_id,
                'school_class_id'  => $bank->school_class_id,
                'created_by'       => auth()->id(),
                'title'            => $paperData['title'],
                'language'         => $paperData['language'],
                'total_marks'      => $paperData['total_marks'],
                'duration_minutes' => $paperData['duration_minutes'],
                'mcq_count'        => $paperData['mcqs']->count(),
                'short_count'      => $paperData['shorts']->count(),
                'long_count'       => $paperData['longs']->count(),
                'mcq_marks'        => $paperData['mcq_marks'],
                'short_marks'      => $paperData['short_marks'],
                'long_marks'       => $paperData['long_marks'],
                'exam_date'        => $paperData['exam_date'] ?? null,
                'instructions'     => $paperData['instructions'] ?? null,
            ]);

            $order = 1;
            foreach ($paperData['mcqs'] as $q) {
                ExamPaperQuestion::create(['exam_paper_id' => $paper->id, 'question_id' => $q->id, 'order' => $order++, 'marks' => $paperData['mcq_marks']]);
            }
            foreach ($paperData['shorts'] as $q) {
                ExamPaperQuestion::create(['exam_paper_id' => $paper->id, 'question_id' => $q->id, 'order' => $order++, 'marks' => $paperData['short_marks']]);
            }
            foreach ($paperData['longs'] as $q) {
                ExamPaperQuestion::create(['exam_paper_id' => $paper->id, 'question_id' => $q->id, 'order' => $order++, 'marks' => $paperData['long_marks']]);
            }

            session()->forget('paper_preview');

            return redirect()->route('school.question-bank.paper.print', [$school->slug, $paper])
                ->with('success', 'Paper saved!');
        });
    }

    public function printPaper(string $school, ExamPaper $paper)
    {
        $school = app('school');
        $paper->load('subject', 'schoolClass', 'creator', 'paperQuestions.question');

        $mcqs   = $paper->paperQuestions->filter(fn($pq) => $pq->question?->type === 'mcq');
        $shorts = $paper->paperQuestions->filter(fn($pq) => $pq->question?->type === 'short');
        $longs  = $paper->paperQuestions->filter(fn($pq) => $pq->question?->type === 'long');

        return view('school.question_bank.papers.print', compact('school', 'paper', 'mcqs', 'shorts', 'longs'));
    }
}
