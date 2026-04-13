<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use thiagoalessio\TesseractOCR\TesseractOCR;

class AIQuestionService
{
    private string $provider;

    public function __construct()
    {
        // Auto-detect: use groq if key set, else ollama
        $groqKey = env('GROQ_API_KEY', '');
        if (!empty($groqKey) && $groqKey !== 'your_groq_key_here') {
            $this->provider = 'groq';
        } else {
            $this->provider = env('AI_PROVIDER', 'ollama');
        }
    }

    // ================================================================
    // TEXT EXTRACTION - Tesseract OCR (instant, local, free)
    // ================================================================

    public function extractTextFromImage(string $imagePath, string $language = 'english'): string
    {
        try {
            $lang = match(strtolower($language)) {
                'urdu'   => 'urd',
                'arabic' => 'ara',
                default  => 'eng',
            };

            $ocr = new TesseractOCR($imagePath);
            $ocr->lang($lang);
            $ocr->psm(3);
            $ocr->oem(3);
            $text = $ocr->run();

            Log::info("OCR: " . strlen($text) . " chars from " . basename($imagePath));
            return trim($text);

        } catch (\Exception $e) {
            Log::error("OCR failed: " . $e->getMessage());
            return '';
        }
    }

    // ================================================================
    // QUESTION GENERATION
    // ================================================================

    public function generateQuestions(
        string $text,
        int    $mcqCount,
        int    $shortCount,
        int    $longCount,
        string $difficulty,
        string $language,
        string $subject,
        string $className
    ): array {
        $prompt = $this->buildPrompt($text, $mcqCount, $shortCount, $longCount, $difficulty, $language, $subject, $className);

        try {
            $start = microtime(true);

            $raw = match($this->provider) {
                'groq'   => $this->groqText($prompt),
                'gemini' => $this->geminiText($prompt),
                default  => $this->ollamaText($prompt),
            };

            Log::info("AI [{$this->provider}] took " . round(microtime(true) - $start, 1) . "s");

            $parsed = $this->parseQuestions($raw);
            return !empty($parsed) ? $parsed : $this->demoQuestions($mcqCount, $shortCount, $longCount);

        } catch (\Exception $e) {
            Log::error("generateQuestions [{$this->provider}]: " . $e->getMessage());
            return $this->demoQuestions($mcqCount, $shortCount, $longCount);
        }
    }

    // ================================================================
    // GROQ - Free, No limits, Super fast (recommended)
    // Get free key: https://console.groq.com
    // ================================================================

    private function groqText(string $prompt): string
    {
        $key   = env('GROQ_API_KEY', '');
        $model = env('GROQ_MODEL', 'llama-3.1-8b-instant'); // fastest free model

        if (empty($key)) throw new \Exception('GROQ_API_KEY not set. Get free key at console.groq.com');

        $response = Http::withToken($key)
            ->timeout(30)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $model,
                'messages'    => [['role' => 'user', 'content' => $prompt]],
                'max_tokens'  => 4000,
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            throw new \Exception('Groq API failed: ' . $response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    // ================================================================
    // OLLAMA - Local fallback
    // ================================================================

    private function ollamaText(string $prompt): string
    {
        $host  = env('OLLAMA_HOST', 'http://localhost:11434');
        $model = env('OLLAMA_MODEL', 'llama3.2:1b');

        $response = Http::timeout(120)->post("{$host}/api/generate", [
            'model'   => $model,
            'prompt'  => $prompt,
            'stream'  => false,
            'options' => ['temperature' => 0.7, 'num_predict' => 3000],
        ]);

        if ($response->failed()) {
            throw new \Exception('Ollama failed: ' . $response->body());
        }

        return $response->json('response', '');
    }

    // ================================================================
    // GEMINI - Google free tier fallback
    // ================================================================

    private function geminiText(string $prompt): string
    {
        $key   = env('GEMINI_API_KEY', '');
        $model = env('GEMINI_MODEL', 'gemini-1.5-flash');

        if (empty($key)) throw new \Exception('GEMINI_API_KEY not set');

        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}", [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 4000],
            ]);

        if ($response->failed()) {
            throw new \Exception('Gemini failed: ' . $response->body());
        }

        return $response->json('candidates.0.content.parts.0.text', '');
    }

    // ================================================================
    // HELPERS
    // ================================================================

    private function buildPrompt(
        string $text, int $mcqCount, int $shortCount, int $longCount,
        string $difficulty, string $language, string $subject, string $className
    ): string {
        $langNote = match(strtolower($language)) {
            'urdu'   => 'Generate ALL questions and answers in Urdu language (Nastaliq script).',
            'arabic' => 'Generate ALL questions and answers in Arabic language.',
            default  => 'Generate ALL questions and answers in English.',
        };

        $types = [];
        if ($mcqCount > 0)   $types[] = "- {$mcqCount} MCQ (4 options a,b,c,d with correct answer)";
        if ($shortCount > 0) $types[] = "- {$shortCount} Short answer (2-3 lines)";
        if ($longCount > 0)  $types[] = "- {$longCount} Long answer (detailed paragraph)";

        return "Expert teacher for {$className}, subject: {$subject}, difficulty: {$difficulty}.\n"
            . "{$langNote}\n\n"
            . "Generate EXACTLY:\n" . implode("\n", $types) . "\n\n"
            . "Content:\n{$text}\n\n"
            . "Return ONLY valid JSON array, no markdown:\n"
            . '[{"type":"mcq","question_text":"Q?","option_a":"A","option_b":"B","option_c":"C","option_d":"D","correct_answer":"a","answer_hint":"hint"},'
            . '{"type":"short","question_text":"Q?","option_a":null,"option_b":null,"option_c":null,"option_d":null,"correct_answer":null,"answer_hint":"answer"},'
            . '{"type":"long","question_text":"Q?","option_a":null,"option_b":null,"option_c":null,"option_d":null,"correct_answer":null,"answer_hint":"guide"}]';
    }

    private function parseQuestions(string $raw): array
    {
        // Remove markdown code blocks
        $clean = preg_replace('/```(?:json)?\s*|\s*```/i', '', $raw);
        $clean = trim($clean);

        // Direct decode
        $data = json_decode($clean, true);
        if (is_array($data) && !empty($data)) {
            return $this->normalizeQuestions($data);
        }

        // Find [ ... ] - handle multiline
        $start = strpos($clean, '[');
        $end   = strrpos($clean, ']');
        if ($start !== false && $end !== false && $end > $start) {
            $jsonStr = substr($clean, $start, $end - $start + 1);
            $data    = json_decode($jsonStr, true);
            if (is_array($data) && !empty($data)) {
                return $this->normalizeQuestions($data);
            }
        }

        // Try fixing common JSON issues (trailing commas, single quotes)
        $fixed = preg_replace('/,\s*([}\]])/m', '$1', $clean); // remove trailing commas
        $data  = json_decode($fixed, true);
        if (is_array($data) && !empty($data)) {
            return $this->normalizeQuestions($data);
        }

        Log::warning('parseQuestions failed. json_last_error: ' . json_last_error_msg() . ' Raw: ' . substr($raw, 0, 300));
        return [];
    }

    private function normalizeQuestions(array $data): array
    {
        // If single object returned instead of array, wrap it
        if (isset($data['type'])) {
            $data = [$data];
        }

        $normalized = [];
        foreach ($data as $q) {
            if (empty($q['question_text'])) continue;
            $normalized[] = [
                'type'           => $q['type'] ?? 'mcq',
                'question_text'  => $q['question_text'],
                'option_a'       => $q['option_a'] ?? null,
                'option_b'       => $q['option_b'] ?? null,
                'option_c'       => $q['option_c'] ?? null,
                'option_d'       => $q['option_d'] ?? null,
                'correct_answer' => $q['correct_answer'] ?? null,
                'answer_hint'    => $q['answer_hint'] ?? null,
                'marks'          => $q['marks'] ?? 1,
            ];
        }
        return $normalized;
    }

    private function demoQuestions(int $mcqCount, int $shortCount, int $longCount = 0): array
    {
        $q = [];
        for ($i = 1; $i <= $mcqCount; $i++) {
            $q[] = ['type' => 'mcq', 'question_text' => "MCQ Question {$i}", 'option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C', 'option_d' => 'Option D', 'correct_answer' => 'a', 'answer_hint' => 'Answer hint'];
        }
        for ($i = 1; $i <= $shortCount; $i++) {
            $q[] = ['type' => 'short', 'question_text' => "Short Question {$i}", 'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null, 'answer_hint' => 'Expected answer'];
        }
        for ($i = 1; $i <= $longCount; $i++) {
            $q[] = ['type' => 'long', 'question_text' => "Long Question {$i}", 'option_a' => null, 'option_b' => null, 'option_c' => null, 'option_d' => null, 'correct_answer' => null, 'answer_hint' => 'Detailed answer'];
        }
        return $q;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }
}
