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

            // For Urdu/Arabic - preprocess image for better OCR
            $processedPath = $imagePath;
            if (in_array($lang, ['urd', 'ara'])) {
                $processedPath = $this->preprocessImageForRTL($imagePath);
            }

            $ocr = new TesseractOCR($processedPath);
            $ocr->lang($lang);
            $ocr->psm(6);  // Assume uniform block of text
            $ocr->oem(1);  // LSTM only - better for Arabic/Urdu

            // For RTL languages add config
            if (in_array($lang, ['urd', 'ara'])) {
                $ocr->config('preserve_interword_spaces', '1');
            }

            $text = $ocr->run();

            // Cleanup temp file
            if ($processedPath !== $imagePath && file_exists($processedPath)) {
                unlink($processedPath);
            }

            Log::info("OCR [{$lang}]: " . strlen($text) . " chars from " . basename($imagePath));
            return trim($text);

        } catch (\Exception $e) {
            Log::error("OCR failed: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Preprocess image for better Urdu/Arabic OCR
     * Increases contrast and converts to grayscale
     */
    private function preprocessImageForRTL(string $imagePath): string
    {
        try {
            $ext  = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $img  = match($ext) {
                'jpg', 'jpeg' => imagecreatefromjpeg($imagePath),
                'png'         => imagecreatefrompng($imagePath),
                'webp'        => imagecreatefromwebp($imagePath),
                default       => imagecreatefromjpeg($imagePath),
            };

            if (!$img) return $imagePath;

            $w = imagesx($img);
            $h = imagesy($img);

            // Scale up 2x for better OCR
            $scaled = imagecreatetruecolor($w * 2, $h * 2);
            imagecopyresampled($scaled, $img, 0, 0, 0, 0, $w * 2, $h * 2, $w, $h);
            imagedestroy($img);

            // Convert to grayscale
            imagefilter($scaled, IMG_FILTER_GRAYSCALE);
            // Increase contrast
            imagefilter($scaled, IMG_FILTER_CONTRAST, -30);

            $tmpPath = sys_get_temp_dir() . '/ocr_' . uniqid() . '.png';
            imagepng($scaled, $tmpPath);
            imagedestroy($scaled);

            return $tmpPath;
        } catch (\Exception $e) {
            Log::warning("Image preprocessing failed: " . $e->getMessage());
            return $imagePath;
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
            'urdu'   => 'تمام سوالات اور جوابات اردو زبان میں لکھیں۔ (Write ALL questions and answers in Urdu)',
            'arabic' => 'اكتب جميع الأسئلة والإجابات باللغة العربية. (Write ALL questions and answers in Arabic)',
            default  => 'Write ALL questions and answers in English.',
        };

        $types = [];
        if ($mcqCount > 0)   $types[] = "- Exactly {$mcqCount} MCQ questions (4 options: a, b, c, d — one correct)";
        if ($shortCount > 0) $types[] = "- Exactly {$shortCount} Short answer questions (2-3 lines)";
        if ($longCount > 0)  $types[] = "- Exactly {$longCount} Long answer questions (detailed paragraph)";
        $typesList = implode("\n", $types);
        $total = $mcqCount + $shortCount + $longCount;

        return "You are creating exam questions for {$className} students, subject: {$subject}, difficulty: {$difficulty}.\n"
            . "{$langNote}\n\n"
            . "STRICT RULES:\n"
            . "1. Generate ONLY from the provided text — do NOT add outside knowledge\n"
            . "2. Every question MUST be directly answerable from the given text only\n"
            . "3. If text is insufficient for {$total} questions, repeat topics differently\n"
            . "4. Generate EXACTLY {$total} questions:\n{$typesList}\n"
            . "5. Return ONLY a JSON array — no explanation, no markdown\n"
            . "6. IMPORTANT: All text values must be valid JSON strings (escape special chars)\n\n"
            . "TEXT:\n---\n{$text}\n---\n\n"
            . "Return ONLY this JSON structure:\n"
            . '[{"type":"mcq","question_text":"Q?","option_a":"A","option_b":"B","option_c":"C","option_d":"D","correct_answer":"a","answer_hint":"From text: ..."},'
            . '{"type":"short","question_text":"Q?","option_a":null,"option_b":null,"option_c":null,"option_d":null,"correct_answer":null,"answer_hint":"From text: ..."},'
            . '{"type":"long","question_text":"Q?","option_a":null,"option_b":null,"option_c":null,"option_d":null,"correct_answer":null,"answer_hint":"From text: ..."}]';
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

        // Find [ ... ] - handle multiline and RTL text
        $start = strpos($clean, '[');
        $end   = strrpos($clean, ']');
        if ($start !== false && $end !== false && $end > $start) {
            $jsonStr = substr($clean, $start, $end - $start + 1);
            $data    = json_decode($jsonStr, true);
            if (is_array($data) && !empty($data)) {
                return $this->normalizeQuestions($data);
            }

            // Fix trailing commas
            $fixed = preg_replace('/,\s*([\]}])/m', '$1', $jsonStr);
            $data  = json_decode($fixed, true);
            if (is_array($data) && !empty($data)) {
                return $this->normalizeQuestions($data);
            }
        }

        // Last resort: extract individual JSON objects (handles broken Urdu/Arabic JSON)
        $objects = $this->extractJsonObjects($clean);
        if (!empty($objects)) {
            return $this->normalizeQuestions($objects);
        }

        Log::warning('parseQuestions failed. Error: ' . json_last_error_msg() . ' Raw: ' . substr($raw, 0, 200));
        return [];
    }

    private function extractJsonObjects(string $text): array
    {
        $objects = [];
        $depth   = 0;
        $inStr   = false;
        $escape  = false;
        $start   = null;

        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');

            if ($escape) { $escape = false; continue; }
            if ($char === '\\' && $inStr) { $escape = true; continue; }
            if ($char === '"') { $inStr = !$inStr; continue; }
            if ($inStr) continue;

            if ($char === '{') {
                if ($depth === 0) $start = $i;
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0 && $start !== null) {
                    $objStr = mb_substr($text, $start, $i - $start + 1, 'UTF-8');
                    $obj    = json_decode($objStr, true);
                    if (is_array($obj) && isset($obj['question_text'])) {
                        $objects[] = $obj;
                    }
                    $start = null;
                }
            }
        }

        return $objects;
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
